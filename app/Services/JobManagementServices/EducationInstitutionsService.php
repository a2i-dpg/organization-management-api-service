<?php

namespace App\Services\JobManagementServices;


use App\Models\AreaOfBusiness;
use App\Models\EducationalInstitution;
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

class EducationInstitutionsService
{
    /**
     * @param Request $request
     * @return \Illuminate\Contracts\Validation\Validator
     */
    public function filterEducationInstitutionValidator(Request $request) :\Illuminate\Contracts\Validation\Validator
    {
        return Validator::make($request->all(), [
            'name' => 'nullable|max:500|min:2'
        ]);
    }

    /**
     * @param array $request
     * @param Carbon $startTime
     * @return array
     */

    public function getEducationalInstitutionList(array $request, Carbon $startTime): array
    {
        $title = $request['title'] ?? "";
        $type = $request['type'] ?? "";
        $paginate = $request['page'] ?? "";
        $pageSize = $request['page_size'] ?? "";
        $order = $request['order'] ?? "ASC";


        $educationalInstitutionBuilder = EducationalInstitution::select(
            [
                'educational_institutions.id',
                'educational_institutions.name',
                'educational_institutions.type',
                'educational_institutions.created_at',
                'educational_institutions.updated_at'
            ]
        );

        $educationalInstitutionBuilder->orderBy('educational_institutions.id', $order);

        if (!empty($name)) {
            $educationalInstitutionBuilder->where('educational_institutions.name', 'like', '%' . $name . '%');
        }
        if (!empty($type)) {
            $educationalInstitutionBuilder->where('educational_institutions.type', 'like', '%' . $type . '%');
        }
        /** @var Collection $areaOfBusiness */
        $educationalInstitution = $educationalInstitutionBuilder->get();


        $response['order'] = $order;
        $response['data'] = $educationalInstitution->toArray()['data'] ?? $educationalInstitution->toArray();
        $response['_response_status'] = [
            "success" => true,
            "code" => Response::HTTP_OK,
            "query_time" => $startTime->diffInSeconds(Carbon::now())
        ];

        return $response;
    }


}
