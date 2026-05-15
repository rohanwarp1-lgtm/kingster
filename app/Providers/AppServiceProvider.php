<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Blade;
use App\Models\GeneralSetting;
use App\Models\Product;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Share general settings and products data with all views
        View::composer('*', function ($view) {
            $generalSettings = GeneralSetting::first();
            $products = Product::where('status', 1)
                              ->where('is_deleted', 0)
                              ->orderBy('index', 'asc')
                              ->get();

            $view->with('generalSettings', $generalSettings);
            $view->with('products', $products);
        });

        Blade::directive('prodImage', function ($expression) {
            return "<?php echo asset($expression); ?>";
        });
    }
}
