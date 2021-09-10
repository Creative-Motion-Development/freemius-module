<?php

Route::group(['middleware' => 'web', 'prefix' => \Helper::getSubdirectory(), 'namespace' => 'Modules\Freemius\Http\Controllers'], function()
{
    Route::get('/', 'FreemiusController@index');
});
