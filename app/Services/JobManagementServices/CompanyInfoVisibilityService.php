<?php

namespace App\Services\JobManagementServices;


use App\Models\CompanyInfoVisibility;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class CompanyInfoVisibilityService
{
    /**
     * @param int $jobId
     * @return Model|Builder
     */
    public function getCompanyInfoVisibility(int $jobId): Model|Builder
    {
        /** @var Builder $companyInfoVisibilityBuilder */
        $companyInfoVisibilityBuilder = CompanyInfoVisibility::select([
            'company_info_visibilities.id',
            'company_info_visibilities.job_id',
            'company_info_visibilities.is_company_name_visible',
            'company_info_visibilities.company_name',
            'company_info_visibilities.company_name_en',
            'company_info_visibilities.is_company_address_visible',
            'company_info_visibilities.company_industry_type',
            'company_info_visibilities.is_company_business_visible',
            'company_info_visibilities.created_at',
            'company_info_visibilities.updated_at',
        ]);

        $companyInfoVisibilityBuilder->where('company_info_visibilities.job_id', $jobId);

        return $companyInfoVisibilityBuilder->firstOrFail();
    }

    /**
     * @param array $data
     * @return CompanyInfoVisibility
     */
    public function storeOrUpdate(array $data): CompanyInfoVisibility
    {
        return CompanyInfoVisibility::updateOrCreate(
            ['job_id' => $data['job_id']],
            $data
        );
    }


    public function companyInfoVisibilityValidator(Request $request): \Illuminate\Contracts\Validation\Validator
    {
        $requestData = $request->all();
        $rules = [
            "job_id" => [
                "required",
                'string',
                'exists:candidate_requirements,id,deleted_at,NULL',
            ],
            'is_company_name_visible' => [
                'required',
                'integer',
                Rule::in(CompanyInfoVisibility::COMPANY_NAME_VISIBILITY)
            ],
            'is_company_address_visible' => [
                'required',
                'integer',
                Rule::in(CompanyInfoVisibility::COMPANY_ADDRESS_VISIBILITY)
            ],
            'company_industry_type' => [
                'required',
                'integer',
                'exists:industry_association_trades,id,deleted_at,NULL'
            ],
            'is_company_business_visible' => [
                'integer',
                Rule::in(CompanyInfoVisibility::COMPANY_BUSINESS_VISIBILITY)
            ]
        ];
        if (is_numeric($requestData['is_company_name_visible']) && $requestData['is_company_name_visible'] == CompanyInfoVisibility::IS_COMPANY_NAME_VISIBLE_FALSE) {
            $rules['company_name'] = [
                'required',
                'string',
                'max:600'
            ];
            $rules['company_name_en'] = [
                'nullable',
                'string',
                'max:300'
            ];
        }
        return validator::make($requestData, $rules);

    }

}
