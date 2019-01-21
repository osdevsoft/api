<?php

/**
we are not really using {prefix} and {id}, we are getting this values as an array on BaseController::_call($uri_params) variable
 */

Route::group(
    ['prefix' => 'api/{prefix}'],
    function () {

        Route::post('/', [
            'uses' => 'Osds\Api\Framework\Laravel\LaravelController@upsert',
        ]);

        Route::get('/', [
            'uses' => 'Osds\Api\Framework\Laravel\LaravelController@get',
        ]);
        Route::get('/schema', [
            'uses' => 'Osds\Api\Framework\Laravel\LaravelController@getSchema',
        ]);

        Route::get('/{id}', [
            'uses' => 'Osds\Api\Framework\Laravel\LaravelController@get',
        ])->where('id', '[0-9]+');

        Route::post('/{id}', [
            'uses' => 'Osds\Api\Framework\Laravel\LaravelController@upsert',
        ])->where('id', '[0-9]+');

        Route::delete('/{id}', [
            'uses' => 'Osds\Api\Framework\Laravel\LaravelController@delete',
        ])->where('id', '[0-9]+');


    }


);