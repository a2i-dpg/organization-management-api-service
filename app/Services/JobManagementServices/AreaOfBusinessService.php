<?php

namespace App\Services\JobManagementServices;


use App\Models\AreaOfBusiness;
use App\Models\EmploymentType;
use App\Models\PrimaryJobInformation;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Symfony\Component\HttpFoundation\Response;

class AreaOfBusinessService
{
    /**
     * @param Request $request
     * @return \Illuminate\Contracts\Validation\Validator
     */
    public function filterAreaOfBusinessValidator(Request $request) :\Illuminate\Contracts\Validation\Validator
    {
        return Validator::make($request->all(), [
            'title' => 'nullable|max:500|min:2'
        ]);

    }

    /**
     * @param array $request
     * @param Carbon $startTime
     * @return array
     */
    public function getAreaOfBusinessList(array $request, Carbon $startTime): array
    {
        $title = $request['title'] ?? "";
        $paginate = $request['page'] ?? "";
        $pageSize = $request['page_size'] ?? "";
        $order = $request['order'] ?? "ASC";


        $areaOfBusinessBuilder = AreaOfBusiness::select(
            [
                'area_of_business.id',
                'area_of_business.title',
                'area_of_business.created_at',
                'area_of_business.updated_at'
            ]
        );

        $areaOfBusinessBuilder->orderBy('area_of_business.id', $order);

        if (!empty($title)) {
            $areaOfBusinessBuilder->where('area_of_business.title', 'like', '%' . $title . '%');
        }
        /** @var Collection $areaOfBusiness */
        $areaOfBusiness = $areaOfBusinessBuilder->get();


        $response['order'] = $order;
        $response['data'] = $areaOfBusiness->toArray()['data'] ?? $areaOfBusiness->toArray();
        $response['_response_status'] = [
            "success" => true,
            "code" => Response::HTTP_OK,
            "query_time" => $startTime->diffInSeconds(Carbon::now())
        ];

        return $response;
    }

}
