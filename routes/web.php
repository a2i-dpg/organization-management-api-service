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
    /** IndustryAssociation Registration Approval */
    $router->put("industry-association-registration-approval/{industryAssociationId}", ["as" => "IndustryAssociation.industry-associations-registration-approval", "uses" => "IndustryAssociationController@industryAssociationRegistrationApproval"]);
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
        $router->put("reject-hr-demand-youth/{hr_demand_youth_id}", ["as" => "reject.hr.demand.youth", "uses" => "HrDemandYouthController@rejectHrDemandYouth"]);


        $router->get('organization-unit-types/{id}/get-hierarchy', ['as' => 'organization-unit-types.hierarchy', 'uses' => 'OrganizationUnitTypeController@getHierarchy']);
        $router->get('organization-units/{id}/get-hierarchy', ['as' => 'organization-units.hierarchy', 'uses' => 'OrganizationUnitController@getHierarchy']);

        /** Assign Service to Organization Unit */
        $router->post('organization-units/{id}/assign-service-to-organization-unit', ['as' => 'organization-units.assign-service-to-organization-unit', 'uses' => 'OrganizationUnitController@assignServiceToOrganizationUnit']);


        /** IndustryAssociation Registration Rejection */
        $router->put("industry-association-registration-rejection/{industryAssociationId}", ["as" => "IndustryAssociation.industry-associations-registration-rejection", "uses" => "IndustryAssociationController@industryAssociationRegistrationRejection"]);

        /** Industry apply for industryAssociation membership */
        $router->post("industry-association-membership-application", ["as" => "organizations.industry-associations-membership-application", "uses" => "OrganizationController@IndustryAssociationMembershipApplication"]);

        /** industry registration approval   */
        $router->put("organization-user-approval/{organizationId}", ["as" => "organization.organization-user-approval", "uses" => "OrganizationController@organizationUserApproval"]);

        /** industry registration rejection  */
        $router->put("organization-user-rejection/{organizationId}", ["as" => "organization.organization-user-rejection", "uses" => "OrganizationController@organizationUserRejection"]);

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
        $router->get("industry-association-dashboard-statistics", ["as" => "industry-association-dashboard-statistics", "uses" => "IndustryAssociationController@industryAssociationDashboardStatistics"]);
        //$router->get("industry-association-members/{industryId}", ["as" => "industry-association-member-details", "uses" => "IndustryAssociationController@industryAssociationMemberDetails"]);

        $router->get("organization-dashboard-statistics", ["as" => "organization-dashboard-statistics", "uses" => "OrganizationController@organizationDashboardStatistics"]);

        /** job management routes */
        $router->group(["prefix" => "jobs", "as" => "jobs"], function () use ($customRouter, $router) {
            $router->get('/', ["as" => "job-list", "uses" => "JobManagementController@getJobList"]);
            $router->get('industry-association-member-jobs', ["as" => "industry-association-members-job-list", "uses" => "JobManagementController@getIndustryAssociationMembersJobList"]);
            $router->get("job-id", ["as" => "job-id", "uses" => "PrimaryJobInfoController@getJobId"]);
            $router->get("job-location", ["as" => "job-location", "uses" => "AdditionalJobInfoController@jobLocation"]);

            $router->get('job-preview/{jobId}', ["as" => "job-preview", "uses" => "JobManagementController@jobPreview"]);
            $router->get('other-benefits', ["as" => "other_benefits", "uses" => "JobManagementController@getOtherBenefits"]);
            $router->post("status-change/{jobId}", ["as" => "jobs.status-change", "uses" => "PrimaryJobInfoController@jobStatusChange"]);
            $router->post("show-in-landing-page-status-change", ["as" => "jobs.show-in-landing-page-status-change", "uses" => "JobManagementController@showInLandingPageStatusChange"]);

            $router->post("primary-job-information", ["as" => "store-primary-job-information", "uses" => "PrimaryJobInfoController@storePrimaryJobInformation"]);
            $router->get("primary-job-information/{jobId}", ["as" => "get-primary-job-information", "uses" => "PrimaryJobInfoController@getPrimaryJobInformation"]);

            $router->post("additional-job-information", ["as" => "store-additional-job-information", "uses" => "AdditionalJobInfoController@storeAdditionalJobInformation"]);
            $router->get("additional-job-information/{jobId}", ["as" => "get-additional-job-information", "uses" => "AdditionalJobInfoController@getAdditionalJobInformation"]);

            $router->post("candidate-requirements", ["as" => "store-candidate-requirements", "uses" => "CandidateRequirementController@storeCandidateRequirements"]);
            $router->get("candidate-requirements/{jobId}", ["as" => "get-candidate-requirements", "uses" => "CandidateRequirementController@getCandidateRequirements"]);

            $router->post("company-info-visibility", ["as" => "store-company-info-visibility", "uses" => "CompanyInfoVisibilityController@storeCompanyInfoVisibility"]);
            $router->get("company-info-visibility/{jobId}", ["as" => "get-company-info-visibility", "uses" => "CompanyInfoVisibilityController@getCompanyInfoVisibility"]);

            $router->post("matching-criteria", ["as" => "store-matching-criteria", "uses" => "MatchingCriteriaController@storeMatchingCriteria"]);
            $router->get("matching-criteria/{jobId}", ["as" => "get-matching-criteria", "uses" => "MatchingCriteriaController@getMatchingCriteria"]);

            $router->post('contact-information', ["as" => "contact-information.store", "uses" => "JobContactInformationController@storeContactInformation"]);
            $router->get('contact-information/{jobId}', ["as" => "contact-information.get", "uses" => "JobContactInformationController@getContactInformation"]);

            /** step schedule routes */
            $router->get('step-schedule/{id}', ["as" => "step-schedule.get", "uses" => "JobManagementController@getOneSchedule"]);
            $router->post('step-schedule', ["as" => "step-schedule.post", "uses" => "JobManagementController@createSchedule"]);
            $router->put('step-schedule/{id}', ["as" => "step-schedule.put", "uses" => "JobManagementController@updateSchedule"]);
            $router->delete('step-schedule/{id}', ["as" => "step-schedule.delete", "uses" => "JobManagementController@destroySchedule"]);

            $router->put('step-schedule/{scheduleId}/assign', ["as" => "step-schedule.assign", "uses" => "JobManagementController@assignCandidateToInterviewSchedule"]);
            $router->put('step-schedule/{scheduleId}/unassign', ["as" => "step-schedule.unassign", "uses" => "JobManagementController@removeCandidateFromInterviewSchedule"]);

            $router->get('candidate/{applicationId}', ["as" => "candidate.view", "uses" => "JobManagementController@getCandidateProfile"]);

            /** Update candidate status in interview steps  */
            $router->group(["prefix" => "candidate", "as" => "candidate-update"], function () use ($router) {
                $router->put('/{applicationId}/reject', ["as" => "candidate-update.reject", "uses" => "JobManagementController@rejectCandidate"]);
                $router->put('/{applicationId}/shortlist', ["as" => "candidate-update.shortList", "uses" => "JobManagementController@shortlistCandidate"]);
                $router->put('/{applicationId}/interviewed', ["as" => "candidate-update.interviewed", "uses" => "JobManagementController@updateInterviewedCandidate"]);
                $router->put('/{applicationId}/remove', ["as" => "candidate-update.remove", "uses" => "JobManagementController@removeCandidateToPreviousStep"]);
                $router->put('/{applicationId}/restore', ["as" => "candidate-update.remove", "uses" => "JobManagementController@restoreRejectedCandidate"]);
                $router->put('/{applicationId}/hire-invite', ["as" => "candidate-update.hire-invite", "uses" => "JobManagementController@hireInviteCandidate"]);
                $router->put('/{applicationId}/hired', ["as" => "candidate-update.hired", "uses" => "JobManagementController@updateHiredCandidate"]);
            });

            /**recruitment step routes **/
            $router->get('recruitment-step/{stepId}', ["as" => "recruitment-steps.get", "uses" => "JobManagementController@getRecruitmentStep"]);
            $router->post('recruitment-step', ["as" => "recruitment-step.store", "uses" => "JobManagementController@createRecruitmentStep"]);
            $router->put('recruitment-step/{stepId}', ["as" => "recruitment-step.update", "uses" => "JobManagementController@updateRecruitmentStep"]);
            $router->delete('recruitment-step/{stepId}', ["as" => "recruitment-step.delete", "uses" => "JobManagementController@destroyRecruitmentStep"]);
            $router->get('recruitment-step/{id}/schedules', ["as" => "recruitment-steps.get-schedules", "uses" => "JobManagementController@stepSchedules"]);

            $router->get('recruitment-steps/{jobId}', ["as" => "recruitment-steps.get-list", "uses" => "JobManagementController@getRecruitmentStepList"]);
            $router->get('candidates/{jobId}', ["as" => "recruitment-step.candidate-list", "uses" => "JobManagementController@recruitmentStepcandidateList"]);

            /**
             * DEPRECATED.
             * USE GET recruitment-step-candidate-list/{jobId}
             **/
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


        /**
         * FourIR Project APIS
         **/
        $customRouter()->resourceRoute('guidelines', 'FourIRGuidelineController')->render();
        $customRouter()->resourceRoute('taglines', 'FourIRTaglineController')->render();
        $customRouter()->resourceRoute('initiatives', 'FourIRInitiativeController')->render();
        $customRouter()->resourceRoute('team-members', 'FourIRInitiativeTeamMemberController')->render();
        $customRouter()->resourceRoute('initiative-cells', 'FourIRInitiativeCellController')->render();
        $customRouter()->resourceRoute('tna-formats', 'FourIRInitiativeTnaFormatController')->render();
        $customRouter()->resourceRoute('initiative-cs', 'FourIRInitiativeCsController')->render();
        $customRouter()->resourceRoute('initiative-curriculums', 'FourIRInitiativeCurriculumController')->render();
        $customRouter()->resourceRoute('initiative-cblms', 'FourIRInitiativeCblmController')->render();
        $customRouter()->resourceRoute('resource-managements', 'FourIRResourceController')->render();
        $customRouter()->resourceRoute('tots', 'FourIRInitiativeTotController')->render();
        $customRouter()->resourceRoute('create-approve-courses', 'FourIRCreateApproveCourseController')->render();
        $customRouter()->resourceRoute('employments', 'FourIREmploymentController')->render();
        $customRouter()->resourceRoute('showcasing', 'FourIRShowcasingController')->render();
        $customRouter()->resourceRoute('initiative-analysis', 'FourIRInitiativeAnalysisController')->render();
        $customRouter()->resourceRoute('scale-up', 'FourIRScaleUpController')->render();
        $customRouter()->resourceRoute('4ir-occupations', 'FourIROccupationController')->render();
        $customRouter()->resourceRoute('assessments', 'FourIRAssessmentController')->render();
        $customRouter()->resourceRoute('sectors', 'FourIRSectorController')->render();


        $router->put('/set-team-launching-date', ["as" => "set.team.launching.date", "uses" => "FourIRInitiativeTeamMemberController@setTeamLaunchingDate"]);
        $router->put('/set-cell-launching-date', ["as" => "set.cell.launching.date", "uses" => "FourIRInitiativeCellController@setTeamLaunchingDate"]);
        $router->put('/approve-four-ir-course/{id}', ["as" => "approve.four.ir.course", "uses" => "FourIRCreateApproveCourseController@approveFourIrCourse"]);
        $router->get('/get-4ir-course-enrolled-youths', ["as" => "get.4ir.course.enrolled.youths", "uses" => "FourIREnrollmentApprovalController@getList"]);
        $router->get('/get-4ir-course-batches', ["as" => "get.4ir.course.batches", "uses" => "FourIRSkillDevelopmentController@getList"]);

        $router->post('/tots-update/{id}', ["as" => "update.4ir.tots", "uses" => "FourIRInitiativeTotController@fourIrTotupdate"]);
        /**
         * Four IR Excel imports
         */

        $router->post('/four-ir-initiatives-import-excel', ["as" => "four.ir.initiatives.import.excel", "uses" => "FourIRInitiativeController@bulkStoreByExcel"]);
        $router->get('/four-ir-initiatives-import-excel-format', ["as" => "four.ir.initiatives.import.excel", "uses" => "FourIRInitiativeController@bulkImporterExcelFormat"]);
        $router->put('/four-ir-initiatives-task-update/{id}', ["as" => "four.ir.initiatives.task.update", "uses" => "FourIRInitiativeController@taskAndSkillUpdate"]);

        /**
         * Four IR Assessment List
         */

        $router->get('/get-four-ir-youth-assessment-list/{fourIrInitiativeId}', ["as" => "get-four-ir-youth-assessment-list", "uses" => "FourIRAssessmentController@getList"]);

        /** Provide suggestions in drop downs */
        $router->group(["prefix" => "suggestions", "as" => "suggestions"], function () use ($router) {
            $router->get('education-levels', ["as" => "education-levels.get-list", "uses" => "EducationLevelController@getList"]);
            $router->get('exam-degrees', ["as" => "exam-degrees.get-list", "uses" => "ExamDegreeController@getList"]);
            $router->get('area-of-experiences', ['as' => 'area-of-experiences.get-list', 'uses' => 'JobManagementController@getAreaOfExperience']);

        });

        $router->post('/organization-import-excel', ["as" => "organization.import.excel", "uses" => "OrganizationController@bulkStoreByExcel"]);
        $router->get("nascib-member/payment/pay-via-ssl/payment-gateway-page-url", ["as" => "pay-via-ssl.payment-gateway-page-url", "uses" => "NascibMemberPaymentController@getPaymentGatewayPageUrl"]);

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

        /** respond to job from youth service */
        $router->post("respond-to-job", ["as" => "service-to-service-call.respond-to-job", "uses" => "JobManagementController@respondToJob"]);

        /** get youth jobs from youth service */
        $router->get("youth-jobs", ["as" => "service-to-service-call.youth-jobs", "uses" => "JobManagementController@youthJobs"]);

        /** Youth Feed statistics job data fetch */
        $router->get('youth-feed-statistics/{youthId}', ["as" => "courses.youth-feed-statistics", "uses" => "JobManagementController@youthFeedStatistics"]);

        /** Fetch all recent jobs for youth feed API */
        $router->get('youth-feed-jobs', ["as" => "service-to-service-call.youth-feed-jobs", "uses" => "JobManagementController@youthFeedJobs"]);
    });


    $router->group(['prefix' => 'public', 'as' => 'public'], function () use ($router) {
        $router->get('job-details/{jobId}', ["as" => "job-details", "uses" => "JobManagementController@publicJobDetails"]);
        $router->get("publications/{id}", ["as" => "public.publication-read", "uses" => "PublicationController@clientSideRead"]);
        // $router->get("industry-association-members/{industryId}", ["as" => "public.industry-association-member-details", "uses" => "IndustryAssociationController@getPublicIndustryAssociationMemberDetails"]);
        $router->get("organizations/{id}", ["as" => "public.organization.details", "uses" => "OrganizationController@organizationDetails"]);
        $router->get("job-sectors", ["as" => "public.job-sectors", "uses" => "JobSectorController@getPublicJobSectorList"]);
        $router->get("occupations", ["as" => "public.occupations", "uses" => "OccupationController@getPublicOccupationList"]);
        $router->get("organization-types", ["as" => "public.organization-types", "uses" => "OrganizationTypeController@getPublicOrganizationTypeList"]);

        $router->get('area-of-business', ['as' => 'JobSector.AreaOfBusiness', 'uses' => 'JobManagementController@getAreaOfBusiness']);
        $router->get('area-of-experiences', ['as' => 'area-of-experiences.get-list', 'uses' => 'JobManagementController@getAreaOfExperience']);

        //public api by domain name identification
        $router->group(['middleware' => 'public-domain-handle'], function () use ($router) {
            $router->get('jobs', ["as" => "public.job-list", "uses" => "JobManagementController@getPublicJobList"]);
            $router->get("industry-association-details", ["as" => "public.industry-association.details", "uses" => "IndustryAssociationController@industryAssociationDetails"]);
            $router->get("contact-info", ["as" => "public.contact-info", "uses" => "ContactInfoController@getPublicContactInfoList"]);
            $router->get("publications", ["as" => "public.publications", "uses" => "PublicationController@getPublicPublicationList"]);
            $router->get("industry-association-members", ["as" => "public.industry-association-members", "uses" => "IndustryAssociationController@getPublicIndustryAssociationMemberList"]);

            /** Nascib Registration */
            $router->post('nascib-members/open-registration', ['as' => 'nascib-members.open-registration', 'uses' => 'NascibMemberController@openRegistration']);
            $router->get('nascib-members/get-static-data', ['as' => 'nascib-members.open-registration', 'uses' => 'NascibMemberController@nascibMemberStaticInfo']);

            /** Smef Registration */
            $router->post('smef-members/open-registration', ['as' => 'smef-members.open-registration', 'uses' => 'SmefMemberController@openRegistration']);
            $router->get('smef-members/get-static-data', ['as' => 'smef-members.open-registration', 'uses' => 'SmefMemberController@smefMemberStaticInfo']);

        });

        $router->group(['prefix' => 'nascib-members/payment', 'as' => 'nascib-members.payment'], function () use ($router) {
            $router->post("pay-via-ssl/pay-now", ["as" => "pay-via-ssl.pay-now", "uses" => "NascibMemberPaymentController@payViaSsl"]);
            $router->post("pay-via-ssl/success", ["as" => "pay-via-ssl.success", "uses" => "NascibMemberPaymentController@success"]);
            $router->post("pay-via-ssl/fail", ["as" => "pay-via-ssl.fail", "uses" => "NascibMemberPaymentController@fail"]);
            $router->post("pay-via-ssl/cancel", ["as" => "pay-via-ssl.cancel", "uses" => "NascibMemberPaymentController@cancel"]);
            $router->post("pay-via-ssl/ipn", ["as" => "pay-via-ssl.ipn", "uses" => "NascibMemberPaymentController@ipn"]);
        });

        $router->get("nise-statistics", ["as" => "nise-statistics", "uses" => "StatisticsController@niseStatistics"]);

    });


    /** List of trades */
    $router->get('trades', ['as' => 'trades.get-list', 'uses' => "TradeController@getList"]);

    /** List of trades */
    $router->get('sub-trades', ['as' => 'trades.get-list', 'uses' => "SubTradeController@getList"]);


    /** Industry Association open  Registration */
    $router->post("industry-association-registration", ["as" => "register.industryAssociation", "uses" => "IndustryAssociationController@industryAssociationOpenRegistration"]);


    /** Organization open Registration */
    $router->post("organization-registration", ["as" => "register.organization", "uses" => "OrganizationController@organizationOpenRegistration", 'middleware' => 'public-domain-handle']);

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

    $customRouter()->resourceRoute('contributions', 'FourIRContributionController')->render();
    $router->get('/get-4ir-certificate-list/{fourIrInitiativeId}', ["as" => "get-4ir-certificate-list", "uses" => "FourIRCertificateController@getCertificates"]);
});



