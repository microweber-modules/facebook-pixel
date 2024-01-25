<?php

namespace MicroweberPackages\Modules\FacebookPixel\Listeners;


use MicroweberPackages\Modules\FacebookPixel\DispatchFacebookPixelSideTracking;

class FacebookPixelPingStatsListener
{
    /**
     * Handle the event.
     */
    public function handle($event): void
    {
        $dispatchTracking = new DispatchFacebookPixelSideTracking();
        $dispatchTracking->dispatch();
    }

}
