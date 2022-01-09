<?php


namespace App\Services;


use App\Models\BaseModel;
use App\Models\HrDemand;
use App\Models\HrDemandInstitute;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class HrDemandInstituteService
{
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
     * @param HrDemandInstitute $hrDemandInstitute
     * @return HrDemandInstitute
     */
    public function hrDemandRejectedByInstitute(HrDemandInstitute $hrDemandInstitute): HrDemandInstitute
    {
        $hrDemandInstitute->rejected_by_institute = BaseModel::BOOLEAN_TRUE;
        $hrDemandInstitute->save();

        return $hrDemandInstitute;
    }

    /**
     * @param Request $request
     * @param int $hrDemandId
     * @return \Illuminate\Contracts\Validation\Validator
     */
    public function hrDemandApprovedByInstituteValidator(Request $request, int $hrDemandId): \Illuminate\Contracts\Validation\Validator
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
                function ($attr, $value, $failed) use ($hrDemandInstitute) {
                    $hrDemand = HrDemand::find($hrDemandInstitute->hr_demand_id);
                    if ($value > $hrDemand->remaining_vacancy) {
                        $failed("Remaining Vacancy exceed");
                    }
                    if($value > $hrDemandInstitute->vacancy_provided_by_institute){
                        $failed("Vacancy provided by institute exceed");
                    }
                }
            ]
        ];
        return Validator::make($data, $rules);
    }
}
