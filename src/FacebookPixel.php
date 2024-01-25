<?php

namespace MicroweberPackages\Modules\FacebookPixel;

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
use Illuminate\Support\Str;

class FacebookPixel
{
    public $eventId = null;
    protected $accessToken;
    protected $pixelId;
    protected $testEventCode;

    public function __construct()
    {
        $this->accessToken = get_option('facebook_access_token', 'facebook_pixel');
        $this->pixelId = get_option('facebook_pixel_id', 'facebook_pixel');
        $this->testEventCode = get_option('facebook_test_event_code', 'facebook_pixel');
    }

    public function setEventId($eventId)
    {
        $this->eventId = $eventId;
    }

    public function viewCart($cartData)
    {
        $api = Api::init(null, null, $this->accessToken);
        $api->setLogger(new CurlLogger());

        $userData = (new UserData())
            //->setEmails(array('joe@eg.com'))
            // ->setPhones(array('12345678901', '14251234567'))
            // It is recommended to send Client IP and User Agent for Conversions API Events.
            ->setExternalId(Session::getId())
            ->setClientIpAddress($_SERVER['REMOTE_ADDR'])
            ->setClientUserAgent($_SERVER['HTTP_USER_AGENT']);

        $contents = [];
        $contentIds = [];
        foreach ($cartData['products'] as $product) {
            $contentIds[] = $product['attributes']['id'];
            $contents[] = (new Content())
                ->setProductId($product['attributes']['id'])
                ->setTitle($product['name'])
                ->setItemPrice($product['priceNumeric'])
                ->setQuantity($product['quantity'])
                ->setDeliveryCategory(DeliveryCategory::HOME_DELIVERY);
        }

        $customData = (new CustomData())
           ->setContents($contents)
            ->setContentIds($contentIds)
            ->setCurrency('BGN')
            ->setValue($cartData['totalNumeric']);

        $event = (new Event())
            ->setEventName('ViewCart')
            ->setEventId($this->eventId)
            ->setEventTime(time())
            ->setEventSourceUrl(url()->current())
            ->setUserData($userData)
            ->setCustomData($customData)
            ->setActionSource(ActionSource::WEBSITE);

        $events = array();
        array_push($events, $event);

        $request = (new EventRequest($this->pixelId))
            ->setTestEventCode($this->testEventCode)
            ->setEvents($events);
        $response = $request->execute();

        return $response;
    }

    public function addToCart($product)
    {
        $api = Api::init(null, null, $this->accessToken);
        $api->setLogger(new CurlLogger());

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
            ->setEventId($this->eventId)
            ->setEventTime(time())
            ->setEventSourceUrl(url()->current())
            ->setUserData($userData)
            ->setCustomData($customData)
            ->setActionSource(ActionSource::WEBSITE);

        $events = array();
        array_push($events, $event);

        $request = (new EventRequest($this->pixelId))
            ->setTestEventCode($this->testEventCode)
            ->setEvents($events);
        $response = $request->execute();

        return $response;
    }

    public function newPurchase($purchaseData)
    {

        $api = Api::init(null, null, $this->accessToken);
        $api->setLogger(new CurlLogger());

        $userData = (new UserData())
            ->setEmails(array($purchaseData['customerData']['email']))
            ->setPhones(array($purchaseData['customerData']['phone']))
            ->setCity($purchaseData['address']['city'])
            ->setCountryCode($purchaseData['address']['countryCode'])
            ->setFirstName($purchaseData['customerData']['firstName'])
            ->setLastName($purchaseData['customerData']['lastName'])
            ->setZipCode($purchaseData['address']['zip'])
            // It is recommended to send Client IP and User Agent for Conversions API Events.
            ->setExternalId(Session::getId())
            ->setClientIpAddress($_SERVER['REMOTE_ADDR'])
            ->setClientUserAgent($_SERVER['HTTP_USER_AGENT']);

        $contents = [];
        $contentIds = [];
        foreach ($purchaseData['products'] as $product) {
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
            ->setCurrency($purchaseData['order']['currency'])
            ->setValue($purchaseData['order']['total_price']);

        $events = [];
        $events[] = (new Event())
            ->setEventName('Purchase')
            ->setEventId($this->eventId)
            ->setEventTime(time())
            ->setEventSourceUrl(url()->current())
            ->setUserData($userData)
            ->setCustomData($customData)
            ->setActionSource(ActionSource::WEBSITE);


        $request = (new EventRequest($this->pixelId))
            ->setTestEventCode($this->testEventCode)
            ->setEvents($events);

        $eventJson = [
            'value'=> $purchaseData['order']['total_price'],
            'currency'=> $purchaseData['order']['currency'],
            'content_type'=> 'product',
            'content_ids'=> $contentIds,
        ];

        $response = $request->execute();

        return [
            'requestResponse' => $response,
            'eventJson' => $eventJson
        ];
    }
}
