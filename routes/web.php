<?php

/** @var Router $router */

use App\Helpers\Classes\CustomRouter;
use Laravel\Lumen\Routing\Router;

$customRouter = function (string $as = '') use ($router) {
    $custom = new CustomRouter($router);
    return $custom->as($as);
};

$router->get('/', function () use ($router) {
    return $router->app->version();
});


$router->group(['prefix' => 'api/v1', 'as' => 'api.v1'], function () use ($router, $customRouter) {

    /** Api info  */
    $router->get('/', ['as' => 'api-info', 'uses' => 'ApiInfoController@apiInfo']);

    /** Auth routes */
    $router->group(['middleware' => 'auth'], function () use ($customRouter, $router) {
        $customRouter()->resourceRoute('ranks', 'RankController')->render();
        $customRouter()->resourceRoute('rank-types', 'RankTypeController')->render();
        $customRouter()->resourceRoute('job-sectors', 'JobSectorController')->render();
        //$customRouter()->resourceRoute('skills', 'SkillController')->render(); //moved to youth service
        $customRouter()->resourceRoute('occupations', 'OccupationController')->render();
        $customRouter()->resourceRoute('organization-types', 'OrganizationTypeController')->render();
        $customRouter()->resourceRoute('organizations', 'OrganizationController')->render();
        $customRouter()->resourceRoute('organization-unit-types', 'OrganizationUnitTypeController')->render();
        $customRouter()->resourceRoute('human-resource-templates', 'HumanResourceTemplateController')->render();
        $customRouter()->resourceRoute('human-resources', 'HumanResourceController')->render();
        $customRouter()->resourceRoute('services', 'ServiceController')->render();
        $customRouter()->resourceRoute('organization-units', 'OrganizationUnitController')->render();
        $customRouter()->resourceRoute('publications', 'PublicationController')->render();
        $customRouter()->resourceRoute('industry-associations', 'IndustryAssociationController')->render();
        $customRouter()->resourceRoute('contact-info', 'ContactInfoController')->render();
        $customRouter()->resourceRoute('job-requirements', 'HrDemandController')->render();
        $customRouter()->resourceRoute('hr-demands', 'HrDemandInstituteController')->render();

        /** Hr demand approve by institute */
        $router->put("hr-demand-approved-by-institute/{id}", ["as" => "institute.hr-demand.approve", "uses" => "HrDemandInstituteController@hrDemandApprovedByInstitute"]);
        $router->put("hr-demand-rejected-by-institute/{id}", ["as" => "institute.hr-demand.reject", "uses" => "HrDemandInstituteController@hrDemandRejectedByInstitute"]);
        /** Hr demand approve by industry association */
        $router->put("hr-demand-approved-by-industry-association/{id}", ["as" => "industry-association.hr-demand.approve", "uses" => "HrDemandInstituteController@hrDemandApprovedByIndustryAssociation"]);
        $router->put("hr-demand-rejected-by-industry-association/{id}", ["as" => "industry-association.hr-demand.reject", "uses" => "HrDemandInstituteController@hrDemandRejectedByIndustryAssociation"]);
        /** Hr demand youths */
        $router->get("hr-demand-youths/{hr_demand_institute_id}", ["as" => "hr.demand.youths", "uses" => "HrDemandYouthController@getHrDemandYouths"]);


        $router->get('organization-unit-types/{id}/get-hierarchy', ['as' => 'organization-unit-types.hierarchy', 'uses' => 'OrganizationUnitTypeController@getHierarchy']);
        $router->get('organization-units/{id}/get-hierarchy', ['as' => 'organization-units.hierarchy', 'uses' => 'OrganizationUnitController@getHierarchy']);

        /** Assign Service to Organization Unit */
        $router->post('organization-units/{id}/assign-service-to-organization-unit', ['as' => 'organization-units.assign-service-to-organization-unit', 'uses' => 'OrganizationUnitController@assignServiceToOrganizationUnit']);

        /** IndustryAssociation Registration Approval */
        $router->put("industry-association-registration-approval/{industryAssociationId}", ["as" => "IndustryAssociation.industry-associations-registration-approval", "uses" => "IndustryAssociationController@industryAssociationRegistrationApproval"]);

        /** IndustryAssociation Registration Rejection */
        $router->put("industry-association-registration-rejection/{industryAssociationId}", ["as" => "IndustryAssociation.industry-associations-registration-rejection", "uses" => "IndustryAssociationController@industryAssociationRegistrationRejection"]);

        /** Industry apply for industryAssociation membership */
        $router->post("industry-association-membership-application", ["as" => "organizations.industry-associations-membership-application", "uses" => "OrganizationController@IndustryAssociationMembershipApplication"]);

        /** industry registration approval   */
        $router->put("organization-registration-approval/{organizationId}", ["as" => "organization.organization-registration-approval", "uses" => "OrganizationController@organizationRegistrationApproval"]);

        /** industry registration rejection  */
        $router->put("organization-registration-rejection/{organizationId}", ["as" => "organization.organization-registration-rejection", "uses" => "OrganizationController@organizationRegistrationRejection"]);

        /** Industry Association membership approval */
        $router->put("industry-association-membership-approval/{organizationId}", ["as" => "industry-association-approval", "uses" => "IndustryAssociationController@industryAssociationMembershipApproval"]);

        /** Industry Association membership rejection */
        $router->put("industry-association-membership-rejection/{organizationId}", ["as" => "industry-association-rejection", "uses" => "IndustryAssociationController@industryAssociationMembershipRejection"]);


        $router->get('organization-profile', ['as' => 'organization.admin-profile', 'uses' => 'OrganizationController@getOrganizationProfile']);
        $router->get('area-of-business', ['as' => 'JobSector.AreaOfBusiness', 'uses' => 'JobManagementController@getAreaOfBusiness']);
        $router->get('educational-institutions', ['as' => 'JobSector.EducationalInstitutions', 'uses' => 'JobManagementController@getEducationalInstitutions']);

        $router->put("industry-association-profile-update", ["as" => "public.organizations", "uses" => "IndustryAssociationController@updateIndustryAssociationProfile"]);
        $router->get("industry-association-profile", ["as" => "public.organizations", "uses" => "IndustryAssociationController@getIndustryAssociationProfile"]);
        $router->put('organization-profile-update', ['as' => 'organization.admin-profile-update', 'uses' => 'OrganizationController@updateOrganizationProfile']);
        $router->get("industry-association-members", ["as" => "industry-association-members", "uses" => "IndustryAssociationController@getIndustryAssociationMemberList"]);
        //$router->get("industry-association-members/{industryId}", ["as" => "industry-association-member-details", "uses" => "IndustryAssociationController@industryAssociationMemberDetails"]);


        /** job management routes */
        $router->group(["prefix" => "jobs", "as" => "jobs"], function () use ($router) {
            $router->get('/', ["as" => "job-list", "uses" => "JobManagementController@getJobList"]);

            $router->get("job-id", ["as" => "job-id", "uses" => "PrimaryJobInfoController@getJobId"]);
            $router->get("job-location", ["as" => "job-location", "uses" => "AdditionalJobInfoController@jobLocation"]);

            $router->get('job-preview/{jobId}', ["as" => "job-preview", "uses" => "JobManagementController@jobPreview"]);
            $router->get('other-benefits', ["as" => "other_benefits", "uses" => "JobManagementController@getOtherBenefits"]);
            $router->post("status-change/{jobId}", ["as" => "jobs.status-change", "uses" => "PrimaryJobInfoController@jobStatusChange"]);

            $router->post("store-primary-job-information", ["as" => "store-primary-job-information", "uses" => "PrimaryJobInfoController@storePrimaryJobInformation"]);
            $router->get("primary-job-information/{jobId}", ["as" => "get-primary-job-information", "uses" => "PrimaryJobInfoController@getPrimaryJobInformation"]);

            $router->post("store-additional-job-information", ["as" => "store-additional-job-information", "uses" => "AdditionalJobInfoController@storeAdditionalJobInformation"]);
            $router->get("additional-job-information/{jobId}", ["as" => "get-additional-job-information", "uses" => "AdditionalJobInfoController@getAdditionalJobInformation"]);

            $router->post("store-candidate-requirements", ["as" => "store-candidate-requirements", "uses" => "CandidateRequirementController@storeCandidateRequirements"]);
            $router->get("candidate-requirements/{jobId}", ["as" => "get-candidate-requirements", "uses" => "CandidateRequirementController@getCandidateRequirements"]);

            $router->post("store-company-info-visibility", ["as" => "store-company-info-visibility", "uses" => "CompanyInfoVisibilityController@storeCompanyInfoVisibility"]);
            $router->get("company-info-visibility/{jobId}", ["as" => "get-company-info-visibility", "uses" => "CompanyInfoVisibilityController@getCompanyInfoVisibility"]);

            $router->post("store-matching-criteria", ["as" => "store-matching-criteria", "uses" => "MatchingCriteriaController@storeMatchingCriteria"]);
            $router->get("matching-criteria/{jobId}", ["as" => "get-matching-criteria", "uses" => "MatchingCriteriaController@getMatchingCriteria"]);

            $router->post('contact-information', ["as" => "contact-information.store", "uses" => "JobContactInformationController@storeContactInformation"]);
            $router->get('contact-information/{jobId}', ["as" => "contact-information.get", "uses" => "JobContactInformationController@getContactInformation"]);


            $router->group(["prefix" => "step-schedule", "as" => "step-schedule"], function () use ($router) {
                $router->post('create', ["as" => "create-schedule", "uses" => "JobManagementController@createSchedule"]);
                $router->put('update/{id}', ["as" => "update-schedule", "uses" => "JobManagementController@updateSchedule"]);
                $router->put('update/{id}', ["as" => "update-schedule", "uses" => "JobManagementController@updateSchedule"]);
                $router->get('get/{id}', ["as" => "update-schedule", "uses" => "JobManagementController@getOneSchedule"]);
                $router->delete('delete/{id}', ["as" => "update-schedule", "uses" => "JobManagementController@destroySchedule"]);
            });

            /** Update candidate status in interview steps  */
            $router->group(["prefix" => "candidate-update", "as" => "candidate-update"], function () use ($router) {
                $router->put('reject/{applicationId}', ["as" => "candidate-update.reject", "uses" => "JobManagementController@rejectCandidate"]);
                $router->put('shortlist/{applicationId}', ["as" => "candidate-update.shortList", "uses" => "JobManagementController@shortlistCandidate"]);
                $router->put('interviewed/{applicationId}', ["as" => "candidate-update.interviewed", "uses" => "JobManagementController@updateInterviewedCandidate"]);
            });


            /**recruitment step routes **/
            $router->get('recruitment-steps/{stepId}', ["as" => "recruitment-steps.get", "uses" => "JobManagementController@getRecruitmentStep"]);
            $router->post('recruitment-steps/{jobId}', ["as" => "recruitment-step.store", "uses" => "JobManagementController@createRecruitmentStep"]);
            $router->put('recruitment-steps/{stepId}', ["as" => "recruitment-step.update", "uses" => "JobManagementController@updateRecruitmentStep"]);
            $router->delete('recruitment-steps/{stepId}', ["as" => "recruitment-step.delete", "uses" => "JobManagementController@destroyRecruitmentStep"]);


            $router->group(["prefix" => "candidates", "as" => "candidate-list"], function () use ($router) {
                $router->get('all/{jobId}', ["as" => "all", "uses" => "JobManagementController@getAllCandidateList"]);
                $router->get('applied/{jobId}', ["as" => "applied", "uses" => "JobManagementController@getAppliedCandidateList"]);
                $router->get('rejected/{jobId}', ["as" => "rejected", "uses" => "JobManagementController@getRejectedCandidateList"]);
                $router->get('shortlisted/{jobId}', ["as" => "shortlisted", "uses" => "JobManagementController@getShortlistedCandidateList"]);
                $router->get('interview-invited/{jobId}', ["as" => "interview-invited", "uses" => "JobManagementController@getInterviewInvitedCandidateList"]);
                $router->get('interviewed/{jobId}', ["as" => "interviewed", "uses" => "JobManagementController@getInterviewedCandidateList"]);
                $router->get('hire-invited/{jobId}', ["as" => "hire-invited", "uses" => "JobManagementController@getHireInvitedCandidateList"]);
                $router->get('hired/{jobId}', ["as" => "hired", "uses" => "JobManagementController@getHiredCandidateList"]);
            });
        });

        /** Provide suggestions in drop downs */
        $router->group(["prefix" => "suggestions", "as" => "suggestions"], function () use ($router) {
            $router->get('education-levels', ["as" => "education-levels.get-list", "uses" => "EducationLevelController@getList"]);
            $router->get('exam-degrees', ["as" => "exam-degrees.get-list", "uses" => "ExamDegreeController@getList"]);
            $router->get('area-of-experiences', ['as' => 'area-of-experiences.get-list', 'uses' => 'JobManagementController@getAreaOfExperience']);

        });
    });

    /** Service to service direct call without any authorization and authentication */
    $router->group(['prefix' => 'service-to-service-call', 'as' => 'service-to-service-call'], function () use ($router) {
        /** Single Organization Fetch  */
        $router->get("organizations/{id}", ["as" => "service-to-service-call.organization", "uses" => "OrganizationController@organizationDetails"]);

        /** Single Industry Association Fetch  */
        $router->get("industry-associations/{id}", ["as" => "service-to-service-call.industry-associations", "uses" => "IndustryAssociationController@industryAssociationDetails"]);

        /** Single Industry Association Code Fetch  */
        $router->get("industry-associations/{id}/get-code", ["as" => "service-to-service-call.industry-associations.get-code", "uses" => "IndustryAssociationController@getCode"]);

        /** apply to job from youth service */
        $router->post("apply-to-job", ["as" => "service-to-service-call.apply-to-job", "uses" => "JobManagementController@applyToJob"]);
    });


    $router->group(['prefix' => 'public', 'as' => 'public'], function () use ($router) {
        $router->get('jobs', ["as" => "public.job-list", "uses" => "JobManagementController@getPublicJobList"]);
        $router->get('job-details/{jobId}', ["as" => "job-details", "uses" => "JobManagementController@publicJobDetails"]);
        $router->get("publications/{id}", ["as" => "public.publication-read", "uses" => "PublicationController@clientSideRead"]);
        // $router->get("industry-association-members/{industryId}", ["as" => "public.industry-association-member-details", "uses" => "IndustryAssociationController@getPublicIndustryAssociationMemberDetails"]);
        $router->get("organizations/{id}", ["as" => "public.organization.details", "uses" => "OrganizationController@organizationDetails"]);
        $router->get("industry-associations/{id}", ["as" => "public.industry-association.details", "uses" => "IndustryAssociationController@industryAssociationDetails"]);
        $router->get("job-sectors", ["as" => "public.job-sectors", "uses" => "JobSectorController@getPublicJobSectorList"]);
        $router->get("occupations", ["as" => "public.occupations", "uses" => "OccupationController@getPublicOccupationList"]);
        $router->get("organization-types", ["as" => "public.organization-types", "uses" => "OrganizationTypeController@getPublicOrganizationTypeList"]);


        //public api by domain name identification
        $router->group(['middleware' => 'public-domain-handle'], function () use ($router) {
            $router->get("contact-info", ["as" => "public.contact-info", "uses" => "ContactInfoController@getPublicContactInfoList"]);
            $router->get("publications", ["as" => "public.publications", "uses" => "PublicationController@getPublicPublicationList"]);
            $router->get("industry-association-members", ["as" => "public.industry-association-members", "uses" => "IndustryAssociationController@getPublicIndustryAssociationMemberList"]);
        });

    });


    /** List of trades */
    $router->get('trades', ['as' => 'trades.get-list', 'uses' => "TradeController@getList"]);

    /** List of trades */
    $router->get('sub-trades', ['as' => 'trades.get-list', 'uses' => "SubTradeController@getList"]);


    /** Industry Association open  Registration */
    $router->post("industry-association-registration", ["as" => "register.industryAssociation", "uses" => "IndustryAssociationController@industryAssociationOpenRegistration"]);


    /** Organization open Registration */
    $router->post("organization-registration", ["as" => "register.organization", "uses" => "OrganizationController@organizationOpenRegistration"]);

    /** Organization Title by Ids for Internal Api */
    $router->post("get-organization-title-by-ids",
        [
            "as" => "organizations.get-organization-title-by-ids",
            "uses" => "OrganizationController@getOrganizationTitleByIds"
        ]
    );

    /** Industry Association Title by Ids for Internal Api */
    $router->post("get-industry-association-title-by-ids",
        [
            "as" => "organizations.get-industry-association-title-by-ids",
            "uses" => "OrganizationController@getIndustryAssociationTitleByIds"
        ]
    );


    /** TODO: Properly Organize Trashed Routes through CustomRouter */
    /** OrganizationType Trash */
    $router->get('organization-types-trashed-data', ['as' => 'organization-types.get-trashed-data', 'uses' => 'OrganizationTypeController@getTrashedData']);
    $router->patch('organization-types-restore/{id}', ['as' => 'organization-types.restore', 'uses' => 'OrganizationTypeController@restore']);
    $router->delete('organization-types-force-delete/{id}', ['as' => 'organization-types.force-delete', 'uses' => 'OrganizationTypeController@forceDelete']);

    /** Organization Trash */
    $router->get('organizations-trashed-data', ['as' => 'organizations.get-trashed-data', 'uses' => 'OrganizationController@getTrashedData']);
    $router->patch('organizations-restore/{id}', ['as' => 'organizations.restore', 'uses' => 'OrganizationController@restore']);
    $router->delete('organizations-force-delete/{id}', ['as' => 'organizations.force-delete', 'uses' => 'OrganizationController@forceDelete']);

    /** OrganizationUnitType Trash */
    $router->get('organization-unit-types-trashed-data', ['as' => 'organization-unit-types.get-trashed-data', 'uses' => 'OrganizationUnitTypeController@getTrashedData']);
    $router->patch('organization-unit-types-restore{id}', ['as' => 'organization-unit-types.restore', 'uses' => 'OrganizationUnitTypeController@restore']);
    $router->delete('organization-unit-types-force-delete/{id}', ['as' => 'organization-unit-types.force-delete', 'uses' => 'OrganizationUnitTypeController@forceDelete']);


});
