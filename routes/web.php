<?php

/** @var \Laravel\Lumen\Routing\Router $router */

$router->get('/hello', 'ExampleController@hateoasResponse');


$router->group( ['prefix'=>'api/v1' ,'as'=>'api.v1'], function() use($router){
    $router->get('/', function () use ($router) {
        return $router->app->version();
    });
});
