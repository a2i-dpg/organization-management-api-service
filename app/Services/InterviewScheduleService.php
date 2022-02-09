<?php

namespace App\Services;

use App\Models\AppliedJob;

use App\Models\CandidateInterview;
use App\Models\InterviewSchedule;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;


class InterviewScheduleService
{

    /**
     * @param int $id
     * @return InterviewSchedule
     */
    public function getOneInterviewSchedule(int $id): InterviewSchedule
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
            'interview_invite_type' => [
                'nullable',
                'integer'
            ],
            'interview_address' => [
                'required',
                'string'
            ]
        ];
        return Validator::make($request->all(), $rules);
    }

    /**
     * @param Request $request
     * @param InterviewSchedule $interviewSchedule
     * @return \Illuminate\Contracts\Validation\Validator
     */
    public function validatorForCandidateAssigning(Request $request, InterviewSchedule $interviewSchedule): \Illuminate\Contracts\Validation\Validator
    {
        $rules = [
            'notify'=>[
                'int',
                Rule::in(CandidateInterview::NOTIFICATION)
            ],
            'applied_job_ids' => [
                'required',
                'array',
                'min:1',
                'distinct',
                'max:' . $interviewSchedule->maximum_number_of_applicants
            ],
            'applied_job_ids.*' => [
                'required',
                'integer',
                Rule::unique('candidate_interviews', 'recruitment_step_id'),
                'exists:applied_jobs,id,deleted_at,NULL'
            ]
        ];

        return Validator::make($request->all(), $rules);
    }

    /**
     * @param int $scheduleId
     * @param array $data
     */
    public function assignCandidateToSchedule(int $scheduleId, array $data)
    {
        $appliedJobIds = $data["applied_job_ids"];


        foreach ($appliedJobIds as $appliedJobId) {
            $candidateInterview = new CandidateInterview();

            $appliedJob = AppliedJob::findOrFail($appliedJobId);

            $candidateInterview->applied_job_id = $appliedJob->id;
            $candidateInterview->job_id = $appliedJob->job_id;
            $candidateInterview->recruitment_step_id = $appliedJob->current_recruitment_step_id;
            $candidateInterview->interview_schedule_id = $scheduleId;

            $candidateInterview->save();
        }

    }

}
