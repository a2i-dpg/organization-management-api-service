<?php

namespace App\Services;

use App\Models\AppliedJob;
use App\Models\BaseModel;
use App\Models\InterviewSchedule;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Symfony\Component\HttpFoundation\Response;




class InterviewScheduleService
{

    /**
     * @param int $id
     * @return InterviewSchedule
     */
    public function getOneInterviewSchedule(int $id):InterviewSchedule
    {
        $scheduleBuilder = InterviewSchedule::select([
            'interview_schedules.id',
            'interview_schedules.recruitment_step_id',
            'interview_schedules.interview_scheduled_at',
            'interview_schedules.maximum_number_of_applicants',
            'interview_schedules.interview_invite_type',
            'interview_schedules.interview_address',
            'interview_schedules.created_at',
            'interview_schedules.updated_at',
            'interview_schedules.deleted_at'
        ]);
        return $scheduleBuilder->firstOrFail();
    }
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

    public function validatorForCandidateAssigning(Request $request, int $id = null): \Illuminate\Contracts\Validation\Validator
    {
        $schedule = $this->getOneInterviewSchedule($id);

        $rules = [
            'applied_job_ids' => [
                'required',
                'array'
            ],
            'applied_job_ids.*' => [
                'required',
                'integer',
                'exists:applied_jobs,id,deleted_at,NULL'
            ]
        ];
        return Validator::make($request->all(), $rules);
    }


    public function assignToSchedule($jobApplicantIds , $id)
    {
        foreach($jobApplicantIds as $jobApplicantId){
            $jobApplicant = AppliedJob::find($jobApplicantId);
            DB::table('assign_candidates_to_schedules')->insert(
                [
                    'applied_job_id' => $jobApplicant->id,
                    'job_id' => $jobApplicant->job_id,
                    'recruitment_step_id' => $jobApplicant->recruitment_step_id,
                    'schedule_id' => $id,
                    'invited_at'=> Carbon::now(),
                    'confirmation_status'=>InterviewSchedule::CONFIRMATION_STATUS
                ]
            );
        }
    }

}
