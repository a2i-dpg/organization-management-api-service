<?php


namespace App\Services;


use App\Models\BaseModel;
use App\Models\HrDemand;
use App\Models\HrDemandInstitute;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use phpDocumentor\Reflection\Types\Null_;
use Ramsey\Collection\Collection;
use Symfony\Component\HttpFoundation\Response;

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
            'hr_demand_institutes.rejected_by_institute',
            'hr_demand_institutes.vacancy_provided_by_institute',
            'hr_demand_institutes.rejected_by_industry_association',
            'hr_demand_institutes.vacancy_approved_by_industry_association',
            'hr_demand_institutes.row_status'
        ]);

        if (!empty($hrDemandId)) {
            $hrDemandBuilder->where('hr_demand_institutes.hr_demand_id', $hrDemandId);
        }

//        $hrDemandBuilder->Where(function ($builder) use ($instituteId) {
//            $builder->orWhereNull('hr_demand_institutes.institute_id');
//            if (!empty($instituteId)) {
//                $builder->orWhere('hr_demand_institutes.institute_id', $instituteId);
//            }
//        });
        if (!empty($instituteId)) {
            $hrDemandBuilder->Where('hr_demand_institutes.institute_id', $instituteId);
        }

        $hrDemandBuilder->orderBy('hr_demand_institutes.id', $order);
        if (is_numeric($rowStatus)) {
            $hrDemandBuilder->where('hr_demand_institutes.row_status', $rowStatus);
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

        /** Delete null institute_id row if at least one row for that institute is exist */
        $instituteIdNullIndex = null;
        foreach ($hrDemands as $index => $hrDemand) {
            if (!$hrDemand->institute_id) {
                $instituteIdNullIndex = $index;
                break;
            }
        }
        foreach ($hrDemands as $hrDemand) {
            if ($hrDemand->institute_id != 0) {
                if ($instituteIdNullIndex != null) {
                    unset($hrDemands[$instituteIdNullIndex]);
                }
            }
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
    public function getOneHrDemandInstitute(int $id): HrDemandInstitute
    {
        /** @var HrDemand|Builder $hrDemandBuilder */
        $hrDemandBuilder = HrDemandInstitute::select([
            'hr_demand_institutes.id',
            'hr_demand_institutes.hr_demand_id',
            'hr_demand_institutes.institute_id',
            'hr_demand_institutes.rejected_by_institute',
            'hr_demand_institutes.vacancy_provided_by_institute',
            'hr_demand_institutes.rejected_by_industry_association',
            'hr_demand_institutes.vacancy_approved_by_industry_association',
            'hr_demand_institutes.row_status'
        ]);

        $hrDemandBuilder->where('hr_demand_institutes.institute_id', '!=', 0);

        $hrDemandBuilder->where('hr_demand_institutes.id', $id);

        return $hrDemandBuilder->firstOrFail();
    }

    /**
     * @param HrDemandInstitute $hrDemandInstitute
     * @param array $data
     * @return HrDemandInstitute
     */
    public function hrDemandApprovedByInstitute(HrDemandInstitute $hrDemandInstitute, array $data): HrDemandInstitute
    {
        if ($hrDemandInstitute->institute_id == null) {
            $authUser = Auth::user();
            $newHrDemandInstitute = new HrDemandInstitute();
            $newHrDemandInstitute->hr_demand_id = $hrDemandInstitute->hr_demand_id;
            $newHrDemandInstitute->vacancy_provided_by_institute = $data['vacancy_provided_by_institute'];
            $newHrDemandInstitute->institute_id = $authUser->institute_id;
            $newHrDemandInstitute->save();
        } else {
            $hrDemandInstitute->vacancy_provided_by_institute = $data['vacancy_provided_by_institute'];
            $hrDemandInstitute->save();
        }

        return $hrDemandInstitute;
    }

    /**
     * @param HrDemandInstitute $hrDemandInstitute
     * @return HrDemandInstitute
     */
    public function hrDemandRejectedByInstitute(HrDemandInstitute $hrDemandInstitute, array $data): HrDemandInstitute
    {
        if ($hrDemandInstitute->institute_id == 0) {
            $authUser = Auth::user();
            $newHrDemandInstitute = new HrDemandInstitute();
            $newHrDemandInstitute->hr_demand_id = $hrDemandInstitute->hr_demand_id;
            $hrDemandInstitute->rejected_by_institute = $data['rejected_by_institute'];
            $newHrDemandInstitute->institute_id = $authUser->institute_id;
            $newHrDemandInstitute->save();
        } else {
            $hrDemandInstitute->rejected_by_institute = $data['rejected_by_institute'];
            $hrDemandInstitute->save();
        }

        return $hrDemandInstitute;
    }

    /**
     * @param HrDemandInstitute $hrDemandInstitute
     * @param array $data
     * @return HrDemandInstitute
     */
    public function hrDemandApprovedByIndustryAssociation(HrDemandInstitute $hrDemandInstitute, array $data): HrDemandInstitute
    {
        $hrDemandInstitute->vacancy_approved_by_industry_association = $data['vacancy_approved_by_industry_association'];
        $hrDemandInstitute->save();

        $hrDemand = HrDemand::find($hrDemandInstitute->hr_demand_id);
        $hrDemand->remaining_vacancy = $hrDemand->remaining_vacancy - $data['vacancy_approved_by_industry_association'];
        $hrDemand->save();

        return $hrDemandInstitute;
    }

    /**
     * @param HrDemandInstitute $hrDemandInstitute
     * @return HrDemandInstitute
     */
    public function hrDemandRejectedByIndustryAssociation(HrDemandInstitute $hrDemandInstitute): HrDemandInstitute
    {
        $hrDemandInstitute->rejected_by_industry_association = BaseModel::BOOLEAN_TRUE;
        $hrDemandInstitute->save();

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
     */
    public function hrDemandApprovedByInstituteValidator(Request $request, HrDemandInstitute $hrDemandInstitute): \Illuminate\Contracts\Validation\Validator
    {
        $data = $request->all();

        $rules = [
            'vacancy_provided_by_institute' => [
                'required',
                'int',
                function ($attr, $value, $failed) use ($hrDemandInstitute, $data) {
                    $hrDemand = HrDemand::find($hrDemandInstitute->hr_demand_id);

                    if ($hrDemand->end_date < Carbon::now()) {
                        $failed("Deadline exceed");
                    }
                    if ($value > $hrDemand->vacancy) {
                        $failed("Vacancy exceed");
                    }

                    if ($hrDemandInstitute->institute_id != 0){
                        if($hrDemandInstitute->vacancy_approved_by_industry_association != 0 &&
                        $hrDemandInstitute->vacancy_approved_by_industry_association > $data['vacancy_provided_by_institute']){
                            $failed("Industry Association already approved more vacancy than the given vacancy !");
                        }
                        if($hrDemandInstitute->rejected_by_industry_association == HrDemandInstitute::REJECTED_BY_INDUSTRY_ASSOCIATION_TRUE){
                            $failed("Already rejected by Industry Association!");
                        }
                    }
                }
            ]
        ];
        return Validator::make($data, $rules);
    }

    /**
     * @param Request $request
     * @param HrDemandInstitute $hrDemandInstitute
     * @return \Illuminate\Contracts\Validation\Validator
     */
    public function hrDemandRejectedByInstituteValidator(Request $request, HrDemandInstitute $hrDemandInstitute): \Illuminate\Contracts\Validation\Validator
    {
        $data = $request->all();

        $rules = [
            'rejected_by_institute' => [
                'required',
                'int',
                Rule::in(HrDemandInstitute::REJECTED_BY_INSTITUTE),
                function ($attr, $value, $failed) use ($hrDemandInstitute) {
                    if ($hrDemandInstitute->institute_id != 0 && $hrDemandInstitute->vacancy_approved_by_industry_association != 0) {
                        $failed("Already approved by Industry Association");
                    }
                }
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

        $rules = [
            'vacancy_approved_by_industry_association' => [
                'required',
                'int',
                'min:1',
                function ($attr, $value, $failed) use ($hrDemandInstitute) {
                    $hrDemand = HrDemand::find($hrDemandInstitute->hr_demand_id);
                    if ($value > $hrDemand->remaining_vacancy) {
                        $failed("Remaining Vacancy exceed");
                    }
                    if ($value > $hrDemandInstitute->vacancy_provided_by_institute) {
                        $failed("Vacancy provided by institute exceed");
                    }
                }
            ]
        ];
        return Validator::make($data, $rules);
    }
}
