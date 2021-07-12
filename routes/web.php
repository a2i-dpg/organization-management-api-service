<?php

/** @var \Laravel\Lumen\Routing\Router $router */

$router->get('/hello', 'ExampleController@hateoasResponse');

$router->get('/', function () use ($router) {
    return $router->app->version();
});


$router->group( ['prefix'=>'api/v1' ,'as'=>'api.v1'], function() use($router){
    //organizations crud operation
    $router->get('/organizations', ['as'=>'organizations.viewAll','uses'=>'OrganizationController@viewAll']);
    $router->post('/organizations', ['as'=>'organizations.store','uses'=>'OrganizationController@store']);
    $router->get('/organizations/{id}', ['as'=>'organizations.view','uses'=>'OrganizationController@view']);
    $router->put('/organizations/{id}', ['as'=>'organizations.update', 'uses'=>'OrganizationController@update']);
    $router->delete('/organizations/{id}',['as'=>'organizations.destroy','uses'=> 'OrganizationController@destroy']);


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
});
