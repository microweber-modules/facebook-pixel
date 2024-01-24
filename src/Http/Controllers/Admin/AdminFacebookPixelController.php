<?php

namespace MicroweberPackages\Modules\FacebookPixel\Http\Controllers\Admin;

use MicroweberPackages\Admin\Http\Controllers\AdminController;
use Illuminate\Http\Request;

class AdminFacebookPixelController extends AdminController
{
    public function index(Request $request)
    {
        return view('facebook_pixel::admin.index');
    }
}
