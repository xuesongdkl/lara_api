<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It is a breeze. Simply tell Lumen the URIs it should respond to
| and give it the Closure to call when that URI is requested.
|
*/

$router->get('/', function () use ($router) {
    return $router->app->version();
});

$router->post('/a', function () use ($router) {
    echo 111111;
});

$router->get('/user/login','User\IndexController@login');
$router->get('/user/center','User\IndexController@uCenter');
//防刷
$router->get('/user/order','User\IndexController@order');

//登录授权
$router->post('/usera','User\IndexController@dologin');
