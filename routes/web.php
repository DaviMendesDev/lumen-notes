<?php

/** @var \Laravel\Lumen\Routing\Router $router */

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all the routes for an application.
| It is a breeze. Simply tell Lumen the URIs it should respond to
| and give it the Closure to call when that URI is requested.
|
*/

$router->get('/', function () use ($router) {
    return $router->app->version();
});

$router->post('/login', 'AuthController@login');
$router->post('/signup', 'AuthController@signup');
$router->post('/guest', 'AuthController@guest');

$router->group([ 'middleware' => 'auth' ], function() use ($router) {
    // notes
    $router->group([ 'prefix' => 'notes' ], function () use ($router) {
        $router->get('me', 'NotesController@me');
        $router->post('create', 'NotesController@create');
        $router->put('{note}', 'NotesController@update');
        $router->delete('{note}', 'NotesController@delete');
    });
});
