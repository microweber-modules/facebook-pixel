<?php

namespace MicroweberPackages\Modules\FacebookPixel;

include_once __DIR__ . '../../vendor/autoload.php';
use AlexWestergaard\PhpGa4\Analytics;
use AlexWestergaard\PhpGa4\Event\AddPaymentInfo;
use AlexWestergaard\PhpGa4\Event\AddShippingInfo;
use AlexWestergaard\PhpGa4\Event\AddToCart;
use AlexWestergaard\PhpGa4\Event\BeginCheckout;
use AlexWestergaard\PhpGa4\Event\Login;
use AlexWestergaard\PhpGa4\Event\PageView;
use AlexWestergaard\PhpGa4\Event\Purchase;
use AlexWestergaard\PhpGa4\Event\Signup;
use AlexWestergaard\PhpGa4\Item;
use FacebookAds\Api;
use FacebookAds\Logger\CurlLogger;
use FacebookAds\Object\ServerSide\ActionSource;
use FacebookAds\Object\ServerSide\Content;
use FacebookAds\Object\ServerSide\CustomData;
use FacebookAds\Object\ServerSide\DeliveryCategory;
use FacebookAds\Object\ServerSide\Event;
use FacebookAds\Object\ServerSide\EventRequest;
use FacebookAds\Object\ServerSide\UserData;
use Illuminate\Support\Facades\Session;
use MicroweberPackages\Modules\SiteStats\DTO\GA4Events\Conversion;
use MicroweberPackages\SiteStats\Models\StatsEvent;
use MicroweberPackages\SiteStats\UtmVisitorData;

class DispatchFacebookPixelSideTracking
{
    public function dispatch()
    {
        $visitorId = 0;
        $getUtmVisitorData = UtmVisitorData::getVisitorData();
        if (isset($getUtmVisitorData['utm_visitor_id'])) {
            $visitorId = $getUtmVisitorData['utm_visitor_id'];
        }

        $eventId = time() . rand(111, 999);
        $accessToken = get_option('facebook_access_token', 'facebook_pixel');
        $pixelId = get_option('facebook_pixel_id', 'facebook_pixel');
        $testEventCode = get_option('facebook_test_event_code', 'facebook_pixel');

        $getStatsEvents = StatsEvent::where('is_sent', null)
            ->where('utm_visitor_id', $visitorId)
            ->get();

        if ($getStatsEvents->count() > 0) {

            $api = Api::init(null, null, $accessToken);
            $api->setLogger(new CurlLogger());

            foreach ($getStatsEvents as $getStatsEvent) {

                $eventData = json_decode($getStatsEvent->event_data, true);

                if ($getStatsEvent->event_action == 'ADD_TO_CART') {

                    $product = $eventData['items'][0];

                    $userData = (new UserData())
                        //->setEmails(array('joe@eg.com'))
                        // ->setPhones(array('12345678901', '14251234567'))
                        // It is recommended to send Client IP and User Agent for Conversions API Events.
                        ->setExternalId(Session::getId())
                        ->setClientIpAddress($_SERVER['REMOTE_ADDR'])
                        ->setClientUserAgent($_SERVER['HTTP_USER_AGENT']);

                    $content = (new Content())
                        ->setProductId($product['id'])
                        ->setTitle($product['name'])
                        ->setItemPrice($product['price'])
                        ->setQuantity($product['quantity'])
                        ->setDeliveryCategory(DeliveryCategory::HOME_DELIVERY);

                    $customData = (new CustomData())
                        ->setContents(array($content))
                        ->setCurrency('BGN')
                        ->setValue($product['price']);

                    $event = (new Event())
                        ->setEventName('AddToCart')
                        ->setEventId($eventId)
                        ->setEventTime(time())
                        ->setEventSourceUrl(url()->current())
                        ->setUserData($userData)
                        ->setCustomData($customData)
                        ->setActionSource(ActionSource::WEBSITE);

                    $events = array();
                    array_push($events, $event);

                    $request = (new EventRequest($pixelId))
                        ->setTestEventCode($testEventCode)
                        ->setEvents($events);
                    $response = $request->execute();

                }

                if ($getStatsEvent->event_action == 'PURCHASE') {

                    $userData = (new UserData())
                        ->setEmails(array($eventData['customer']['email']))
                        ->setPhones(array($eventData['customer']['phone']))
                     //   ->setCity($purchaseData['address']['city'])
                       // ->setCountryCode($purchaseData['address']['countryCode'])
                        ->setFirstName($eventData['customer']['first_name'])
                        ->setLastName($eventData['customer']['last_name'])
                     //   ->setZipCode($purchaseData['address']['zip'])
                        // It is recommended to send Client IP and User Agent for Conversions API Events.
                        ->setExternalId(Session::getId())
                        ->setClientIpAddress($_SERVER['REMOTE_ADDR'])
                        ->setClientUserAgent($_SERVER['HTTP_USER_AGENT']);

                    $contents = [];
                    $contentIds = [];
                    foreach ($eventData['items'] as $product) {
                        $contentIds[] = $product['id'];
                        $contents[] = (new Content())
                            ->setProductId($product['id'])
                            ->setTitle($product['name'])
                            ->setItemPrice($product['price'])
                            ->setQuantity($product['quantity'])
                            ->setDeliveryCategory(DeliveryCategory::HOME_DELIVERY);
                    }

                    $customData = (new CustomData())
                        ->setContents($contents)
                        ->setContentIds($contentIds)
                        ->setCurrency($eventData['currency'])
                        ->setValue($eventData['total']);

                    $events = [];
                    $events[] = (new Event())
                        ->setEventName('Purchase')
                        ->setEventId($eventId)
                        ->setEventTime(time())
                        ->setEventSourceUrl(url()->current())
                        ->setUserData($userData)
                        ->setCustomData($customData)
                        ->setActionSource(ActionSource::WEBSITE);


                    $request = (new EventRequest($pixelId))
                        ->setTestEventCode($testEventCode)
                        ->setEvents($events);

                    $response = $request->execute();

                }

                $getStatsEvent->is_sent = 1;
                $getStatsEvent->save();
            }
        }

    }
}
