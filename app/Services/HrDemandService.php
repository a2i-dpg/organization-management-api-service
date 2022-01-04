<?php

namespace App\Services;

use App\Models\HrDemand;
use App\Models\HrDemandInstitute;
use Carbon\Carbon;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

/**
 * Class HrDemandService
 * @package App\Services
 */
class HrDemandService
{
    /**
     * @param array $data
     * @return HrDemand
     */
    public function store(array $data): HrDemand
    {
        $hrDemand = new HrDemand();
        $hrDemand->fill($data);
        $hrDemand->save();

        if(!empty($data['institute_id'])){
            foreach ($data['institute_id'] as $datum){
                $payload = [
                    'hr_demand_id' => $hrDemand->id,
                    'institute_id' => $datum
                ];
                $hrDemandInstitute = new HrDemandInstitute();
                $hrDemandInstitute->fill($payload);
                $hrDemandInstitute->save();
            }
        }
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
     * @param Request $request
     * @param int|null $id
     * @return \Illuminate\Contracts\Validation\Validator
     */
    public function validator(Request $request, int $id = null): \Illuminate\Contracts\Validation\Validator
    {
        $data = $request->all();
        if (!empty($data['institute_id'])) {
            $data["institute_id"] = isset($data['institute_id']) && is_array($data['institute_id']) ? $data['institute_id'] : explode(',', $data['institute_id']);
        }
        $customMessage = [
            'row_status.in' => 'Row status must be within 1 or 0. [30000]'
        ];
        $rules = [
            'industry_association_id' => [
                'required',
                'int',
                'exists:industry_associations,id,deleted_at,NULL',
            ],
            'organization_id' => [
                'required',
                'int',
                'exists:organizations,id,deleted_at,NULL',
            ],
            'institute_id' => [
                'required',
                'array'
            ],
            'institute_id.*' => [
                'nullable',
                'int'
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
}
