<?php

namespace App\Services\JobManagementServices;

use App\Models\AreaOfExperience;
use App\Models\BaseModel;
use App\Models\CandidateRequirement;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;


class CandidateRequirementsService
{

    /**
     * @param string $jobId
     * @return CandidateRequirement|null
     */
    public function getCandidateRequirements(string $jobId): CandidateRequirement|null
    {
        /** @var CandidateRequirement|Builder $candidateRequirementBuilder */
        $candidateRequirementBuilder = CandidateRequirement::select([
            'candidate_requirements.id',
            'candidate_requirements.job_id',
            'candidate_requirements.other_educational_qualification',
            'candidate_requirements.other_educational_qualification_en',
            'candidate_requirements.is_experience_needed',
            'candidate_requirements.is_freshers_encouraged',
            'candidate_requirements.minimum_year_of_experience',
            'candidate_requirements.maximum_year_of_experience',
            'candidate_requirements.additional_requirements',
            'candidate_requirements.additional_requirements_en',
            'candidate_requirements.age_minimum',
            'candidate_requirements.age_maximum',
            'candidate_requirements.person_with_disability',
            'candidate_requirements.preferred_retired_army_officer',
            'candidate_requirements.created_at',
            'candidate_requirements.updated_at',
        ]);

        $candidateRequirementBuilder->where('candidate_requirements.job_id', $jobId);
        $candidateRequirementBuilder->with('degrees.educationLevel:id,title,title_en');
        $candidateRequirementBuilder->with('degrees.examDegree:id,title,title_en');
        $candidateRequirementBuilder->with('preferredEducationalInstitutions:id,name');
        $candidateRequirementBuilder->with('trainings:job_id,title');
        $candidateRequirementBuilder->with('professionalCertifications:job_id,title');
        $candidateRequirementBuilder->with('areaOfExperiences:id,title,title_en');
        $candidateRequirementBuilder->with('areaOfBusinesses:id,title,title_en');
        $candidateRequirementBuilder->with('skills:id,title,title_en');
        $candidateRequirementBuilder->with('genders:job_id,gender_id');

        return $candidateRequirementBuilder->first();
    }

    /**
     * @param array $validatedData
     * @return CandidateRequirement
     */
    public function store(array $validatedData): CandidateRequirement
    {
        return CandidateRequirement::updateOrCreate(
            ['job_id' => $validatedData['job_id']],
            $validatedData
        );
    }

    /**
     * @param CandidateRequirement $candidateRequirements
     * @param array $degrees
     */
    public function syncWithDegrees(CandidateRequirement $candidateRequirements, array $degrees)
    {
        DB::table('candidate_requirement_degrees')->where('candidate_requirement_id', $candidateRequirements->id)->delete();
        foreach ($degrees as $item) {
            $educationLevelId = $item["education_level_id"] ?? null;
            $examDegreeId = $item["exam_degree_id"] ?? null;
            $majorSubject = $item["major_subject"] ?? null;
            DB::table('candidate_requirement_degrees')->insert(
                [
                    'candidate_requirement_id' => $candidateRequirements->id,
                    'job_id' => $candidateRequirements->job_id,
                    'education_level_id' => $educationLevelId,
                    'exam_degree_id' => $examDegreeId,
                    'major_subject' => $majorSubject
                ]
            );
        }
    }

    /**
     * @param CandidateRequirement $candidateRequirements
     * @param array $preferredEducationalInstitution
     */
    public function syncWithPreferredEducationalInstitution(CandidateRequirement $candidateRequirements, array $preferredEducationalInstitution)
    {
        $candidateRequirements->preferredEducationalInstitutions()->syncWithPivotValues($preferredEducationalInstitution, ['job_id' => $candidateRequirements->job_id]);
    }

    /**
     * @param CandidateRequirement $candidateRequirements
     * @param array $training
     */
    public function syncWithTraining(CandidateRequirement $candidateRequirements, array $training)
    {
        DB::table('candidate_requirement_trainings')->where('candidate_requirement_id', $candidateRequirements->id)->delete();
        foreach ($training as $item) {
            DB::table('candidate_requirement_trainings')->insert(
                [
                    'candidate_requirement_id' => $candidateRequirements->id,
                    'job_id' => $candidateRequirements->job_id,
                    'title' => $item
                ]
            );
        }
    }

    /**
     * @param CandidateRequirement $candidateRequirements
     * @param array $professionalCertification
     */
    public function syncWithProfessionalCertification(CandidateRequirement $candidateRequirements, array $professionalCertification)
    {
        DB::table('candidate_requirement_professional_certifications')->where('candidate_requirement_id', $candidateRequirements->id)->delete();
        foreach ($professionalCertification as $item) {
            DB::table('candidate_requirement_professional_certifications')->insert(
                [
                    'candidate_requirement_id' => $candidateRequirements->id,
                    'job_id' => $candidateRequirements->job_id,
                    'title' => $item
                ]
            );
        }
    }

    /**
     * @param CandidateRequirement $candidateRequirements
     * @param array $areaOfExperience
     */
    public function syncWithAreaOfExperience(CandidateRequirement $candidateRequirements, array $areaOfExperience)
    {
        $candidateRequirements->areaOfExperiences()->syncWithPivotValues($areaOfExperience, ['job_id' => $candidateRequirements->job_id]);
    }

    /**
     * @param CandidateRequirement $candidateRequirements
     * @param array $areaOfBusiness
     */
    public function syncWithAreaOfBusiness(CandidateRequirement $candidateRequirements, array $areaOfBusiness)
    {
        $candidateRequirements->areaOfBusiness()->syncWithPivotValues($areaOfBusiness, ['job_id' => $candidateRequirements->job_id]);
    }

    /**
     * @param CandidateRequirement $candidateRequirements
     * @param array $skills
     */
    public function syncWithSkills(CandidateRequirement $candidateRequirements, array $skills)
    {
        $candidateRequirements->skills()->syncWithPivotValues($skills, ['job_id' => $candidateRequirements->job_id]);
    }

    /**
     * @param CandidateRequirement $candidateRequirements
     * @param array $gender
     */
    public function syncWithGender(CandidateRequirement $candidateRequirements, array $gender)
    {
//        dd($candidateRequirements ,"<--->" , $gender);
        DB::table('candidate_requirement_gender')->where('candidate_requirement_id', $candidateRequirements->id)->delete();
        foreach ($gender as $item) {
            DB::table('candidate_requirement_gender')->insert(
                [
                    'candidate_requirement_id' => $candidateRequirements->id,
                    'job_id' => $candidateRequirements->job_id,
                    'gender_id' => $item
                ]
            );
        }
    }


    /**
     * @param Request $request
     * @return \Illuminate\Contracts\Validation\Validator
     */
    public function validator(Request $request): \Illuminate\Contracts\Validation\Validator
    {
        $data = $request->all();


        if (!empty($data["degrees"])) {
            $data["degrees"] = is_array($data['degrees']) ? $data['degrees'] : explode(',', $data['degrees']);
        }
        if (!empty($data["preferred_educational_institutions"])) {
            $data["preferred_educational_institutions"] = is_array($data['preferred_educational_institutions']) ? $data['preferred_educational_institutions'] : explode(',', $data['preferred_educational_institutions']);
        }
        if (!empty($data["trainings"])) {
            $data["trainings"] = is_array($data['trainings']) ? $data['trainings'] : explode(',', $data['trainings']);
        }
        if (!empty($data["professional_certifications"])) {
            $data["professional_certifications"] = is_array($data['professional_certifications']) ? $data['professional_certifications'] : explode(',', $data['professional_certifications']);
        }
        if (!empty($data["area_of_experiences"])) {
            $data["area_of_experiences"] = is_array($data['area_of_experiences']) ? $data['area_of_experiences'] : explode(',', $data['area_of_experiences']);
        }
        if (!empty($data["area_of_businesses"])) {
            $data["area_of_businesses"] = is_array($data['area_of_businesses']) ? $data['area_of_businesses'] : explode(',', $data['area_of_businesses']);
        }
        if (!empty($data["skills"])) {
            $data["skills"] = is_array($data['skills']) ? $data['skills'] : explode(',', $data['skills']);
        }
        if (!empty($data["genders"])) {
            $data["genders"] = is_array($data['genders']) ? $data['genders'] : explode(',', $data['genders']);
        }


        $rules = [
            "job_id" => [
                "required",
                "exists:additional_job_information,job_id,deleted_at,NULL",
            ],
            "degrees" => [
                "nullable",
                "array",
            ],
            "degrees.*.education_level_id" => [
                "nullable",
                "exists:education_levels,id,deleted_at,NULL",
            ],
            "degrees.*.exam_degree_id" => [
                "nullable",
                "exists:exam_degrees,id,deleted_at,NULL",
            ],
            "degrees.*.major_subject" => [
                "nullable",
                "string"
            ],
            "preferred_educational_institutions" => [
                "nullable",
                "array"
            ],
            "preferred_educational_institutions.*" => [
                "integer",
                "distinct",
                "required",
                "exists:educational_institutions,id,deleted_at,NULL",
            ],
            "other_educational_qualification" => [
                "nullable",
                "string",
            ],
            "other_educational_qualification_en" => [
                "nullable",
                "string",
            ],
            "trainings" => [
                "nullable",
                "array"
            ],
            "trainings.*" => [
                "string",
            ],
            "professional_certifications" => [
                "nullable",
                "array"
            ],
            "professional_certifications.*" => [
                "string",
            ],
            "is_experience_needed" => [
                "nullable",
                "numeric",
                Rule::in(array_keys(BaseModel::BOOLEAN_FLAG))
            ],
            "minimum_year_of_experience" => [
                "nullable",
                "numeric",
                "min:0",
                "max:50"
            ],
            "maximum_year_of_experience" => [
                "nullable",
                "numeric",
                "min:1",
                "max:50"
            ],
            "is_freshers_encouraged" => [
                "nullable",
                "numeric",
                Rule::in(array_keys(BaseModel::BOOLEAN_FLAG))
            ],
            "area_of_experiences" => [
                "nullable",
                "array",
            ],
            "area_of_experiences.*" => [
                "required",
                "distinct",
                "integer",
                "exists:area_of_experiences,id,deleted_at,NULL",
            ],
            "area_of_businesses" => [
                "nullable",
                "array",
            ],
            "area_of_businesses.*" => [
                "integer",
                "distinct",
                "required",
                "exists:area_of_business,id,deleted_at,NULL",
            ],
            "skills" => [
                "nullable",
                "array",
            ],
            "skills.*" => [
                "integer",
                "distinct",
                "required",
                "exists:skills,id,deleted_at,NULL",
            ],
            "additional_requirements" => [
                "nullable",
                "string"
            ],
            "additional_requirements_en" => [
                "nullable",
                "string"
            ],
            "genders" => [
                "nullable",
                "array"
            ],
            "genders.*" => [
                "distinct",
                Rule::in(array_keys(BaseModel::GENDERS))
            ],
            "age_minimum" => [
                "nullable",
                "numeric",
                "min:14",
                "max:90"
            ],
            "age_maximum" => [
                "nullable",
                "numeric",
                "min:14",
                "max:90"
            ],
            "person_with_disability" => [
                "nullable",
                "numeric",
                Rule::in(array_keys(BaseModel::BOOLEAN_FLAG))
            ],
            "preferred_retired_army_officer" => [
                "nullable",
                "numeric",
                Rule::in(array_keys(BaseModel::BOOLEAN_FLAG))
            ],
        ];
        return Validator::make($data, $rules);
    }

}
