<?php
namespace MicroweberPackages\Modules\FacebookPixel\Listeners;


use MicroweberPackages\Modules\FacebookPixel\FacebookPixel;

class FacebookPixelPingStatsListener
{
    /**
     * Handle the event.
     */
    public function handle($event): void
    {

        $eventId = time();

        $fbPixel = new FacebookPixel();
        $fbPixel->setEventId($eventId);
//        $viewCartEvent = $fbPixel->viewCart([
//            'total'=>app()->cart_manager->total(),
//            'currency'=>get_currency_code(),
//            'products'=>app()->cart_manager->get()
//        ]);

//        $addToCartEvent = $fbPixel->addToCart([
//
//        ]);

        dd(1);



    }
}
