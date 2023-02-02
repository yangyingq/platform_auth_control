<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

$router->group(['namespace' => 'Admin'], function($router){
    //系统模块
    $router->group(['prefix' => 'system', 'namespace' => 'system'], function ($router) {
        //角色模块
        $router->group(['prefix' => 'role'], function ($router) {
            $router->get('list', 'RoleController@list');
        });
    });
    $router->post('login', 'SiteController@login');
    $router->get('admin_info', 'SiteController@getAdminInfo');
    $router->post('logout', 'SiteController@logout');
});
