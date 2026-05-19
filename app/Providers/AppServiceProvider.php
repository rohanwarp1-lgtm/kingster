<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\View;
use App\Models\Product;
use App\Models\GeneralSetting;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        Paginator::useBootstrap();

        $this->loadHelpers();

        // Share $products and $generalSettings with every frontend view so the
        // header nav (logo, top-bar, Our Products dropdown) always renders.
        View::composer(
            ['layout.mainlayout', 'layout.partials.header', 'layout.partials.footer',
             'index-3', 'products', 'product-details', 'about-us', 'contact-us',
             'shipping-returns', 'privacy-policy', 'terms-condition',
             'warranty-apply', 'warranty-check', 'warranty-rules'],
            function ($view) {
                static $products = null;
                static $generalSettings = null;

                if ($products === null) {
                    try {
                        $products = Product::where('status', 1)
                            ->where('is_deleted', 0)
                            ->orderBy('index', 'asc')
                            ->get();
                    } catch (\Exception $e) {
                        $products = collect();
                    }
                }

                if ($generalSettings === null) {
                    try {
                        $generalSettings = GeneralSetting::first();
                    } catch (\Exception $e) {
                        $generalSettings = null;
                    }
                }

                $view->with('products', $products)
                     ->with('generalSettings', $generalSettings);
            }
        );

        // Resolve product/setting image paths to public asset URLs.
        // Stored paths may be prefixed with 'public/' (products) or not (settings).
        Blade::directive('prodImage', function ($expression) {
            return "<?php
                \$_pi = (string) ({$expression});
                if (\$_pi === '' || \$_pi === null) { echo ''; }
                elseif (str_starts_with(\$_pi, 'public/')) { echo asset(substr(\$_pi, 7)); }
                else { echo asset(\$_pi); }
            ?>";
        });
    }

    protected function loadHelpers(): void
    {
        $helpers = glob(app_path('Support/*.php'));

        foreach ($helpers as $helper) {
            require_once $helper;
        }
    }
}
