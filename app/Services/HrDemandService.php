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
            'hr_demand_institutes.institute_id',
            'hr_demands.end_date',
            'hr_demands.skill_id',
            'hr_demands.vacancy',
        ])->acl();

        $hrDemandBuilder->join('hr_demand_institutes', function ($join) use ($rowStatus) {
            $join->on('hr_demand_institutes.hr_demand_id', '=', 'hr_demands.id')
                ->whereNull('hr_demand_institutes.deleted_at');
        });
        $hrDemandBuilder->join('organizations', function ($join) use ($rowStatus) {
            $join->on('organizations.id', '=', 'hr_demands.organization_id')
                ->whereNull('organizations.deleted_at');
        });

        if(!empty($skillIds)){
            $hrDemandBuilder->whereIn('skill_id', $skillIds);
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

        $instituteIds = $hrDemands->pluck('institute_id')->unique();
        $titleByInstituteIds = ServiceToServiceCall::getInstituteTitleByIds($instituteIds);
        foreach ($hrDemands as $hrDemand){
            $hrDemand['institute_title'] = $titleByInstituteIds[''];
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
     * @param array $data
     * @return HrDemand
     */
    public function store(array $data): HrDemand
    {
        $hrDemand = new HrDemand();
        $hrDemand->fill($data);
        $hrDemand->save();


        $this->storeHrDemandInstitutes($data, $hrDemand);
        return $hrDemand;
    }

    /**
     * @param HrDemand $hrDemand
     * @param array $data
     * @return HrDemand
     */
    public function update(HrDemand $hrDemand, array $data): HrDemand
    {
        $hrDemand->fill($data);
        $hrDemand->save();

        $hrDemandInstituteIds = HrDemandInstitute::where('hr_demand_id',$hrDemand->id)->pluck('id');
        foreach ($hrDemandInstituteIds as $id){
            $hrDemandInstitute = HrDemandInstitute::find($id);
            $hrDemandInstitute->delete();
        }

        $this->storeHrDemandInstitutes($data, $hrDemand);
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
     * @param Request $request
     * @param int|null $id
     * @return \Illuminate\Contracts\Validation\Validator
     */
    public function validator(Request $request, int $id = null): \Illuminate\Contracts\Validation\Validator
    {
        $data = $request->all();
        if (!empty($data['institute_ids'])) {
            $data["institute_ids"] = isset($data['institute_ids']) && is_array($data['institute_ids']) ? $data['institute_ids'] : explode(',', $data['institute_ids']);
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
            'institute_ids' => [
                'required',
                'array'
            ],
            'institute_ids.*' => [
                'nullable',
                'int',
                'distinct'
            ],
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
     * @param HrDemandInstitute $hrDemandInstitute
     * @param array $data
     * @return HrDemandInstitute
     */
    public function hrDemandApprovedByInstitute(HrDemandInstitute $hrDemandInstitute, array $data): HrDemandInstitute
    {
        $hrDemandInstitute->vacancy_provided_by_institute = $data['vacancy_provided_by_institute'];
        $hrDemandInstitute->save();

        return $hrDemandInstitute;
    }

    /**
     * @param Request $request
     * @param int $hrDemandId
     * @return \Illuminate\Contracts\Validation\Validator
     */
    public function hrDemandApproveByInstituteValidator(Request $request, int $hrDemandId): \Illuminate\Contracts\Validation\Validator
    {
        $data = $request->all();

        $rules = [
            'vacancy_provided_by_institute' => [
                'required',
                'int',
                function ($attr, $value, $failed) use ($hrDemandId) {
                    $hrDemand = HrDemand::find($hrDemandId);

                    if ($value > $hrDemand->vacancy) {
                        $failed("Vacancy exceed");
                    }
                }
            ]
        ];
        return Validator::make($data, $rules);
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
}
