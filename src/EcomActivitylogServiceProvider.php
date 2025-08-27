<?php

namespace HamzaAabila\EcomActivitylog;

use Filament\Support\Assets\Css;
use Filament\Support\Assets\Js;
use Filament\Support\Facades\FilamentAsset;
use Illuminate\Support\ServiceProvider;
use Livewire\Livewire;
use HamzaAabila\EcomActivitylog\Filament\Components\TimelineComponent;
use HamzaAabila\EcomActivitylog\Contracts\TimelineFormatter;
use HamzaAabila\EcomActivitylog\Services\DefaultTimelineFormatter;

class EcomActivitylogServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__ . '/../config/ecom-activitylog.php', 'ecom-activitylog');
        
        // Bind the default formatter
        $this->app->bind(TimelineFormatter::class, DefaultTimelineFormatter::class);
    }

    public function boot(): void
    {
        // Load translations
        $this->loadTranslationsFrom(__DIR__ . '/../lang', 'ecom-activitylog');
        
        // Load views
        $this->loadViewsFrom(__DIR__ . '/../resources/views', 'ecom-activitylog');

        // Register Livewire components
        Livewire::component('ecom-activity-timeline', TimelineComponent::class);

        // Register Filament assets
        FilamentAsset::register([
            Css::make('ecom-activitylog-styles', __DIR__ . '/../resources/dist/ecom-activitylog.css'),
            Js::make('ecom-activitylog-scripts', __DIR__ . '/../resources/dist/ecom-activitylog.js'),
        ], package: 'hamzaabaila/ecom-activitylog');

        if ($this->app->runningInConsole()) {
            // Publish config
            $this->publishes([
                __DIR__ . '/../config/ecom-activitylog.php' => config_path('ecom-activitylog.php'),
            ], 'ecom-activitylog-config');

            // Publish views
            $this->publishes([
                __DIR__ . '/../resources/views' => resource_path('views/vendor/ecom-activitylog'),
            ], 'ecom-activitylog-views');

            // Publish translations
            $this->publishes([
                __DIR__ . '/../lang' => lang_path('vendor/ecom-activitylog'),
            ], 'ecom-activitylog-lang');

            // Publish assets
            $this->publishes([
                __DIR__ . '/../resources/dist' => public_path('vendor/ecom-activitylog'),
            ], 'ecom-activitylog-assets');
        }
    }
}