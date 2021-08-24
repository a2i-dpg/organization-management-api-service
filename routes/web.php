<?php

/** @var Router $router */

use App\Helpers\Classes\CustomRouter;
use Laravel\Lumen\Routing\Router;

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

    $router->post('organization-units/{id}/assign-service-to-organization-unit', ['as' => 'organization-units.assign-service-to-organization-unit', 'uses' => 'OrganizationUnitController@assignServiceToOrganizationUnit']);

    /** organizationType trash */
    $router->get('organization-types-trashed-data', ['as' => 'organization-types.get-trashed-data', 'uses' => 'OrganizationTypeController@getTrashedData']);
    $router->patch('organization-types-restore/{id}', ['as' => 'organization-types.restore', 'uses' => 'OrganizationTypeController@restore']);
    $router->delete('organization-types/{id}', ['as' => 'organization-types.force-delete', 'uses' => 'OrganizationTypeController@forceDelete']);

    /** organization trash */
    $router->get('organizations-trashed-data', ['as' => 'organizations.get-trashed-data', 'uses' => 'OrganizationController@getTrashedData']);
    $router->patch('organizations-restore/{id}', ['as' => 'organizations.restore', 'uses' => 'OrganizationController@restore']);
    $router->delete('organizations-force-delete/{id}', ['as' => 'organizations.force-delete', 'uses' => 'OrganizationController@forceDelete']);

    /** organizationUnitType trash */
    $router->get('organization-unit-types-trashed-data', ['as' => 'organization-unit-types.get-trashed-data', 'uses' => 'OrganizationUnitTypeController@getTrashedData']);
    $router->patch('organization-unit-types-restore{id}', ['as' => 'organization-unit-types.restore', 'uses' => 'OrganizationUnitTypeController@restore']);
    $router->delete('organization-unit-types-force-delete/{id}', ['as' => 'organization-unit-types.force-delete', 'uses' => 'OrganizationUnitTypeController@forceDelete']);

    /** organizationUnit trash */
    $router->get('organization-units-trashed-data', ['as' => 'organization-units.get-trashed-data', 'uses' => 'OrganizationUnitController@getTrashedData']);
    $router->patch('organization-units-restore/{id}', ['as' => 'organization-units.restore', 'uses' => 'OrganizationUnitController@restore']);
    $router->delete('organization-units-force-delete/{id}', ['as' => 'organization-units.force-delete', 'uses' => 'OrganizationUnitController@forceDelete']);

    /** skill trash */
    $router->get('skills-trashed-data', ['as' => 'skills.get-trashed-data', 'uses' => 'SkillController@getTrashedData']);
    $router->patch('skills-restore/{id}', ['as' => 'skills.restore', 'uses' => 'SkillController@restore']);
    $router->delete('skills-force-delete/{id}', ['as' => 'skills.force-delete', 'uses' => 'SkillController@forceDelete']);

    /**JobSector trash */
    $router->get('job-sectors-trashed-data', ['as' => 'skills.get-trashed-data', 'uses' => 'SkillController@getTrashedData']);
    $router->patch('job-sectors-restore/{id}', ['as' => 'skills.restore', 'uses' => 'SkillController@restore']);
    $router->delete('job-sectors-force-delete/{id}', ['as' => 'skills.force-delete', 'uses' => 'SkillController@forceDelete']);

    /**Occupation trash */
    $router->get('occupations-trashed-data', ['as' => 'occupations.get-trashed-data', 'uses' => 'OccupationController@getTrashedData']);
    $router->patch('occupations-restore/{id}', ['as' => 'occupations.restore', 'uses' => 'OccupationController@restore']);
    $router->delete('occupations-force-delete/{id}', ['as' => 'occupations.force-delete', 'uses' => 'OccupationController@forceDelete']);

    /**RankType trash */
    $router->get('rank-types-trashed-data', ['as' => 'rank-types.get-trashed-data', 'uses' => 'RankTypeController@getTrashedData']);
    $router->patch('rank-types-restore/{id}', ['as' => 'rank-types.restore', 'uses' => 'RankTypeController@restore']);
    $router->delete('rank-types-force-delete/{id}', ['as' => 'rank-types.force-delete', 'uses' => 'RankTypeController@forceDelete']);

    /**Rank trash */
    $router->get('ranks-trashed-data', ['as' => 'ranks.get-trashed-data', 'uses' => 'RankController@getTrashedData']);
    $router->patch('ranks-restore/{id}', ['as' => 'ranks.restore', 'uses' => 'RankController@restore']);
    $router->delete('ranks-force-delete/{id}', ['as' => 'ranks.force-delete', 'uses' => 'RankController@forceDelete']);

    /**Service trash */
    $router->get('services-trashed-data', ['as' => 'services.get-trashed-data', 'uses' => 'ServiceController@getTrashedData']);
    $router->patch('services-restore/{id}', ['as' => 'services.restore', 'uses' => 'ServiceController@restore']);
    $router->delete('services-force-delete/{id}', ['as' => 'services.force-delete', 'uses' => 'ServiceController@forceDelete']);

    /**HumanResourceTemplate trash */
    $router->get('human-resource-templates-trashed-data', ['as' => 'human-resource-templates.get-trashed-data', 'uses' => 'HumanResourceTemplateController@getTrashedData']);
    $router->patch('human-resource-templates-restore/{id}', ['as' => 'human-resource-templates.restore', 'uses' => 'HumanResourceTemplateController@restore']);
    $router->delete('human-resource-templates-force-delete/{id}', ['as' => 'human-resource-templates.force-delete', 'uses' => 'HumanResourceTemplateController@forceDelete']);


    /**HumanResource trash */
    $router->get('human-resources-trashed-data', ['as' => 'human-resources.get-trashed-data', 'uses' => 'HumanResourceController@getTrashedData']);
    $router->patch('human-resources-restore/{id}', ['as' => 'human-resources.restore', 'uses' => 'HumanResourceController@restore']);
    $router->delete('human-resources-force-delete/{id}', ['as' => 'human-resources.force-delete', 'uses' => 'HumanResourceController@forceDelete']);

});
