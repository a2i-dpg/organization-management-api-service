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

    //ranks crud operation
    $customRouter('ranks')->resourceRoute('ranks', 'RankController')->render();

    //ranktypes crud operation
    $customRouter('ranktypes')->resourceRoute('ranktypes', 'RankTypeController')->render();

    //jobsectors crud operation
    $customRouter('jobsectors')->resourceRoute('jobsectors', 'JobSectorController')->render();

    //skills crud operation
    $customRouter('skills')->resourceRoute('skills', 'SkillController')->render();

    //occupation crud api
    $customRouter('occupations')->resourceRoute('occupations', 'OccupationController')->render();


    //organizationsTypes crud operation
    $customRouter('organizationtypes')->resourceRoute('organizationtypes','OrganizationTypeController')->render();

    //organization crud operation
    $customRouter('organizations')->resourceRoute('organizations','OrganizationController')->render();



});
