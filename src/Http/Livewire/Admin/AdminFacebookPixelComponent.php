<?php

namespace MicroweberPackages\Modules\FacebookPixel\Http\Livewire\Admin;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use MicroweberPackages\Admin\Http\Livewire\AdminComponent;

class AdminFacebookPixelComponent extends AdminComponent
{
    use AuthorizesRequests;

    public function render()
    {
        return view('facebook_pixel::admin.livewire.index');
    }
}
