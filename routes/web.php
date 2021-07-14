<?php

/** @var \Laravel\Lumen\Routing\Router $router */

$router->get('/hello', 'ExampleController@hateoasResponse');

$router->get('/', function () use ($router) {
    return $router->app->version();
});


$router->group( ['prefix'=>'api/v1' ,'as'=>'api.v1'], function() use($router){



    //organization crud operation
    $router->get('/organizations', ['as'=>'organizations.getList','uses'=>'OrganizationController@getList']);
    $router->post('/organizations', ['as'=>'organizations.store','uses'=>'OrganizationController@store']);
    $router->get('/organizations/{id}', ['as'=>'organizations.read','uses'=>'OrganizationController@read']);
    $router->put('/organizations/{id}', ['as'=>'organizations.update', 'uses'=>'OrganizationController@update']);
    $router->delete('/organizations/{id}',['as'=>'organizations.destroy','uses'=> 'OrganizationController@destroy']);


    /**organizationType crud operation
     * */
    $router->get('/organizationtypes', ['as'=>'organizationtypes.getList','uses'=>'OrganizationTypeController@getList']);
    $router->get('/organizationtypes/{id}', ['as'=>'organizationtypes.read','uses'=>'OrganizationTypeController@read']);
    $router->post('/organizationtypes', ['as'=>'organizationtypes.store','uses'=>'OrganizationTypeController@store']);
    $router->put('/organizationtypes/{id}', ['as'=>'organizationtypes.update', 'uses'=>'OrganizationTypeController@update']);
    $router->delete('/organizationtypes/{id}',['as'=>'organizationtypes.destroy','uses'=> 'OrganizationTypeController@destroy']);

    //ranktypes crud operation
    $router->get('/ranktypes', ['as'=>'ranktypes.getList','uses'=>'RankTypeController@getList']);
    $router->get('/ranktypes/{id}', ['as'=>'ranktypes.read','uses'=>'RankTypeController@read']);
    $router->post('/ranktypes', ['as'=>'ranktypes.store','uses'=>'RankTypeController@store']);
    $router->put('/ranktypes/{id}', ['as'=>'ranktypes.update', 'uses'=>'RankTypeController@update']);
    $router->delete('/ranktypes/{id}',['as'=>'ranktypes.destroy','uses'=> 'RankTypeController@destroy']);


    //ranks crud operation
    $router->get('/ranks', ['as'=>'ranks.getList','uses'=>'RankController@getList']);
    $router->get('/ranks/{id}', ['as'=>'ranks.read','uses'=>'RankController@read']);
    $router->post('/ranks', ['as'=>'ranks.store','uses'=>'RankController@store']);
    $router->put('/ranks/{id}', ['as'=>'ranks.update', 'uses'=>'RankController@update']);
    $router->delete('/ranks/{id}',['as'=>'ranks.destroy','uses'=> 'RankController@destroy']);


    //jobsectors crud operation
    $router->get('/jobsectors', ['as'=>'jobsectors.getList','uses'=>'JobSectorController@getList']);
    $router->get('/jobsectors/{id}', ['as'=>'jobsectors.read','uses'=>'JobSectorController@read']);
    $router->post('/jobsectors', ['as'=>'jobsectors.store','uses'=>'JobSectorController@store']);
    $router->put('/jobsectors/{id}', ['as'=>'jobsectors.update', 'uses'=>'JobSectorController@update']);
    $router->delete('/jobsectors/{id}',['as'=>'jobsectors.destroy','uses'=> 'JobSectorController@destroy']);

    //skills crud operation
    $router->get('/skills', ['as'=>'skills.getList','uses'=>'SkillController@getList']);
    $router->get('/skills/{id}', ['as'=>'skills.read','uses'=>'SkillController@read']);
    $router->post('/skills', ['as'=>'skills.store','uses'=>'SkillController@store']);
    $router->put('/skills/{id}', ['as'=>'skills.update', 'uses'=>'SkillController@update']);
    $router->delete('/skills/{id}',['as'=>'skills.destroy','uses'=> 'SkillController@destroy']);


    //occupation crud api
    $router->get('/occupations', ['as'=>'occupations.getList','uses'=>'OccupationController@getList']);
    $router->get('/occupations/{id}', ['as'=>'occupations.read','uses'=>'OccupationController@read']);
    $router->post('/occupations', ['as'=>'occupations.store','uses'=>'OccupationController@store']);
    $router->put('/occupations/{id}', ['as'=>'occupations.update', 'uses'=>'OccupationController@update']);
    $router->delete('/occupations/{id}',['as'=>'occupations.destroy','uses'=> 'OccupationController@destroy']);

});
