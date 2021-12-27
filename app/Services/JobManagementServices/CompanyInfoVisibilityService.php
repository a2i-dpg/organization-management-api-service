<?php

namespace App\Services\JobManagementServices;


use App\Models\CompanyInfoVisibility;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class CompanyInfoVisibilityService
{

    /**
     * @param array $validatedData
     * @return CompanyInfoVisibility
     */
    public function store(array $validatedData): CompanyInfoVisibility
    {
        $companyInfoVisibility = app(CompanyInfoVisibility::class);
        $companyInfoVisibility->fill($validatedData);
        $companyInfoVisibility->save();
        return $companyInfoVisibility;
    }


    public function validator(Request $request): \Illuminate\Contracts\Validation\Validator
    {
        $requestData = $request->all();
        $rules = [
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
                'integer'
            ],
            'is_company_business_visible' => [
                'integer',
                Rule::in(CompanyInfoVisibility::COMPANY_BUSINESS_VISIBILITY)
            ]
        ];
        if (!empty($requestData['is_company_name_visible']) && $requestData['is_company_name_visible'] == CompanyInfoVisibility::IS_COMPANY_NAME_VISIBLE_FALSE) {
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
