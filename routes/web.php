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

        /** industry registration/industryAssociation membership approval   */
        $router->put("registration-or-membership-approval/{organizationId}", ["as" => "IndustryAssociation.industry-associations-membership-approval", "uses" => "IndustryAssociationController@registrationOrMembershipApproval"]);

        /** industry registration/industryAssociation membership rejection  */
        $router->put("registration-or-membership-rejection/{organizationId}", ["as" => "IndustryAssociation.industry-associations-membership-rejection", "uses" => "IndustryAssociationController@registrationOrMembershipRejection"]);

        $router->get('organization-admin-profile', ['as' => 'organization.admin-profile', 'uses' => 'OrganizationController@getOrganizationAdminProfile']);

        $router->put("industry-association-admin-profile-update", ["as" => "public.organizations", "uses" => "IndustryAssociationController@updateIndustryAssociationAdminProfile"]);
        $router->get("industry-association-admin-profile", ["as" => "public.organizations", "uses" => "IndustryAssociationController@getIndustryAssociationAdminProfile"]);

        $router->put('organization-admin-profile-update', ['as' => 'organization.admin-profile-update', 'uses' => 'OrganizationController@updateOrganizationAdminProfile']);


        $router->get("industry-association-members", ["as" => "industry-association-members", "uses" => "IndustryAssociationController@getIndustryAssociationMemberList"]);

    });


    $router->group(['prefix' => 'public', 'as' => 'public'], function () use ($router) {
        $router->get("publications", ["as" => "public.publications", "uses" => "PublicationController@getPublicPublicationList"]);
        $router->get("industry-association-members", ["as" => "public.industry-association-members", "uses" => "IndustryAssociationController@getPublicIndustryAssociationMemberList"]);
        $router->get("industry-association-member-details/{industryId}", ["as" => "public.industry-association-member-details", "uses" => "IndustryAssociationController@getPublicIndustryAssociationMemberDetails"]);
    });


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
