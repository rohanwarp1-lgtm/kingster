<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Modules\FbaAuto\Interfaces\FbaAutoRepositoryInterface;
use App\Modules\FbaAuto\Repositories\FbaAutoRepository;
use App\Modules\FbaAuto\Services\FbaAutoService;
use App\Modules\Warranty\Repositories\WarrantyRepository;
use App\Modules\Warranty\Services\WarrantyService;
use App\Modules\Rma\Repositories\RmaRepository;
use App\Modules\Rma\Services\RmaWorkflowService;
use App\Modules\ReturnReport\Repositories\ReturnReportRepository;
use App\Modules\ReturnReport\Services\AnalyticsService;
use App\Modules\ReturnReport\Services\ExportService;

class ModuleServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(FbaAutoRepositoryInterface::class, FbaAutoRepository::class);
        
        $this->app->singleton(FbaAutoService::class, function ($app) {
            return new FbaAutoService($app->make(FbaAutoRepository::class));
        });
        
        $this->app->singleton(WarrantyRepository::class);
        $this->app->singleton(WarrantyService::class, function ($app) {
            return new WarrantyService($app->make(WarrantyRepository::class));
        });
        
        $this->app->singleton(RmaRepository::class);
        $this->app->singleton(RmaWorkflowService::class, function ($app) {
            return new RmaWorkflowService($app->make(RmaRepository::class));
        });
        
        $this->app->singleton(ReturnReportRepository::class);
        $this->app->singleton(AnalyticsService::class, function ($app) {
            return new AnalyticsService($app->make(ReturnReportRepository::class));
        });
        $this->app->singleton(ExportService::class, function ($app) {
            return new ExportService($app->make(ReturnReportRepository::class));
        });
    }

    public function boot(): void
    {
        $modules = [
            'FbaAuto' => 'fba-auto',
            'Warranty' => 'warranty',
            'Rma' => 'rma',
            'ReturnReport' => 'return-report',
        ];
        
        foreach ($modules as $module => $namespace) {
            $modulePath = module_path($module);

            if (is_dir($modulePath . '/Resources/views')) {
                $this->loadViewsFrom($modulePath . '/Resources/views', $namespace);
            }
        }
    }
}
