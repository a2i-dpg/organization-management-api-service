<?php

namespace App\Services;

use App\Facade\ServiceToServiceCall;
use App\Models\BaseModel;
use App\Models\HrDemand;
use App\Models\HrDemandInstitute;
use Carbon\Carbon;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Database\Eloquent\Builder;
use Ramsey\Collection\Collection;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class HrDemandService
 * @package App\Services
 */
class HrDemandService
{
    /**
     *
     * @param array $request
     * @param Carbon $startTime
     * @return array
     */
    public function getHrDemandList(array $request, Carbon $startTime): array
    {
        $paginate = $request['page'] ?? "";
        $pageSize = $request['page_size'] ?? "";
        $rowStatus = $request['row_status'] ?? "";
        $order = $request['order'] ?? "ASC";
        $skillIds = $request['skill_ids'] ?? [];

        /** @var Builder $hrDemandBuilder */
        $hrDemandBuilder = HrDemand::select([
            'hr_demands.id',
            'hr_demands.industry_association_id',
            'hr_demands.organization_id',
            'organizations.title as organization_title',
            'organizations.title_en as organization_title_en',
            'hr_demands.end_date',
            'hr_demands.vacancy',
            'hr_demands.remaining_vacancy',
            'hr_demands.all_institutes'
        ])->acl();

        $hrDemandBuilder->join('organizations', function ($join) use ($rowStatus) {
            $join->on('organizations.id', 'hr_demands.organization_id')
                ->whereNull('organizations.deleted_at');
        });

        /**
         * To filter by Skill_ids, first join hr_demand with hr_demand_skills. Then apply filter on hr_demand_skills.
         * As skill_ids query parameter is an array, so we use here mySQL groupBy() on he_demand_id.
         */
        if (!empty($skillIds)) {
            $hrDemandBuilder->join('hr_demand_skills', function ($join) {
                $join->on('hr_demand_skills.hr_demand_id', 'hr_demands.id');
            });
            $hrDemandBuilder->whereIn('hr_demand_skills.skill_id', $skillIds);
            $hrDemandBuilder->groupBy('hr_demands.id');
        }

        $hrDemandBuilder->orderBy('hr_demands.id', $order);
        if (is_numeric($rowStatus)) {
            $hrDemandBuilder->where('hr_demands.row_status', $rowStatus);
        }

        /** @var Collection $hrDemands */
        if (is_numeric($paginate) || is_numeric($pageSize)) {
            $pageSize = $pageSize ?: BaseModel::DEFAULT_PAGE_SIZE;
            $hrDemands = $hrDemandBuilder->paginate($pageSize);
            $paginateData = (object)$hrDemands->toArray();
            $response['current_page'] = $paginateData->current_page;
            $response['total_page'] = $paginateData->last_page;
            $response['page_size'] = $paginateData->per_page;
            $response['total'] = $paginateData->total;
        } else {
            $hrDemands = $hrDemandBuilder->get();
        }

        $response['order'] = $order;
        $response['data'] = $hrDemands->toArray()['data'] ?? $hrDemands->toArray();
        $response['_response_status'] = [
            "success" => true,
            "code" => Response::HTTP_OK,
            "query_time" => $startTime->diffInSeconds(Carbon::now()),
        ];

        return $response;
    }

    /**
     * @param int $id
     * @return HrDemand
     */
    public function getOneHrDemand(int $id): HrDemand
    {
        /** @var HrDemand|Builder $hrDemandBuilder */
        $hrDemandBuilder = HrDemand::select([
            'hr_demands.id',
            'hr_demands.industry_association_id',
            'hr_demands.organization_id',
            'organizations.title as organization_title',
            'organizations.title_en as organization_title_en',
            'hr_demands.end_date',
            'hr_demands.vacancy',
            'hr_demands.remaining_vacancy',
            'hr_demands.all_institutes'
        ]);

        $hrDemandBuilder->join('organizations', function ($join) {
            $join->on('organizations.id', '=', 'hr_demands.organization_id')
                ->whereNull('organizations.deleted_at');
        });

        $hrDemandBuilder->where('hr_demands.id', $id);

        $hrDemandBuilder->with('hrDemandInstitutes');
        $hrDemandBuilder->with('mandatorySkills');
        $hrDemandBuilder->with('optionalSkills');

        $hrDemand = $hrDemandBuilder->firstOrFail();

        if (!empty($hrDemand['hrDemandInstitutes'])) {
            $instituteIds = $hrDemand['hrDemandInstitutes']->pluck('institute_id')->toArray();
            $titleByInstituteIds = ServiceToServiceCall::getInstituteTitleByIds($instituteIds);
            foreach ($hrDemand['hrDemandInstitutes'] as $hrDemandInstitute) {
                $hrDemandInstitute['institute_title'] = $titleByInstituteIds[$hrDemandInstitute['institute_id']]['title'] ?? "";
                $hrDemandInstitute['institute_title_en'] = $titleByInstituteIds[$hrDemandInstitute['institute_id']]['title_en'] ?? "";
            }
        }

        return $hrDemand;
    }

    /**
     * @param array $data
     * @return array
     */
    public function store(array $data): array
    {
        $createdHrDemands = [];
        foreach ($data['hr_demands'] as $hrDemand) {
            $payload = [
                'industry_association_id' => $data['industry_association_id'],
                'organization_id' => $data['organization_id'],
                'requirement' => $hrDemand['requirement'],
                'end_date' => $hrDemand['end_date'],
                'requirement_en' => $hrDemand['requirement_en'] ?? "",
                'vacancy' => $hrDemand['vacancy'],
                'remaining_vacancy' => $hrDemand['vacancy'],
                'all_institutes' => empty($hrDemand['institute_ids']) ? 1 : 0
            ];

            $hrDemandInstance = new HrDemand();
            $hrDemandInstance->fill($payload);
            $hrDemandInstance->save();

            $createdHrDemands[] = $hrDemandInstance;

            $this->syncHrDemandSkills($hrDemand, $hrDemandInstance);
            $this->storeHrDemandInstitutes($hrDemand, $hrDemandInstance);
        }
        return $createdHrDemands;
    }

    /**
     * @param HrDemand $hrDemand
     * @param array $data
     * @return HrDemand
     */
    public function update(HrDemand $hrDemand, array $data): HrDemand
    {
        $payloadForHrDemand = [
            'end_date' => $data['end_date'],
            'requirement' => $data['requirement'],
            'requirement_en' => $data['requirement_en'] ?? "",
            'vacancy' => $data['vacancy'],
            'remaining_vacancy' => $hrDemand->remaining_vacancy,
            'all_institutes' => empty($data['institute_ids']) ? 1 : 0,
        ];
        $invalidatedApprovedVacanciesByIndustryAssociation = 0;

        /** Update hr_demand skills */
        $this->syncHrDemandSkills($data, $hrDemand);

        /** If vacancy changed then do bellow stuff */
        if ($hrDemand->vacancy != $data['vacancy']) {
            $vacancyChanged = $hrDemand->vacancy - $data['vacancy'];
            $payloadForHrDemand['remaining_vacancy'] = $hrDemand->remaining_vacancy - $vacancyChanged;
        }

        /**
         * These are the possible case that may happen,
         * 1) May be previously hr_demand was created with ALL_INSTITUTES and now changed to some institutes
         * 2) May be previously hr_demand was created with some institutes and now changed to some other institutes
         * 3) May be previous & current institute_ids are same (No effect)
         * 4) May be previously hr_demand was created with some institutes and now changed to ALL_INSTITUTES
         */

        /** If ALL INSTITUTE = FALSE */
        if (!empty($data['institute_ids'])) {
            $existHrDemandInstitutes = HrDemandInstitute::where('hr_demand_id', $hrDemand->id)
                ->where('row_status', HrDemandInstitute::ROW_STATUS_ACTIVE)
                ->get();
            $existHrDemandInstituteIds = $existHrDemandInstitutes->pluck('institute_id')->toArray();

            /** If the given institute_ids are not present in existing institutes then create new institutes */
            foreach ($data['institute_ids'] as $instituteId) {
                if (!in_array($instituteId, $existHrDemandInstituteIds)) {
                    $institutePayload = [
                        'hr_demand_id' => $hrDemand->id,
                        'institute_id' => $instituteId
                    ];
                    $newHrDemandInstitute = new HrDemandInstitute();
                    $newHrDemandInstitute->fill($institutePayload);
                    $newHrDemandInstitute->save();
                }
            }

            /** If already existing institute is not present in given institutes then INVALID those existing institutes including all_institute row if exist */
            foreach ($existHrDemandInstitutes as $hrDemandInstitute) {
                if (!in_array($hrDemandInstitute->institute_id, $data['institute_ids'])) {
                    $newHrDemandInstitute = HrDemandInstitute::find($hrDemandInstitute->id);
                    $invalidatedApprovedVacanciesByIndustryAssociation += $hrDemandInstitute->vacancy_approved_by_industry_association;
                    $newHrDemandInstitute->row_status = HrDemandInstitute::ROW_STATUS_INVALID;
                    $newHrDemandInstitute->save();
                }
            }
        } else {
            /**
             * For ALL INSTITUTE = TRUE,
             * Create a row for all_institutes if we had no all_institute row previously for this hr_demand.
             */
            $allInstituteRow = HrDemandInstitute::where('hr_demand_id', $hrDemand->id)
                ->where('row_status', HrDemandInstitute::ROW_STATUS_ACTIVE)
                ->whereNull('institute_id')
                ->first();
            if (empty($allInstituteRow)) {
                $institutePayload = [
                    'hr_demand_id' => $hrDemand->id
                ];
                $newHrDemandInstitute = new HrDemandInstitute();
                $newHrDemandInstitute->fill($institutePayload);
                $newHrDemandInstitute->save();
            }

            /** INVALIDATED such institute rows those were not updated by the institute */
            $invalidHrDemandInstitutes = HrDemandInstitute::where('hr_demand_id', $hrDemand->id)
                ->where('row_status', HrDemandInstitute::ROW_STATUS_ACTIVE)
                ->whereNotNull('institute_id')
                ->where('vacancy_provided_by_institute', 0)
                ->get();
            foreach ($invalidHrDemandInstitutes as $hrDemandInstitute) {
                $hrDemandInstitute->row_status = HrDemandInstitute::ROW_STATUS_INVALID;
                $hrDemandInstitute->save();
            }
        }

        $payloadForHrDemand['remaining_vacancy'] += $invalidatedApprovedVacanciesByIndustryAssociation;
        $hrDemand->fill($payloadForHrDemand);
        $hrDemand->save();

        return $hrDemand;
    }

    /**
     * @param HrDemand $hrDemand
     * @return bool
     */
    public function destroy(HrDemand $hrDemand): bool
    {
        $hrDemand->hrDemandInstitutes()->delete();

        return $hrDemand->delete();
    }

    /**
     * @param array $data
     * @param HrDemand $hrDemand
     * @return void
     */
    private function syncHrDemandSkills(array $data, HrDemand $hrDemand)
    {
        $pivotTableDataToSync = [];

        /** Store Mandatory Skills */
        if (!empty($data['mandatory_skill_ids'])) {
            foreach ($data['mandatory_skill_ids'] as $skillId) {
                $hrDemandSkill = [
                    'skill_type' => HrDemand::HR_DEMAND_SKILL_TYPE_MANDATORY
                ];
                $pivotTableDataToSync += array($skillId => $hrDemandSkill);
            }
        }

        /** Store Optional Skills */
        if (!empty($data['optional_skill_ids'])) {
            foreach ($data['optional_skill_ids'] as $skillId) {
                $hrDemandSkill = [
                    'skill_type' => HrDemand::HR_DEMAND_SKILL_TYPE_OPTIONAL
                ];
                $pivotTableDataToSync += array($skillId => $hrDemandSkill);
            }
        }

        $hrDemand->skills()->sync($pivotTableDataToSync);
    }

    /**
     * @param array $data
     * @param HrDemand $hrDemand
     * @return void
     */
    private function storeHrDemandInstitutes(array $data, HrDemand $hrDemand)
    {
        /**
         * Store institutes that were given in "institute_ids" array
         * */
        if (empty($data['institute_ids'])) {
            $payload = [
                'hr_demand_id' => $hrDemand->id
            ];
            $hrDemandInstitute = new HrDemandInstitute();
            $hrDemandInstitute->fill($payload);
            $hrDemandInstitute->save();
        } else {
            foreach ($data['institute_ids'] as $id) {
                $payload = [
                    'hr_demand_id' => $hrDemand->id,
                    'institute_id' => $id
                ];
                $hrDemandInstitute = new HrDemandInstitute();
                $hrDemandInstitute->fill($payload);
                $hrDemandInstitute->save();
            }
        }
    }

    /**
     * @param array $data
     * @return array
     */
    public function makeProperArrayForHrDemandData(array $data): array
    {
        if (!empty($data['institute_ids'])) {
            $data['institute_ids'] = isset($data['institute_ids']) && is_array($data['institute_ids']) ? $data['institute_ids'] : explode(',', $data['institute_ids']);
        }
        if (!empty($data['mandatory_skill_ids'])) {
            $data['mandatory_skill_ids'] = isset($data['mandatory_skill_ids']) && is_array($data['mandatory_skill_ids']) ? $data['mandatory_skill_ids'] : explode(',', $data['mandatory_skill_ids']);
        }
        if (!empty($data['optional_skill_ids'])) {
            $data['optional_skill_ids'] = isset($data['optional_skill_ids']) && is_array($data['optional_skill_ids']) ? $data['optional_skill_ids'] : explode(',', $data['optional_skill_ids']);
        }
        return $data;
    }

    /**
     * @param Request $request
     * @return \Illuminate\Contracts\Validation\Validator
     */
    public function filterValidator(Request $request): \Illuminate\Contracts\Validation\Validator
    {
        if ($request->filled('order')) {
            $request->offsetSet('order', strtoupper($request->get('order')));
        }

        $customMessage = [
            'order.in' => 'Order must be within ASC or DESC.[30000]',
            'row_status.in' => 'Row status must be within 1 or 0. [30000]'
        ];

        $requestData = $request->all();
        if (!empty($requestData['skill_ids'])) {
            $requestData['skill_ids'] = is_array($requestData['skill_ids']) ? $requestData['skill_ids'] : explode(',', $requestData['skill_ids']);
        }

        return Validator::make($requestData, [
            'skill_ids' => [
                'nullable',
                'array',
                'min:1',
                'max:10'
            ],
            'skill_ids.*' => [
                'required',
                'integer',
                'distinct',
                'min:1'
            ],
            'page_size' => 'nullable|integer|gt:0',
            'order' => [
                'string',
                'nullable',
                Rule::in([BaseModel::ROW_ORDER_ASC, BaseModel::ROW_ORDER_DESC])
            ],
            'row_status' => [
                'nullable',
                "integer",
                Rule::in([HrDemand::ROW_STATUS_ACTIVE, HrDemand::ROW_STATUS_INACTIVE]),
            ],
        ], $customMessage);
    }

    /**
     * @param Request $request
     * @param int|null $id
     * @return \Illuminate\Contracts\Validation\Validator
     */
    public function validator(Request $request, int $id = null): \Illuminate\Contracts\Validation\Validator
    {
        $data = $request->all();
        if (!empty($data['hr_demands']) && is_array($data['hr_demands'])) {
            foreach ($data['hr_demands'] as &$hrDemand) {
                $hrDemand = $this->makeProperArrayForHrDemandData($hrDemand);
            }
        }

        $customMessage = [
            'row_status.in' => 'Row status must be within 1 or 0. [30000]'
        ];
        $rules = [
            'industry_association_id' => [
                'required',
                'int',
                'exists:industry_associations,id,deleted_at,NULL'
            ],
            'organization_id' => [
                'required',
                'int',
                'exists:organizations,id,deleted_at,NULL',
            ],
            'hr_demands' => [
                'required',
                'array',
                'min:1'
            ],
            'hr_demands.*.mandatory_skill_ids' => [
                'required',
                'array',
                'min:1'
            ],
            'hr_demands.*.mandatory_skill_ids.*' => [
                'required',
                'int',
                'exists:skills,id,deleted_at,NULL',
                function ($attr, $value, $failed) use ($data) {
                    $hrDemandArrayCurrentIndex = explode('.', $attr)[1];
                    if (!empty($data['hr_demands']) && !empty($data['hr_demands'][$hrDemandArrayCurrentIndex]['optional_skill_ids'])) {
                        if (in_array($value, $data['hr_demands'][$hrDemandArrayCurrentIndex]['optional_skill_ids'])) {
                            $failed('Mandatory & Optional skill Ids are identical');
                        }
                    }
                }
            ],
            'hr_demands.*.optional_skill_ids' => [
                'nullable',
                'array',
                'min:1'
            ],
            'hr_demands.*.optional_skill_ids.*' => [
                'required',
                'int',
                'exists:skills,id,deleted_at,NULL'
            ],
            'hr_demands.*.end_date' => [
                'required',
                'date',
                'date_format:Y-m-d',
                'after:' . Carbon::now(),
            ],
            'hr_demands.*.requirement' => [
                'required',
                'string'
            ],
            'hr_demands.*.requirement_en' => [
                'nullable',
                'string'
            ],
            'hr_demands.*.vacancy' => [
                'required',
                'int'
            ],
            'hr_demands.*.institute_ids' => [
                'nullable',
                'array',
                'min:1'
            ],
            'hr_demands.*.institute_ids.*' => [
                'required',
                'int'
            ],
            'row_status' => [
                'required_if:' . $id . ',!=,null',
                'nullable',
                Rule::in([HrDemand::ROW_STATUS_ACTIVE, HrDemand::ROW_STATUS_INACTIVE]),
            ]
        ];
        return Validator::make($data, $rules, $customMessage);
    }

    /**
     * @param Request $request
     * @param HrDemand $hrDemand
     * @param int|null $id
     * @return \Illuminate\Contracts\Validation\Validator
     */
    public function updateValidator(Request $request, HrDemand $hrDemand, int $id = null): \Illuminate\Contracts\Validation\Validator
    {
        $data = $request->all();
        $customMessage = [
            'row_status.in' => 'Row status must be within 1 or 0. [30000]'
        ];

        $data = $this->makeProperArrayForHrDemandData($data);

        $rules = [
            'end_date' => [
                'required',
                'date',
                'date_format:Y-m-d',
                'after:' . Carbon::now(),
            ],
            'mandatory_skill_ids' => [
                'required',
                'array',
                'min:1'
            ],
            'mandatory_skill_ids.*' => [
                'required',
                'int',
                'exists:skills,id,deleted_at,NULL',
                function ($attr, $value, $failed) use ($data) {
                    if (!empty($data['optional_skill_ids'])) {
                        if (in_array($value, $data['optional_skill_ids'])) {
                            $failed('Mandatory & Optional skill Ids are identical');
                        }
                    }
                }
            ],
            'optional_skill_ids' => [
                'nullable',
                'array',
                'min:1'
            ],
            'optional_skill_ids.*' => [
                'required',
                'int',
                'exists:skills,id,deleted_at,NULL'
            ],
            'requirement' => [
                'required',
                'string'
            ],
            'requirement_en' => [
                'nullable',
                'string'
            ],
            'vacancy' => [
                'required',
                'int',
                function ($attr, $value, $failed) use ($hrDemand, $data) {
                    if ($data['vacancy'] < $hrDemand->vacancy - $hrDemand->remaining_vacancy) {
                        $failed('Vacancy is invalid as already more number of seats are approved by Institutes!');
                    }
                }
            ],
            'institute_ids' => [
                'nullable',
                'array',
                'min:1'
            ],
            'institute_ids.*' => [
                'required',
                'int'
            ],
            'row_status' => [
                'required_if:' . $id . ',!=,null',
                'nullable',
                Rule::in([HrDemand::ROW_STATUS_ACTIVE, HrDemand::ROW_STATUS_INACTIVE]),
            ]
        ];
        return Validator::make($data, $rules, $customMessage);
    }
}
