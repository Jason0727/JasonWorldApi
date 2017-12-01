<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application with dingo.
| It is a breeze. Simply tell Lumen the URIs it should respond to
| and give it the Closure to call when that URI is requested.
|
*/

$api = app('Dingo\Api\Routing\Router');

$api->version('v1',['namespace'=> 'App\Http\Controllers\Api',
    'middleware' => ['throttle:200,1','cors'],
],function ($api){

   

    //$api->group(['middleware' => 'auth'],function ($api){
    $api->group([],function ($api){

        /**
         * 角色Api
         */
        $api->get('role/{page_size}',['uses' => 'RoleController@index']);
        $api->post('role',['uses' => 'RoleController@insert']);
        $api->put('role',['uses' => 'RoleController@update']);
        $api->delete('role',['uses' => 'RoleController@delete']);
        $api->options('role',function (){});

       
        $api->group(['middleware' => 'jwt.refresh'],function ($api){

            $api->post('auth/refreshtoken',['uses' => 'AuthController@refreshToken']);
            $api->put('auth',['uses' => 'AuthController@resetPassword']);

        });
    });

    $api->post('auth',['uses' => 'AuthController@login']);
    $api->get('video',['uses' => 'AuthController@video']);
    $api->get('image',['uses' => 'AuthController@image']);
    $api->get('say',['uses' => 'AuthController@say']);
});
