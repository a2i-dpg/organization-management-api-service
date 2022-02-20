<?php

namespace App\Services\JobManagementServices;

use App\Models\AppliedJob;

use App\Models\CandidateInterview;
use App\Models\InterviewSchedule;
use App\Models\RecruitmentStep;
use Dotenv\Exception\ValidationException;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
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
        /** @var Builder|Model $scheduleBuilder */
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
        return $scheduleBuilder->where('interview_schedules.id', $id)->firstOrFail();
    }


    public function getSchedulesByStepId(int $id): mixed
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
        return $scheduleBuilder->where('interview_schedules.recruitment_step_id', $id)->get();
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
     * @return InterviewSchedule|bool
     */
    public function update(InterviewSchedule $schedule, array $data): InterviewSchedule|bool
    {
        $schedule->fill($data);
        $schedule->save();
        return $schedule;

    }

    /**
     * @param int $scheduleId
     * @return mixed
     */
    public function isScheduleUpdatable(int $scheduleId): mixed
    {
        return $this->countCandidatePerSchedule($scheduleId)==0;
    }


    /**
     * @param InterviewSchedule $schedule
     * @return bool
     */
    public function destroy(InterviewSchedule $schedule): bool
    {
        $scheduledCandidates = $this->countCandidatePerSchedule($schedule->id);

        if ($scheduledCandidates == 0) {
            return $schedule->delete();
        } else {
            return false;
        }
    }

    /**
     * @param int $scheduleId
     * @return mixed
     */
    public function countCandidatePerSchedule(int $scheduleId): mixed
    {
        return CandidateInterview::where('interview_schedule_id', $scheduleId)->count('id');

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
                'string',
                'exists:primary_job_information,job_id,deleted_at,NULL'
            ],
            'recruitment_step_id' => [
                'integer',
                'required',
                'exists:recruitment_steps,id,deleted_at,NULL'
            ],
            'interview_scheduled_at' => [
                'required',
                'date_format:Y-m-d H:i'
            ],
            'maximum_number_of_applicants' => [
                'required',
                'integer'
            ],
            'interview_invite_type' => [
                'required',
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
    public function CandidateAssigningToScheduleValidator(Request $request, InterviewSchedule $interviewSchedule): \Illuminate\Contracts\Validation\Validator
    {

        $requestData = $request->all();
        if (!empty($requestData['applied_job_ids'])) {
            $requestData['applied_job_ids'] = is_array($requestData['applied_job_ids']) ? $requestData['applied_job_ids'] : explode(',', $requestData['applied_job_ids']);
        }

        $step = RecruitmentStep::where('id', $interviewSchedule->recruitment_step_id)->where('job_id', $interviewSchedule->job_id)->findOrFail();

        /** @var CandidateInterview|Builder $currentScheduleCandidateCount */
        $currentScheduleCandidateCount = CandidateInterview::where('recruitment_step_id', $step->id)
            ->where('interview_schedule_id', $interviewSchedule->id)
            ->count();

        /** @var CandidateInterview|Builder $existingScheduleCandidateCount */
        $existingScheduleCandidateCount = CandidateInterview::where('recruitment_step_id', $step->id)
            ->where('interview_schedule_id', $interviewSchedule->id)
            ->whereIn('applied_job_id', $requestData['applied_job_ids'])
            ->count();

        /** @var AppliedJob|Builder $applicantCount */
        $applicantCount = AppliedJob::where('job_id', $interviewSchedule->job_id)->whereIn('id', $requestData['applied_job_ids'])->count();

        if ($applicantCount != count($requestData['applied_job_ids'])) throw new ValidationException('applied_job_ids not valid.');

        $rules = [
            'notify' => [
                'required',
                'int',
                Rule::in(CandidateInterview::NOTIFICATION)
            ],
            'applied_job_ids' => [
                'required',
                'array',
                'min:1',
                'distinct',
                // 'max:' . $interviewSchedule->maximum_number_of_applicants
                'max:' . ($interviewSchedule->maximum_number_of_applicants - $currentScheduleCandidateCount + $existingScheduleCandidateCount)
            ],
            'applied_job_ids.*' => [
                'required',
                'integer',
                'exists:applied_jobs,id,deleted_at,NULL,job_id,' . $interviewSchedule->job_id,
                Rule::unique('candidate_interviews', 'applied_job_id')
                    ->where(function (\Illuminate\Database\Query\Builder $query) use ($interviewSchedule) {
                        return $query->where('recruitment_step_id', $interviewSchedule->recruitment_step_id);
                    })
            ],
            'interview_invite_type' => [
                'integer',
                'required',
                Rule::in(AppliedJob::INVITE_TYPES)
            ]
        ];

        return Validator::make($requestData, $rules);
    }

    /**
     * @param Request $request
     * @param InterviewSchedule $interviewSchedule
     * @return \Illuminate\Contracts\Validation\Validator
     */
    public function CandidateRemoveFromScheduleValidator(Request $request, InterviewSchedule $interviewSchedule): \Illuminate\Contracts\Validation\Validator
    {
        if (!empty($request['applied_job_ids'])) {
            $request['applied_job_ids'] = isset($request['applied_job_ids']) && is_array($request['applied_job_ids']) ? $request['applied_job_ids'] : explode(',', $request['applied_job_ids']);
        }

        $rules = [
            'applied_job_ids' => [
                'required',
                'array',
                'min:1',
                'distinct',
            ],
            'applied_job_ids.*' => [
                'required',
                'integer',
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
            $appliedJob->apply_status = AppliedJob::APPLY_STATUS['Interview_scheduled'];
            $appliedJob->save();

            $candidateInterview->applied_job_id = $appliedJob->id;
            $candidateInterview->job_id = $appliedJob->job_id;
            $candidateInterview->recruitment_step_id = $appliedJob->current_recruitment_step_id;
            $candidateInterview->interview_schedule_id = $scheduleId;

            $candidateInterview->save();
        }

    }

    /**
     * @param int $scheduleId
     * @param array $data
     */
    public function removeCandidateFromSchedule(int $scheduleId, array $data)
    {
        $appliedJobIds = $data["applied_job_ids"];


        foreach ($appliedJobIds as $appliedJobId) {
            $appliedJob = AppliedJob::findOrFail($appliedJobId);
            $appliedJob->apply_status = AppliedJob::APPLY_STATUS['Shortlisted'];
            $appliedJob->save();

            CandidateInterview::where('applied_job_id', $appliedJob->id)
                ->where('recruitment_step_id', $appliedJob->current_recruitment_step_id)
                ->where('interview_schedule_id', $scheduleId)
                ->delete();

        }

    }

}
