<?php

Route::name('facebook-pixel.')
    ->prefix('/facebook-pixel')
    ->middleware(['web'])
    ->namespace('MicroweberPackages\Modules\FacebookPixel\Http\Controllers')
    ->group(function () {

        Route::get('/export-feed', 'FacebookPixelExportController@index')->name('export-feed');

        Route::get('/faawf', function () {

           // event($event = new \MicroweberPackages\Order\Events\OrderWasCreated(\MicroweberPackages\Order\Models\Order::find(9), []));

            event(new \MicroweberPackages\SiteStats\Events\PingStatsEvent([
                'referer'=>site_url(),
            ]));

        });

    });
