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

$router->post('login', 'AuthController@login');
$router->post('signup', 'AuthController@signup');
$router->post('guest', 'AuthController@guest');
$router->post('refresh', 'AuthController@refresh');

$router->group([ 'middleware' => 'auth' ], function() use ($router) {
    $router->get('/me', 'AuthController@me');

    $router->get('notes/me', 'NotesController@me');

    // workspaces
    $router->group([ 'prefix' => 'workspaces'], function () use ($router) {
        $router->get('list', 'WorkspacesController@list');
        $router->post('create', 'WorkspacesController@create');
        $router->get('{workspace}/roles', 'WorkspacesController@roles');
        $router->get('{workspace}/members', 'WorkspacesController@members');

        $router->group([ 'prefix' => '{workspace}/notes' ], function () use ($router) {
            $router->get('me', 'NotesController@me');
            $router->get('{note}', 'NotesController@show');
            $router->post('create', 'NotesController@create');
            $router->put('{note}', 'NotesController@writeInto');
            $router->delete('{note}', 'NotesController@delete');
        });
    });
});
