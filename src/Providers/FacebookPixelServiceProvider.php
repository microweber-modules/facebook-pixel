<?php

namespace MicroweberPackages\Modules\FacebookPixel\Providers;

use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\View;
use Livewire\Livewire;

use MicroweberPackages\Modules\FacebookPixel\Http\Livewire\Admin\AdminFacebookPixelComponent;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class FacebookPixelServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        $package->name('microweber-module-facebook_pixel');
        $package->hasViews('microweber-module-facebook_pixel');
        $package->hasRoute('api');
        $package->hasRoute('web');
        $package->hasRoute('admin');
        $package->runsMigrations(true);
    }

    public function packageBooted()
    {
        $this->registerComponents();
    }

    public function registerComponents()
    {
        Blade::componentNamespace('MicroweberPackages\\Modules\\FacebookPixel\\View\\Components', 'facebook_pixel');

        View::addNamespace('facebook_pixel', normalize_path(__DIR__) . '/../resources/views');

        Livewire::component('facebook_pixel::admin-facebook-pixel', AdminFacebookPixelComponent::class);

        return $this;
    }

    public function register(): void {

        $this->loadMigrationsFrom(__DIR__ . '/../database/migrations/');
        $this->loadRoutesFrom(__DIR__ . '/../routes/admin.php');
        $this->loadRoutesFrom(__DIR__ . '/../routes/web.php');

        $getFacebookPixelIsEnabled = get_option('is_enabled', 'facebook_pixel');
        if ($getFacebookPixelIsEnabled) {
            $this->app->register(\MicroweberPackages\Modules\FacebookPixel\Providers\FacebookPixelEventsServiceProvider::class);
        }
        
        parent::register();

    }

}
