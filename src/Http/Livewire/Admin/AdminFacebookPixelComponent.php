<?php

namespace MicroweberPackages\Modules\FacebookPixel\Http\Livewire\Admin;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use MicroweberPackages\Admin\Http\Livewire\AdminComponent;

class AdminFacebookPixelComponent extends AdminComponent
{
    use AuthorizesRequests;

    public function render()
    {
        $exportSecretKey = get_option('fb_pixel_export_feed_secret', 'facebook_pixel');
        if (empty($exportSecretKey)) {
            $exportSecretKey = md5(time() . rand(111111, 999999));
            save_option('fb_pixel_export_feed_secret', $exportSecretKey, 'facebook_pixel');
        }

        return view('facebook_pixel::admin.livewire.index', [
            'exportSecretKey' => $exportSecretKey
        ]);
    }
}
