<?php

/** @var \Laravel\Lumen\Routing\Router $router */

use App\Helpers\Classes\CustomRouter;

$customRouter = function (string $as = '') use ($router) {
    $custom = new CustomRouter($router);
    return $custom->as($as);
};


$router->group(['prefix' => 'api/v1', 'as' => 'api.v1'], function () use ($router, $customRouter) {
    $router->get('/', ['as' => 'api-info', 'uses' => 'ApiInfoController@apiInfo']);
    $customRouter()->resourceRoute('ranks', 'RankController')->render();
    $customRouter()->resourceRoute('rank-types', 'RankTypeController')->render();
    $customRouter()->resourceRoute('job-sectors', 'JobSectorController')->render();
    $customRouter()->resourceRoute('skills', 'SkillController')->render();
    $customRouter()->resourceRoute('occupations', 'OccupationController')->render();
    $customRouter()->resourceRoute('organization-types', 'OrganizationTypeController')->render();
    $customRouter()->resourceRoute('organizations', 'OrganizationController')->render();
    $customRouter()->resourceRoute('organization-unit-types', 'OrganizationUnitTypeController')->render();
    $customRouter()->resourceRoute('human-resource-templates', 'HumanResourceTemplateController')->render();
    $customRouter()->resourceRoute('human-resources', 'HumanResourceController')->render();
    $customRouter()->resourceRoute('services', 'ServiceController')->render();
    $customRouter()->resourceRoute('organization-units', 'OrganizationUnitController')->render();


    $router->get('organization-unit-types/{id}/get-hierarchy', ['as' => 'organization-unit-types.hierarchy', 'uses' => 'OrganizationUnitTypeController@getHierarchy']);
    $router->get('organization-units/{id}/get-hierarchy', ['as' => 'organization-units.hierarchy', 'uses' => 'OrganizationUnitController@getHierarchy']);

    //Assign services to organization unit
    $router->post('organization-units/{id}/assign-service-to-organization-unit', ['as' => 'organization-units.assign-service-to-organization-unit', 'uses' => 'OrganizationUnitController@assignServiceToOrganizationUnit']);

    $router->get('organization-units-trashed-data', ['as' => 'organization-units.get-trashed-data', 'uses' => 'OrganizationUnitController@getTrashedData']);
    $router->get('organization-units-restore-data/{id}', ['as' => 'organization-units.restore-data', 'uses' => 'OrganizationUnitController@restore']);
    $router->get('organization-units-force-delete/{id}', ['as' => 'organization-units.restore-data', 'uses' => 'OrganizationUnitController@forceDelete']);

});
