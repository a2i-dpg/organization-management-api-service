<?php

namespace App\Services;

use App\Models\BaseModel;
use App\Models\InterviewSchedule;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Symfony\Component\HttpFoundation\Response;




class InterviewScheduleService
{
    /**
     * @param array $data
     * @return InterviewSchedule
     */
    public function store(array $data): InterviewSchedule
    {
        $schedule = new InterviewSchedule();
        $schedule->fill($data);
        $schedule->save();
        return $schedule;
    }


    /**
     * @param InterviewSchedule $schedule
     * @param array $data
     * @return InterviewSchedule
     */
    public function update(InterviewSchedule $schedule, array $data): InterviewSchedule
    {
        $schedule->fill($data);
        $schedule->save();
        return $schedule;
    }

    /**
     * @param InterviewSchedule $schedule
     * @return bool
     */
    public function destroy(InterviewSchedule $schedule): bool
    {
        return $schedule->delete();
    }


    /**
     * @param Request $request
     * @param int|null $id
     * @return \Illuminate\Contracts\Validation\Validator
     */
    public function validator(Request $request, int $id = null): \Illuminate\Contracts\Validation\Validator
    {
        $rules = [
            'job_id' => [
                'required',
                'string'
            ],
            'recruitment_step_id' => [
                'nullable',
                'string'
            ],
            'interview_scheduled_at' => [
                'nullable',
                'string'
            ],
            'maximum_number_of_applicants' => [
                'required',
                'integer'
            ],
            'interview_invite_type' =>[
                'nullable',
                'integer'
            ],
            'interview_address' =>[
                'required',
                'string'
	        ]
        ];
        return Validator::make($request->all(), $rules);
    }


}
