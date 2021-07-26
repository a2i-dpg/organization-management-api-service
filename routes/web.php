<?php

/** @var \Laravel\Lumen\Routing\Router $router */

use App\Helpers\Classes\CustomRouter;

$customRouter = function (string $as = '') use ($router) {
    $custom = new CustomRouter($router);
    return $custom->as($as);
};


$router->get('/hello', 'ExampleController@hateoasResponse');

$router->get('/', function () use ($router) {
    return $router->app->version();
});


$router->group(['prefix' => 'api/v1', 'as' => 'api.v1'], function () use ($router, $customRouter) {

    $customRouter()->resourceRoute('ranks', 'RankController')->render();
    $customRouter()->resourceRoute('rank-types', 'RankTypeController')->render();
    $customRouter()->resourceRoute('job-sectors', 'JobSectorController')->render();
    $customRouter()->resourceRoute('skills', 'SkillController')->render();
    $customRouter()->resourceRoute('occupations', 'OccupationController')->render();
    $customRouter()->resourceRoute('organization-types', 'OrganizationTypeController')->render();
    $customRouter()->resourceRoute('organizations', 'OrganizationController')->render();
    $customRouter()->resourceRoute('organizations', 'OrganizationController')->render();
    $customRouter()->resourceRoute('organization-unit-types', 'OrganizationUnitTypeController')->render();
    $customRouter()->resourceRoute('human-resource-templates', 'HumanResourceTemplateController')->render();
    $customRouter()->resourceRoute('human-resources', 'HumanResourceController')->render();
});
