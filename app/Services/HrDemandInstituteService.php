<?php


namespace App\Services;


use App\Models\BaseModel;
use App\Models\HrDemand;
use App\Models\HrDemandInstitute;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

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

                    if($hrDemand->end_date > Carbon::now()){
                        $failed("Deadline exceed");
                    }
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

    /**
     * @param HrDemand $hrDemand
     * @param array $data
     * @return HrDemand
     */
    public function update(HrDemandInstitute $hrDemandInstitute, array $data): HrDemand
    {
        $hrDemand = HrDemand::find($hrDemandInstitute->hr_demand_id);

        $payloadForHrDemand = [
            'end_date' => $data['end_date'],
            'skill_id' => $data['skill_id'],
            'requirement' => $data['requirement'],
            'requirement_en' => $data['requirement_en'],
            'vacancy' => $data['vacancy']
        ];

        if($hrDemand->skill_id != $data['skill_id']){
            $hrDemandInstituteIds = HrDemandInstitute::where('hr_demand_id',$hrDemand->id)->pluck('id');

            foreach ($hrDemandInstituteIds as $id){
                $hrDemandInstitute = HrDemandInstitute::find($id);
                $hrDemandInstitute->delete();
            }


        }


        $hrDemandInstitute->fill($data);
        $hrDemandInstitute->save();

        $this->storeHrDemandInstitutes($data, $hrDemand);
        return $hrDemand;
    }

    /**
     * @param Request $request
     * @param int|null $id
     * @return \Illuminate\Contracts\Validation\Validator
     */
    public function updateValidator(Request $request, HrDemandInstitute $hrDemandInstitute, int $id = null): \Illuminate\Contracts\Validation\Validator
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
                function ($attr, $value, $failed) use ($hrDemandInstitute, $data) {
                    $hrDemand = HrDemand::find($hrDemandInstitute->hr_demand_id);
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
