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


Route::group(['prefix' => 'v1'], function () {
    Route::post('auth/register', 'AuthController@register');
    Route::post('auth/login', 'AuthController@login');

    Route::group(['middleware' => 'token'], function () {
        Route::get('auth/logout', 'AuthController@logout');

        Route::apiResource('board', 'BoardController');
        Route::group(['prefix' => 'board/{board}'], function () {
            Route::post('member', 'BoardController@addMember');
            Route::delete('member/{member_id}', 'BoardController@removeMember');

            Route::apiResource('list', 'BoardListController');
            Route::group(['prefix' => 'list/{list_id}'], function ( ){
                Route::post('right', 'BoardListController@moveRight');
                Route::post('left', 'BoardListController@moveLeft');

                Route::apiResource('card', 'BoardCardController');
            });
        });

        Route::post('card/{card_id}/up', 'BoardCardController@moveUp');
        Route::post('card/{card_id}/down', 'BoardCardController@moveDown');
    });
});

Auth::routes();

Route::get('/home', 'HomeController@index')->name('home');

Auth::routes();

Route::get('/home', 'HomeController@index')->name('home');
