<?php

/** @var \Laravel\Lumen\Routing\Router $router */

use App\Helpers\Classes\CustomRouter;

$customRouter = function (string $as = '') use ($router) {
    $custom = new CustomRouter($router);
    return $custom->as($as);
};

//$router->get('/', function () use ($router) {
//    return $router->app->version();
//});

$router->get('/', ['as' => 'api-info', 'uses' => 'ApiInfoController@apiInfo']);

$router->group(['prefix' => 'api/v1', 'as' => 'api.v1'], function () use ($router, $customRouter) {
    $customRouter()->resourceRoute('ranks', 'RankController')->render();
    $customRouter()->resourceRoute('rank-types', 'RankTypeController')->render();
    $customRouter()->resourceRoute('job-sectors', 'JobSectorController')->render();
    $customRouter()->resourceRoute('skills', 'SkillController')->render();
    $customRouter()->resourceRoute('occupations', 'OccupationController')->render();
    $customRouter()->resourceRoute('organization-types', 'OrganizationTypeController')->render();
    $customRouter()->resourceRoute('organizations', 'OrganizationController')->render();
    $customRouter()->resourceRoute('organization-unit-types', 'OrganizationUnitTypeController')->render();
    $router->get('organization-unit-types/{id}/get-hierarchy', ['as' => 'get-hierarchy', 'uses' => 'OrganizationUnitTypeController@getHierarchy']);
    $customRouter()->resourceRoute('human-resource-templates', 'HumanResourceTemplateController')->render();
    $customRouter()->resourceRoute('human-resources', 'HumanResourceController')->render();
    $customRouter()->resourceRoute('services', 'ServiceController')->render();
    $customRouter()->resourceRoute('organization-units', 'OrganizationUnitController')->render();
    $router->get('organization-units/{id}/get-hierarchy', ['as' => 'get-hierarchy', 'uses' => 'OrganizationUnitController@getHierarchy']);
    $customRouter()->resourceRoute('organization-unit-services', 'OrganizationUnitServiceController')->render();
//    $router->get('get-hierrarchy/id', ['as' => 'api-info', 'uses' => 'OrganizationUnitTypeController@getHierrarchy']);
});
