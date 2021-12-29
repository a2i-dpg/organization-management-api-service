<?php

namespace App\Services\JobManagementServices;

use App\Models\BaseModel;
use App\Models\CandidateRequirement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

/**
 * WARNING: NOT COMPLETED
 */
class CandidateRequirementsService
{
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
        DB::table('candidate_requirements_degrees')->where('candidate_requirements_id', $candidateRequirements->id)->delete();
        foreach ($degrees as $item) {
            $educationLevel = !empty($item["education_level"]) ? $item["education_level"] : null;
            $eduGroup = !empty($item["edu_group"]) ? $item["edu_group"] : null;
            $eduMajor = !empty($item["edu_major"]) ? $item["edu_major"] : null;
            DB::table('candidate_requirements_training')->insert(
                [
                    'candidate_requirements_id' => $candidateRequirements->id,
                    'education_level_id' => $educationLevel,
                    'edu_group_id' => $eduGroup,
                    'edu_major_id' => $eduMajor
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
        foreach ($preferredEducationalInstitution as $item) {
            DB::table('candidate_requirements_preferred_educational_institution')->updateOrInsert(
                [
                    'candidate_requirements_id' => $candidateRequirements->id,
                    'preferred_educational_institution_id' => $item
                ],
                [
                    'candidate_requirements_id' => $candidateRequirements->id,
                    'preferred_educational_institution_id' => $item

                ]
            );

        }

    }

    /**
     * @param CandidateRequirement $candidateRequirements
     * @param array $training
     */
    public function syncWithTraining(CandidateRequirement $candidateRequirements, array $training)
    {
        DB::table('candidate_requirements_training')->where('candidate_requirements_id', $candidateRequirements->id)->delete();
        foreach ($training as $item) {
            DB::table('candidate_requirements_training')->insert(
                [
                    'candidate_requirements_id' => $candidateRequirements->id,
                    'training' => $item
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
        DB::table('candidate_requirements_professional_certification')->where('candidate_requirements_id', $candidateRequirements->id)->delete();
        foreach ($professionalCertification as $item) {
            DB::table('candidate_requirements_professional_certification')->insert(
                [
                    'candidate_requirements_id' => $candidateRequirements->id,
                    'professional_certification' => $item
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
        DB::table('candidate_requirements_area_of_experience')->where('candidate_requirements_id', $candidateRequirements->id)->delete();
        foreach ($areaOfExperience as $item) {
            DB::table('candidate_requirements_area_of_experience')->insert(
                [
                    'candidate_requirements_id' => $candidateRequirements->id,
                    'area_of_experience_id' => $item
                ]
            );
        }
    }

    /**
     * @param CandidateRequirement $candidateRequirements
     * @param array $areaOfBusiness
     */
    public function syncWithAreaOfBusiness(CandidateRequirement $candidateRequirements, array $areaOfBusiness)
    {
        DB::table('candidate_requirements_area_of_business')->where('candidate_requirements_id', $candidateRequirements->id)->delete();
        foreach ($areaOfBusiness as $item) {
            DB::table('candidate_requirements_area_of_business')->insert(
                [
                    'candidate_requirements_id' => $candidateRequirements->id,
                    'area_of_business_id' => $item
                ]
            );
        }
    }

    /**
     * @param CandidateRequirement $candidateRequirements
     * @param array $skills
     */
    public function syncWithSkills(CandidateRequirement $candidateRequirements, array $skills)
    {
        DB::table('candidate_requirements_skills')->where('candidate_requirements_id', $candidateRequirements->id)->delete();
        foreach ($skills as $item) {
            DB::table('candidate_requirements_skills')->insert(
                [
                    'candidate_requirements_id' => $candidateRequirements->id,
                    'skills_id' => $item
                ]
            );
        }
    }

    /**
     * @param CandidateRequirement $candidateRequirements
     * @param array $gender
     */
    public function syncWithGender(CandidateRequirement $candidateRequirements, array $gender)
    {
        DB::table('candidate_requirements_gender')->where('candidate_requirements_id', $candidateRequirements->id)->delete();
        foreach ($gender as $item) {
            DB::table('candidate_requirements_gender')->insert(
                [
                    'candidate_requirements_id' => $candidateRequirements->id,
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
        $rules = [
            "job_id" => [
                "required",
                "exists:additional_job_information,job_id,deleted_at,NULL",
            ],
            "degrees" => [
                "nullable",
                "array",
                "max:24"
            ],
            "degrees.*.education_level" => [
                "nullable",
                "exists:education_levels,education_level_id,deleted_at,NULL",
            ],
            "degrees.*.edu_group" => [
                "nullable",
                "exists:edu_groups,id,deleted_at,NULL",
            ],
            "degrees.*.edu_major" => [
                "nullable",
                "string"
            ],
            "preferred_educational_institution" => [
                "nullable",
                "array"
            ],
            "preferred_educational_institution.*" => [
                "exists:edu_institutions,id,deleted_at,NULL",
                "numeric",
            ],
            "other_educational_qualification" => [
                "nullable",
                "string",
            ],
            "training" => [
                "nullable",
                "array"
            ],
            "training.*" => [
                "string",
            ],
            "professional_certification" => [
                "nullable",
                "array"
            ],
            "professional_certification.*" => [
                "string",
            ],
            "experience" => [
                "nullable",
                "array"
            ],
            "experience.minimum_year_of_experience" => [
                "nullable",
                "numeric",
                "min:0",
                "max:50"
            ],
            "experience.maximum_year_of_experience" => [
                "nullable",
                "numeric",
                "min:1",
                "max:50"
            ],
            "experience.freshers" => [
                "nullable",
                "numeric",
                Rule::in(array_keys(BaseModel::BOOLEAN_FLAG))
            ],
            "experience.area_of_experience" => [
                "nullable",
                "array",
            ],
            "experience.area_of_experience.*" => [
                "exists:skills,id,deleted_at,NULL",
            ],
            "experience.area_of_business" => [
                "nullable",
                "array",
            ],
            "experience.area_of_business.*" => [
                "exists:area_of_business,id,deleted_at,NULL",
            ],
            "skills" => [
                "nullable",
                "array",
            ],
            "skills.*" => [
                "exists:skills,id,deleted_at,NULL",
            ],
            "additional_requirements" => [
                "nullable",
                "string"
            ],
            "gender" => [
                "nullable",
                "array"
            ],
            "gender.*" => [
                Rule::in(array_keys(BaseModel::GENDERS))
            ],
            "age_minimum" => [
                "nullable",
                "numeric",
                "min:14",
                "max:90"
            ],
            "age_maximum" => [
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
        return Validator::make($request->all(), $rules);
    }

}
