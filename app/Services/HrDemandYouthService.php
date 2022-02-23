<?php

namespace App\Services;

use App\Models\BaseModel;
use App\Models\HrDemand;
use App\Models\HrDemandInstitute;
use App\Models\HrDemandYouth;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Ramsey\Collection\Collection;
use Symfony\Component\HttpFoundation\Response;

class HrDemandYouthService
{

    /**
     *
     * @param array $request
     * @param Carbon $startTime
     * @param int $hrDemandInstituteId
     * @return array
     */
    public function getHrDemandYouthList(array $request, Carbon $startTime, int $hrDemandInstituteId): array
    {
        $paginate = $request['page'] ?? "";
        $pageSize = $request['page_size'] ?? "";
        $rowStatus = $request['row_status'] ?? "";
        $order = $request['order'] ?? "ASC";
        $hrDemandYouthType = $request['hr_demand_youth_type'] ?? "";

        /** @var Builder $hrDemandBuilder */
        $hrDemandBuilder = HrDemandYouth::select([
            'hr_demand_youths.id',
            'hr_demand_youths.hr_demand_id',
            'hr_demand_youths.hr_demand_institute_id',
            'hr_demand_youths.cv_link',
            'hr_demand_youths.youth_id',
            'hr_demand_youths.approval_status',
            'hr_demand_youths.row_status'
        ]);

        if (!empty($hrDemandInstituteId)) {
            $hrDemandBuilder->where('hr_demand_institute_id', $hrDemandInstituteId);
        }

        if (!empty($hrDemandYouthType)) {
            if ($hrDemandYouthType == HrDemandYouth::HR_DEMAND_YOUTH_TYPE_CV_LINK) {
                $hrDemandBuilder->whereNotNull('hr_demand_youths.cv_link');
            } else if ($hrDemandYouthType == HrDemandYouth::HR_DEMAND_YOUTH_TYPE_YOUTH_ID) {
                $hrDemandBuilder->whereNotNull('hr_demand_youths.youth_id');
            }
        }

        $hrDemandBuilder->orderBy('hr_demand_youths.id', $order);
        if (is_numeric($rowStatus)) {
            $hrDemandBuilder->where('hr_demand_youths.row_status', $rowStatus);
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
     *
     * @param HrDemandYouth $hrDemandYouth
     * @return void
     */
    public function deleteHrDemandYouth(HrDemandYouth $hrDemandYouth)
    {
        if ($hrDemandYouth->approval_status == HrDemandYouth::APPROVAL_STATUS_APPROVED) {
            $hrDemandInstitute = HrDemandInstitute::find($hrDemandYouth->hr_demand_institute_id);
            $hrDemandInstitute->vacancy_approved_by_industry_association -= 1;
            $hrDemandInstitute->save();

            $hrDemand = HrDemand::find($hrDemandYouth->hr_demand_id);
            $hrDemand->remaining_vacancy += 1;
            $hrDemand->save();
        }
        $hrDemandYouth->approval_status = HrDemandYouth::APPROVAL_STATUS_REJECTED;
        $hrDemandYouth->save();
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
            'hr_demand_youth_type' => [
                'nullable',
                'int',
                Rule::in(HrDemandYouth::HR_DEMAND_YOUTH_TYPES)
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
                Rule::in([HrDemandYouth::ROW_STATUS_ACTIVE, HrDemandYouth::ROW_STATUS_INACTIVE, HrDemandYouth::ROW_STATUS_INVALID]),
            ],
        ], $customMessage);
    }

    /**
     * @param Request $request
     * @return \Illuminate\Contracts\Validation\Validator
     */
    public function deleteValidator(Request $request): \Illuminate\Contracts\Validation\Validator
    {
        $requestData = $request->all();

        return Validator::make($requestData, [
            'hr_demand_youth_type' => [
                'nullable',
                'int',
                Rule::in(HrDemandYouth::HR_DEMAND_YOUTH_TYPES)
            ]
        ]);
    }
}
