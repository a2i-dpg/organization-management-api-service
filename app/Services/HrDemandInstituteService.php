<?php


namespace App\Services;


use App\Facade\ServiceToServiceCall;
use App\Models\BaseModel;
use App\Models\HrDemand;
use App\Models\HrDemandInstitute;
use App\Models\HrDemandYouth;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Ramsey\Collection\Collection;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

class HrDemandInstituteService
{
    /**
     *
     * @param array $request
     * @param Carbon $startTime
     * @return array
     */
    public function getHrDemandInstituteList(array $request, Carbon $startTime): array
    {
        $hrDemandId = $request['hr_demand_id'] ?? "";
        $skillIds = $request['skill_ids'] ?? "";
        $paginate = $request['page'] ?? "";
        $pageSize = $request['page_size'] ?? "";
        $rowStatus = $request['row_status'] ?? "";
        $order = $request['order'] ?? "ASC";
        $instituteId = $request['institute_id'] ?? "";

        /** @var Builder $hrDemandBuilder */
        $hrDemandBuilder = HrDemandInstitute::select([
            'hr_demand_institutes.id',
            'hr_demand_institutes.hr_demand_id',
            'hr_demand_institutes.institute_id',
            'hr_demands.organization_id',
            'organizations.title as organization_title',
            'organizations.title_en as organization_title_en',
            'hr_demand_institutes.rejected_by_institute',
            'hr_demand_institutes.vacancy_provided_by_institute',
            'hr_demand_institutes.rejected_by_industry_association',
            'hr_demand_institutes.vacancy_approved_by_industry_association',
            'hr_demand_institutes.row_status'
        ]);

        $hrDemandBuilder->join('hr_demands', function ($join) {
            $join->on('hr_demands.id', '=', 'hr_demand_institutes.hr_demand_id')
                ->whereNull('hr_demands.deleted_at');
        });

        $hrDemandBuilder->join('organizations', function ($join) {
            $join->on('organizations.id', '=', 'hr_demands.organization_id')
                ->whereNull('organizations.deleted_at');
        });

        if(!empty($skillIds)){
            $hrDemandBuilder->join('hr_demand_skills', function ($join) {
                $join->on('hr_demand_skills.hr_demand_id', '=', 'hr_demands.id')
                    ->whereNull('organizations.deleted_at');
            });
            $hrDemandBuilder->whereIn('hr_demand_skills.skill_id', $skillIds);
            $hrDemandBuilder->groupBy('hr_demands.id');
        }

        if (!empty($hrDemandId)) {
            $hrDemandBuilder->where('hr_demand_institutes.hr_demand_id', $hrDemandId);
        }
        if (!empty($instituteId)) {
            $hrDemandBuilder->where(function ($builder) use ($instituteId) {
                $builder->orWhere('hr_demand_institutes.institute_id', $instituteId);
                $builder->orWhereNull('hr_demand_institutes.institute_id');
            });
        }

        $hrDemandBuilder->orderBy('hr_demand_institutes.id', $order);
        if (is_numeric($rowStatus)) {
            $hrDemandBuilder->where('hr_demand_institutes.row_status', $rowStatus);
        }

        $hrDemandBuilder->with('hrDemand');

        /** @var Collection $hrDemandInstitutes */
        if (is_numeric($paginate) || is_numeric($pageSize)) {
            $pageSize = $pageSize ?: BaseModel::DEFAULT_PAGE_SIZE;
            $hrDemandInstitutes = $hrDemandBuilder->paginate($pageSize);
            $paginateData = (object)$hrDemandInstitutes->toArray();
            $response['current_page'] = $paginateData->current_page;
            $response['total_page'] = $paginateData->last_page;
            $response['page_size'] = $paginateData->per_page;
            $response['total'] = $paginateData->total;
        } else {
            $hrDemandInstitutes = $hrDemandBuilder->get();
        }

        /** Delete null institute_id row if at least one row for that institute is exist */
        $idsToRemove = [];
        $authUser = Auth::user();
        $hrDemandInstituteGroupByHrDemandIds = $hrDemandInstitutes->groupBy('hr_demand_id');
        foreach ($hrDemandInstituteGroupByHrDemandIds as $hrDemandInstituteGroup) {
            $hrDemandNullRowId = null;
            foreach ($hrDemandInstituteGroup as $hrDemandInstitute) {
                if (empty($hrDemandInstitute->institute_id)) {
                    $hrDemandNullRowId = $hrDemandInstitute->id;
                    break;
                }
            }
            if ($hrDemandNullRowId) {
                if ($authUser->user_type == BaseModel::INDUSTRY_ASSOCIATION_USER_TYPE) {
                    $idsToRemove[] = $hrDemandNullRowId;
                } else {
                    foreach ($hrDemandInstituteGroup as $hrDemandInstitute) {
                        if ($hrDemandInstitute->institute_id) {
                            $idsToRemove[] = $hrDemandNullRowId;
                        }
                    }
                }
            }
        }
        foreach ($idsToRemove as $idToRemove) {
            foreach ($hrDemandInstitutes as $key => $hrDemandInstitute) {
                if ($idToRemove == $hrDemandInstitute->id) {
                    unset($hrDemandInstitutes[$key]);
                    break;
                }
            }
        }

        /** Fetch & add Institute titles from Institute Service */
        $instituteIds = $hrDemandInstitutes->pluck('institute_id')->unique()->toArray();
        $titleByInstituteIds = ServiceToServiceCall::getInstituteTitleByIds($instituteIds);
        foreach ($hrDemandInstitutes as $hrDemandInstitute) {
            if (!empty($hrDemandInstitute['institute_id']) && !empty($titleByInstituteIds[$hrDemandInstitute['institute_id']])) {
                $hrDemandInstitute['institute_title'] = $titleByInstituteIds[$hrDemandInstitute['institute_id']]['title'];
                $hrDemandInstitute['institute_title_en'] = $titleByInstituteIds[$hrDemandInstitute['institute_id']]['title_en'];
            }
        }

        $finalData = $hrDemandInstitutes->toArray()['data'] ?? $hrDemandInstitutes->toArray();

        $response['order'] = $order;
        $response['data'] = array_values($finalData);
        $response['_response_status'] = [
            "success" => true,
            "code" => Response::HTTP_OK,
            "query_time" => $startTime->diffInSeconds(Carbon::now()),
        ];

        return $response;
    }

    /**
     * @param int $id
     * @return HrDemandInstitute
     */
    public function getOneHrDemandInstitute(int $id): HrDemandInstitute
    {
        /** @var HrDemandInstitute|Builder $hrDemandBuilder */
        $hrDemandBuilder = HrDemandInstitute::select([
            'hr_demand_institutes.id',
            'hr_demand_institutes.hr_demand_id',
            'hr_demand_institutes.institute_id',
            'hr_demands.organization_id',
            'organizations.title as organization_title',
            'organizations.title_en as organization_title_en',
            'hr_demand_institutes.rejected_by_institute',
            'hr_demand_institutes.vacancy_provided_by_institute',
            'hr_demand_institutes.rejected_by_industry_association',
            'hr_demand_institutes.vacancy_approved_by_industry_association',
            'hr_demand_institutes.row_status'
        ]);

        $hrDemandBuilder->where('hr_demand_institutes.id', $id);

        $hrDemandBuilder->join('hr_demands', function ($join) {
            $join->on('hr_demands.id', '=', 'hr_demand_institutes.hr_demand_id')
                ->whereNull('hr_demands.deleted_at');
        });

        $hrDemandBuilder->join('organizations', function ($join) {
            $join->on('organizations.id', '=', 'hr_demands.organization_id')
                ->whereNull('organizations.deleted_at');
        });

        $hrDemandBuilder->with('hrDemand');

        $hrDemandInstitute = $hrDemandBuilder->firstOrFail();

        /** Fetch & add Institute titles from Institute Service */
        $instituteIds = [];
        if (!empty($hrDemandInstitute->institute_id)) {
            $instituteIds[] = $hrDemandInstitute->institute_id;
            $titleByInstituteIds = ServiceToServiceCall::getInstituteTitleByIds($instituteIds);
            if (!empty($hrDemandInstitute['institute_id']) && !empty($titleByInstituteIds[$hrDemandInstitute['institute_id']])) {
                $hrDemandInstitute['institute_title'] = $titleByInstituteIds[$hrDemandInstitute['institute_id']]['title'];
                $hrDemandInstitute['institute_title_en'] = $titleByInstituteIds[$hrDemandInstitute['institute_id']]['title_en'];
            }
        }

        /** Fetch All Hr Demand Youths */
        /** @var HrDemandYouth|Builder $hrDemandYouths */
        $hrDemandYouths = HrDemandYouth::where('hr_demand_institute_id', $hrDemandInstitute->id)->get();

        /** Set Youth Details if youth_id exist in HrDemandYouth */
        $youthIds = $hrDemandYouths->pluck('youth_id')->filter(function ($value) {
            return !is_null($value);
        })->toArray();
        if ($youthIds) {
            $youthProfiles = ServiceToServiceCall::getYouthProfilesByIds($youthIds);
            $indexedYouths = [];
            foreach ($youthProfiles as $item) {
                $indexedYouths[$item['id']] = $item;
            }

            foreach ($hrDemandYouths as $hrDemandYouth) {
                if (!empty($hrDemandYouth->youth_id)) {
                    $hrDemandYouth['youth_details'] = $indexedYouths[$hrDemandYouth['youth_id']] ?? "";
                }
            }
        }

        $hrDemandYouthCvLinks = [];
        $hrDemandYouthsYouthIds = [];
        foreach ($hrDemandYouths as $hrDemandYouth) {
            if ($hrDemandYouth['cv_link']) {
                $hrDemandYouthCvLinks[] = $hrDemandYouth;
            } else if ($hrDemandYouth['youth_id']) {
                $hrDemandYouthsYouthIds[] = $hrDemandYouth;
            }
        }

        $hrDemandInstitute['hr_demand_youths_cv_links'] = $hrDemandYouthCvLinks;
        $hrDemandInstitute['hr_demand_youths_youth_ids'] = $hrDemandYouthsYouthIds;

        return $hrDemandInstitute;
    }

    /**
     * @param HrDemandInstitute $hrDemandInstitute
     * @param array $data
     * @return HrDemandInstitute
     * @throws Throwable
     */
    public function hrDemandApprovedByInstitute(HrDemandInstitute $hrDemandInstitute, array $data): HrDemandInstitute
    {
        $updatedHrDemandInstitute = $this->updateHrDemandInstituteByInstituteUser($hrDemandInstitute, $data);

        $this->storeOrUpdateYouthAndCvLinkForHrDemand($updatedHrDemandInstitute, $data);

        return $updatedHrDemandInstitute;
    }

    /**
     * @param HrDemandInstitute $hrDemandInstitute
     * @return HrDemandInstitute
     * @throws Throwable
     */
    public function hrDemandRejectedByInstitute(HrDemandInstitute $hrDemandInstitute): HrDemandInstitute
    {
        $updatedHrDemandInstitute = $this->updateHrDemandInstituteByInstituteUser($hrDemandInstitute);

        $this->invalidAllHrDemandYouths($hrDemandInstitute);

        return $updatedHrDemandInstitute;
    }

    /**
     * @param HrDemandInstitute $hrDemandInstitute
     * @param array $data
     * @return HrDemandInstitute
     * @throws Throwable
     */
    public function updateHrDemandInstituteByInstituteUser(HrDemandInstitute $hrDemandInstitute, array $data = []): HrDemandInstitute
    {
        $authUser = Auth::user();
        $updatedHrDemandInstitute = $hrDemandInstitute;

        /**
         * If, row not exist for the institute then create new row for the institute
         * Else, update that row
         */
        if (empty($hrDemandInstitute->institute_id)) {
            $updatedHrDemandInstitute = new HrDemandInstitute();
            $updatedHrDemandInstitute->hr_demand_id = $hrDemandInstitute->hr_demand_id;

            $this->fillDataToCreateOrUpdateHrDemandInstitute($data, $updatedHrDemandInstitute);

            $updatedHrDemandInstitute->institute_id = $authUser->institute_id;
        } else {
            /** Check weather row is for logged_in institute user OR not */
            throw_if($authUser->institute_id != $hrDemandInstitute->institute_id, ValidationException::withMessages([
                "Unauthorized Action!"
            ]));

            $this->fillDataToCreateOrUpdateHrDemandInstitute($data, $updatedHrDemandInstitute);
        }
        $updatedHrDemandInstitute->save();

        return $updatedHrDemandInstitute;
    }

    /**
     * @param array $data
     * @param HrDemandInstitute $updatedHrDemandInstitute
     */
    private function fillDataToCreateOrUpdateHrDemandInstitute(array $data, HrDemandInstitute $updatedHrDemandInstitute): void
    {
        /**
         * If, send "cv_links" OR "youth_ids" as query parameter means APPROVAL
         * Else, means REJECTION
         */
        if (!empty($data) && (!empty($data['cv_links']) || !empty($data['youth_ids']))) {
            $cv_links = !empty($data['cv_links']) ? count($data['cv_links']) : 0;
            $youth_ids = !empty($data['youth_ids']) ? count($data['youth_ids']) : 0;

            $updatedHrDemandInstitute->rejected_by_institute = HrDemandInstitute::REJECTED_BY_INSTITUTE_FALSE;
            $updatedHrDemandInstitute->vacancy_provided_by_institute = $cv_links + $youth_ids;
        } else {
            $updatedHrDemandInstitute->rejected_by_institute = HrDemandInstitute::REJECTED_BY_INSTITUTE_TRUE;
        }
    }

    /**
     * @param HrDemandInstitute $hrDemandInstitute
     * @param array $data
     * @return void
     */
    public function storeOrUpdateYouthAndCvLinkForHrDemand(HrDemandInstitute $hrDemandInstitute, array $data)
    {
        $hrDemandYouths = HrDemandYouth::where('hr_demand_institute_id', $hrDemandInstitute->id)->get();
        $hrDemandYouthCvLinks = $hrDemandYouths->pluck('cv_link')->toArray();
        $hrDemandYouthYouthIds = $hrDemandYouths->pluck('youth_id')->toArray();

        /** If previously stored cv_link OR youth_id is missing in given cv_links OR youth_ids, then INVALIDATED those previous rows */
        foreach ($hrDemandYouths as $hrDemandYouth) {
            if (!empty($data['cv_links']) && !empty($hrDemandYouth->cv_link)) {
                if (!in_array($hrDemandYouth->cv_link, $data['cv_links'])) {
                    $hrDemandYouth->row_status = HrDemandYouth::ROW_STATUS_INVALID;
                    $hrDemandYouth->save();
                }
            }
            if (!empty($data['youth_ids']) && !empty($hrDemandYouth->youth_id)) {
                if (!in_array($hrDemandYouth->youth_id, $data['youth_ids'])) {
                    $hrDemandYouth->row_status = HrDemandYouth::ROW_STATUS_INVALID;
                    $hrDemandYouth->save();
                }
            }
        }

        /** If given cv_link is previously not stored, then create new row for given cv_link */
        if (!empty($data['cv_links'])) {
            foreach ($data['cv_links'] as $link) {
                if (!in_array($link, $hrDemandYouthCvLinks)) {
                    $hrDemandYouth = new HrDemandYouth();
                    $hrDemandYouth->fill([
                        'hr_demand_id' => $hrDemandInstitute->hr_demand_id,
                        'hr_demand_institute_id' => $hrDemandInstitute->id,
                        'cv_link' => $link,
                        'approval_status' => HrDemandYouth::APPROVAL_STATUS_PENDING
                    ]);
                    $hrDemandYouth->save();
                }
            }
        }

        /** If given youth_id is previously not stored, then create new row for given youth_id */
        if (!empty($data['youth_ids'])) {
            foreach ($data['youth_ids'] as $youthId) {
                if (!in_array($youthId, $hrDemandYouthYouthIds)) {
                    $hrDemandYouth = new HrDemandYouth();
                    $hrDemandYouth->fill([
                        'hr_demand_id' => $hrDemandInstitute->hr_demand_id,
                        'hr_demand_institute_id' => $hrDemandInstitute->id,
                        'youth_id' => $youthId,
                        'approval_status' => HrDemandYouth::APPROVAL_STATUS_PENDING
                    ]);
                    $hrDemandYouth->save();
                }
            }
        }
    }

    /**
     * @param HrDemandInstitute $hrDemandInstitute
     * @return void
     */
    private function invalidAllHrDemandYouths(HrDemandInstitute $hrDemandInstitute): void
    {
        $hrDemandYouths = HrDemandYouth::where('hr_demand_institute_id', $hrDemandInstitute->id)->get();
        foreach ($hrDemandYouths as $hrDemandYouth) {
            $hrDemandYouth->row_status = HrDemandYouth::ROW_STATUS_INVALID;
            $hrDemandYouth->save();
        }
    }

    /**
     * @param HrDemandInstitute $hrDemandInstitute
     * @param array $data
     * @return HrDemandInstitute
     */
    public function hrDemandApprovedByIndustryAssociation(HrDemandInstitute $hrDemandInstitute, array $data): HrDemandInstitute
    {
        $hrDemand = HrDemand::find($hrDemandInstitute->hr_demand_id);

        /**
         * When Industry Association user want to update its previous approval, then first
         * find difference between previous Approval vacancy and currently given Approval vacancy by Industry Association.
         */
        $approvedVacancyDifference = $hrDemandInstitute->vacancy_approved_by_industry_association - count($data['hr_demand_youth_ids']);
        $hrDemand->remaining_vacancy = $hrDemand->remaining_vacancy + $approvedVacancyDifference;
        $hrDemand->save();

        /** Approve Hr demand Youths */
        $previouslyApprovedHrDemandYouthIds = HrDemandYouth::where('hr_demand_institute_id', $hrDemandInstitute->id)
            ->where('approval_status', HrDemandYouth::APPROVAL_STATUS_APPROVED)
            ->pluck('id')
            ->toArray();

        /** If previously stored Hr Demand Youth is not present  */
        foreach ($previouslyApprovedHrDemandYouthIds as $hrDemandYouthId) {
            if (!in_array($hrDemandYouthId, $data['hr_demand_youth_ids'])) {
                $hrDemandYouth = HrDemandYouth::find($hrDemandYouthId);
                $hrDemandYouth->approval_status = HrDemandYouth::APPROVAL_STATUS_PENDING;
                $hrDemandYouth->save();
            }
        }

        foreach ($data['hr_demand_youth_ids'] as $hrDemandYouthId) {
            $hrDemandYouth = HrDemandYouth::find($hrDemandYouthId);
            $hrDemandYouth->approval_status = HrDemandYouth::APPROVAL_STATUS_APPROVED;
            $hrDemandYouth->save();
        }

        $hrDemandInstitute->rejected_by_industry_association = HrDemandInstitute::REJECTED_BY_INDUSTRY_ASSOCIATION_FALSE;
        $hrDemandInstitute->vacancy_approved_by_industry_association = count($data['hr_demand_youth_ids']);
        $hrDemandInstitute->save();

        return $hrDemandInstitute;
    }

    /**
     * @param HrDemandInstitute $hrDemandInstitute
     * @return HrDemandInstitute
     */
    public function hrDemandRejectedByIndustryAssociation(HrDemandInstitute $hrDemandInstitute): HrDemandInstitute
    {
        $hrDemand = HrDemand::find($hrDemandInstitute->hr_demand_id);
        $hrDemand->remaining_vacancy += $hrDemandInstitute->vacancy_approved_by_industry_association;
        $hrDemand->save();

        $hrDemandInstitute->rejected_by_industry_association = HrDemandInstitute::REJECTED_BY_INDUSTRY_ASSOCIATION_TRUE;
        $hrDemandInstitute->vacancy_approved_by_industry_association = 0;
        $hrDemandInstitute->save();

        /** Reject all Hr demand youths */
        $hrDemandYouths = HrDemandYouth::where('hr_demand_institute_id', $hrDemandInstitute->id)->get();
        foreach ($hrDemandYouths as $hrDemandYouth) {
            $hrDemandYouth->approval_status = HrDemandYouth::APPROVAL_STATUS_REJECTED;
            $hrDemandYouth->save();
        }

        return $hrDemandInstitute;
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

        return Validator::make($requestData, [
            'hr_demand_id' => [
                'nullable',
                'int'
            ],
            'skill_ids' => [
                'nullable',
                'array',
                'min:1'
            ],
            'skill_ids.*' => [
                'required',
                'int'
            ],
            'institute_id' => [
                'nullable',
                'int'
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
     * @param HrDemandInstitute $hrDemandInstitute
     * @return \Illuminate\Contracts\Validation\Validator
     * @throws Throwable
     */
    public function hrDemandApprovedByInstituteValidator(Request $request, HrDemandInstitute $hrDemandInstitute): \Illuminate\Contracts\Validation\Validator
    {
        $data = $request->all();
        $hrDemand = HrDemand::find($hrDemandInstitute->hr_demand_id);

        if (!empty($data['cv_links'])) {
            $data['cv_links'] = isset($data['cv_links']) && is_array($data['cv_links']) ? $data['cv_links'] : explode(',', $data['cv_links']);
        }
        if (!empty($data['youth_ids'])) {
            $data['youth_ids'] = isset($data['youth_ids']) && is_array($data['youth_ids']) ? $data['youth_ids'] : explode(',', $data['youth_ids']);
        }

        $cv_links = !empty($data['cv_links']) && is_array($data['cv_links']) ? count($data['cv_links']) : 0;
        $youth_ids = !empty($data['youth_ids']) && is_array($data['youth_ids']) ? count($data['youth_ids']) : 0;

        throw_if(empty($data['cv_links']) && empty($data['youth_ids']), ValidationException::withMessages([
            "Both cv_links & youth_ids can't be missing!"
        ]));

        throw_if($hrDemand->end_date < Carbon::now(), ValidationException::withMessages([
            "Deadline exceed.[66200]"
        ]));

        throw_if($hrDemandInstitute->rejected_by_industry_association == HrDemandInstitute::REJECTED_BY_INDUSTRY_ASSOCIATION_TRUE, ValidationException::withMessages([
            "Already rejected by Industry Association.[66500]"
        ]));

        throw_if($hrDemandInstitute->vacancy_approved_by_industry_association > $cv_links + $youth_ids, ValidationException::withMessages([
            "Industry Association already approved more vacancy than the given vacancy.[66400]"
        ]));

        throw_if($hrDemandInstitute->row_status == HrDemandInstitute::ROW_STATUS_INVALID, ValidationException::withMessages([
            "Hr Demand Institute already Invalidated!"
        ]));

        /** Validate that already approved Hr Demand Youth can't be missing in given cv_links OR youth_ids */
        $hrDemandYouths = HrDemandYouth::where('hr_demand_institute_id', $hrDemandInstitute->id)->get();
        foreach ($hrDemandYouths as $hrDemandYouth) {
            if (!empty($data['cv_links']) && is_array($data['cv_links'])) {
                throw_if(!empty($hrDemandYouth->cv_link) && $hrDemandYouth->approval_status == HrDemandYouth::APPROVAL_STATUS_APPROVED && !in_array($hrDemandYouth->cv_link, $data['cv_links']),
                    ValidationException::withMessages([
                        "CV link: " . $hrDemandYouth->cv_link . " already approved by Industry Association User!"
                    ]));
            }
            if (!empty($data['youth_ids']) && is_array($data['youth_ids'])) {
                throw_if(!empty($hrDemandYouth->youth_id) && $hrDemandYouth->approval_status == HrDemandYouth::APPROVAL_STATUS_APPROVED && !in_array($hrDemandYouth->youth_id, $data['youth_ids']),
                    ValidationException::withMessages([
                        "Youth id: " . $hrDemandYouth->youth_id . " already approved by Industry Association User!"
                    ]));
            }
        }

        $rules = [
            'cv_links' => [
                'nullable',
                'array',
                'min:1'
            ],
            'cv_links.*' => [
                'required',
                'string',
                'distinct'
            ],
            'youth_ids' => [
                'nullable',
                'array',
                'min:1'
            ],
            'youth_ids.*' => [
                'required',
                'int',
                'distinct'
            ]
        ];
        return Validator::make($data, $rules);
    }

    /**
     * @param Request $request
     * @param HrDemandInstitute $hrDemandInstitute
     * @return \Illuminate\Contracts\Validation\Validator
     */
    public function hrDemandApprovedByIndustryAssociationValidator(Request $request, HrDemandInstitute $hrDemandInstitute): \Illuminate\Contracts\Validation\Validator
    {
        $data = $request->all();

        if (!empty($data['hr_demand_youth_ids'])) {
            $data['hr_demand_youth_ids'] = isset($data['hr_demand_youth_ids']) && is_array($data['hr_demand_youth_ids']) ? $data['hr_demand_youth_ids'] : explode(',', $data['hr_demand_youth_ids']);
        }

        $rules = [
            'hr_demand_youth_ids' => [
                'required',
                'array',
                'min:1',
                function ($attr, $value, $failed) use ($hrDemandInstitute) {
                    $hrDemand = HrDemand::find($hrDemandInstitute->hr_demand_id);

                    /**
                     * When Industry Association user want to update its previous approval, then first
                     * find difference between previous Approval vacancy and currently given Approval vacancy by Industry Association.
                     */
                    $approvedVacancyDifference = $hrDemandInstitute->vacancy_approved_by_industry_association - count($value);

                    $updatedRemainingVacancy = $hrDemand->remaining_vacancy + $approvedVacancyDifference;

                    if ($updatedRemainingVacancy < 0) {
                        $failed("Approved more number of vacancy than the available vacancy!");
                    }

                    if (count($value) > $hrDemandInstitute->vacancy_provided_by_institute) {
                        $failed("Vacancy provided by institute exceed.[66100]");
                    }

                    if ($hrDemandInstitute->vacancy_provided_by_institute == 0) {
                        $failed("Not yet fulfilled by Institute!");
                    }

                    if ($hrDemandInstitute->rejected_by_institute == HrDemandInstitute::REJECTED_BY_INSTITUTE_TRUE) {
                        $failed("Already rejected by Institute!");
                    }

                    if ($hrDemandInstitute->row_status == HrDemandInstitute::ROW_STATUS_INVALID) {
                        $failed("Hr Demand Institute already Invalidated!");
                    }
                }
            ],
            'hr_demand_youth_ids.*' => [
                'required',
                'int',
                'distinct',
                'exists:hr_demand_youths,id,deleted_at,NULL',
                function ($attr, $value, $failed) use ($hrDemandInstitute) {
                    $hrDemandYouth = HrDemandYouth::findOrFail($value);
                    if ($hrDemandYouth->hr_demand_institute_id != $hrDemandInstitute->id) {
                        $failed('HrDemandYouth Id = ' . $value . ' not belongs to HrDemandInstitute Id = ' . $hrDemandInstitute->id);
                    }
                    if ($hrDemandYouth->hr_demand_institute_id == $hrDemandInstitute->id &&
                        $hrDemandYouth->row_status == HrDemandYouth::ROW_STATUS_INVALID) {
                        $failed('HrDemandYouth Id = ' . $value . ' already invalidated by Institute!');
                    }
                }
            ]
        ];
        return Validator::make($data, $rules);
    }
}
