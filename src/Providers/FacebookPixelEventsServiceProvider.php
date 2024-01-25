<?php

namespace MicroweberPackages\Modules\FacebookPixel\Providers;

use Illuminate\Foundation\Support\Providers\EventServiceProvider;
use MicroweberPackages\Modules\FacebookPixel\Listeners\FacebookPixelPingStatsListener;
use MicroweberPackages\SiteStats\Events\PingStatsEvent;

class FacebookPixelEventsServiceProvider extends EventServiceProvider
{
    protected $listen = [
        PingStatsEvent::class => [
            FacebookPixelPingStatsListener::class
        ]
    ];
}
