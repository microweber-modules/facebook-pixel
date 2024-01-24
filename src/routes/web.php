<?php

Route::name('facebook-pixel.')
    ->prefix('/facebook-pixel')
    ->middleware(['web'])
    ->namespace('MicroweberPackages\Modules\FacebookPixel\Http\Controllers')
    ->group(function () {

        Route::get('/export-feed', 'FacebookPixelExportController@index')->name('index');

    });
