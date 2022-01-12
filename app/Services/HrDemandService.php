<?php

namespace App\Services;

use App\Facade\ServiceToServiceCall;
use App\Models\BaseModel;
use App\Models\HrDemand;
use App\Models\HrDemandInstitute;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
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
            'organizations.title',
            'organizations.title_en',
            'hr_demands.end_date',
            'hr_demands.skill_id',
            'hr_demands.vacancy'
        ])->acl();

        $hrDemandBuilder->join('organizations', function ($join) use ($rowStatus) {
            $join->on('organizations.id', '=', 'hr_demands.organization_id')
                ->whereNull('organizations.deleted_at');
        });

        if(!empty($skillIds)){
            $hrDemandBuilder->whereIn('hr_demands.skill_id', $skillIds);
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
     * @return HrDemandInstitute
     */
    public function getOneHrDemand(int $id): HrDemandInstitute
    {
        /** @var HrDemand|Builder $hrDemandBuilder */
        $hrDemandBuilder = HrDemand::select([
            'hr_demands.id',
            'hr_demands.industry_association_id',
            'hr_demands.organization_id',
            'organizations.title',
            'organizations.title_en',
            'hr_demands.end_date',
            'hr_demands.skill_id',
            'hr_demands.vacancy'
        ]);

        $hrDemandBuilder->join('organizations', function ($join) {
            $join->on('organizations.id', '=', 'hr_demands.organization_id')
                ->whereNull('organizations.deleted_at');
        });

        $hrDemandBuilder->where('hr_demand_institutes.id', $id);

        return $hrDemandBuilder->firstOrFail();
    }

    /**
     * @param array $data
     * @return array
     */
    public function store(array $data): array
    {
        $createdHrDemands = [];
        foreach ($data['hr_demands'] as $hrDemand){
            $payload = [
                'industry_association_id' => $data['industry_association_id'],
                'organization_id' => $data['organization_id'],
                'requirement' => $hrDemand['requirement'],
                'end_date' => $hrDemand['end_date'],
                'skill_id' => $hrDemand['skill_id'],
                'requirement_en' => $hrDemand['requirement_en'] ?? "",
                'vacancy' => $hrDemand['vacancy'],
                'remaining_vacancy' => $hrDemand['vacancy'],
                'created_by' => Auth::id(),
                'updated_by' => Auth::id()
            ];
            $hrDemandInstance = new HrDemand();
            $hrDemandInstance->fill($payload);
            $hrDemandInstance->save();

            $createdHrDemands[] = $hrDemandInstance;

            $this->storeHrDemandInstitutes($hrDemand, $hrDemandInstance);
        }
        return $createdHrDemands;
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
    private function storeHrDemandInstitutes(array $data, HrDemand $hrDemand){
        if(!empty($data['institute_ids']) && is_array($data['institute_ids'])){
            foreach ($data['institute_ids'] as $id){
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
     * @param HrDemand $hrDemand
     * @param array $data
     * @return HrDemand
     */
    public function update(HrDemand $hrDemand, array $data): HrDemand
    {
        $payloadForHrDemand = [
            'end_date' => $data['end_date'],
            'skill_id' => $data['skill_id'],
            'requirement' => $data['requirement'],
            'requirement_en' => $data['requirement_en'],
            'vacancy' => $data['vacancy']
        ];
        $hrDemand->fill($payloadForHrDemand);
        $hrDemand->save();

        /** Invalid all previous Hr demand requests fulfilled by Institute */
        if($hrDemand->skill_id != $data['skill_id']){
            $hrDemandInstituteIds = HrDemandInstitute::where('hr_demand_id',$hrDemand->id)->pluck('id');
            foreach ($hrDemandInstituteIds as $id){
                $hrDemandInstitute = HrDemandInstitute::find($id);
                $hrDemandInstitute->row_status = HrDemandInstitute::ROW_STATUS_INVALID;
                $hrDemandInstitute->save();
            }
        }

        return $hrDemand;
    }

    /**
     * @param Request $request
     * @param int|null $id
     * @return \Illuminate\Contracts\Validation\Validator
     */
    public function validator(Request $request, int $id = null): \Illuminate\Contracts\Validation\Validator
    {
        $data = $request->all();
        if(!empty($data['hr_demands']) && is_array($data['hr_demands'])){
            foreach ($data['hr_demands'] as &$hrDemand){
                $hrDemand['institute_ids'] = isset($hrDemand['institute_ids']) && is_array($hrDemand['institute_ids']) ? $hrDemand['institute_ids'] : explode(',', $hrDemand['institute_ids']);
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
            'hr_demands.*.skill_id' => [
                'required',
                'int',
                'exists:skills,id,deleted_at,NULL',
            ],
            'hr_demands.*.end_date' => [
                'required',
                'date',
                'date_format:Y-m-d',
                'after:'.Carbon::now(),
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
                'required',
                'array'
            ],
            'hr_demands.*.institute_ids.*' => [
                'nullable',
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
    public function updateValidator(Request $request, HrDemand $hrDemand, int $id = null): \Illuminate\Contracts\Validation\Validator
    {
        $data = $request->all();
        $customMessage = [
            'row_status.in' => 'Row status must be within 1 or 0. [30000]'
        ];
        $rules = [
            'end_date' => [
                'required',
                'date',
                'date_format:Y-m-d',
                'after:'.Carbon::now(),
            ],
            'skill_id' => [
                'required',
                'int',
                'exists:skills,id,deleted_at,NULL',
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
                    if($data['vacancy'] < $hrDemand->vacancy - $hrDemand->remaining_vacancy){
                        $failed('Vacancy is invalid as already more number of seats are approved by Institutes!');
                    }
                }
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
