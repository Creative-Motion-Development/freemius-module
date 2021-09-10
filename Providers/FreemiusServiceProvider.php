<?php

namespace Modules\Freemius\Providers;

use App\Option;
use Illuminate\Support\ServiceProvider;
use Illuminate\Database\Eloquent\Factory;

//Module alias
define('FREEMIUS_MODULE', 'freemius');

class FreemiusServiceProvider extends ServiceProvider
{
    /**
     * Indicates if loading of the provider is deferred.
     *
     * @var bool
     */
    protected $defer = false;

    /**
     * Boot the application events.
     *
     * @return void
     */
    public function boot()
    {
        $this->requireApi();
        $this->registerConfig();
        $this->registerViews();
        $this->registerFactories();
        $this->loadMigrationsFrom(FREEMIUS_MODULE_DIR.'/Database/Migrations');
        $this->hooks();
    }

    public function requireApi()
    {
        require_once FREEMIUS_MODULE_DIR."/Api/load.php";
    }

    /**
     * Module hooks.
     */
    public function hooks()
    {
        // Add item to settings sections.
        \Eventy::addFilter('settings.sections', function ($sections) {
            $sections['freemius'] = ['title' => __('Freemius'), 'icon' => 'shopping-cart', 'order' => 250];

            return $sections;
        }, 16);

        // Section settings
        \Eventy::addFilter('settings.section_settings', function ($settings, $section) {

            if ($section != 'freemius') {
                return $settings;
            }

            return [
                'app_id' => Option::get('freemius_app_id'),
                'public_key' => Option::get('freemius_public_key'),
                'secret_key' => Option::get('freemius_secret_key'),
            ];
        }, 20, 2);

        // Section parameters.
        \Eventy::addFilter('settings.section_params', function ($params, $section) {

            if ($section != 'freemius') {
                return $params;
            }

            $params = [
                'app_id',
                'public_key',
                'secret_key',
            ];

            return $params;
        }, 20, 2);

        // Settings view name.
        \Eventy::addFilter('settings.view', function ($view, $section) {
            if ($section != 'freemius') {
                return $view;
            } else {
                return 'freemius::settings';
            }
        }, 20, 2);

    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->registerTranslations();
    }

    /**
     * Register config.
     *
     * @return void
     */
    protected function registerConfig()
    {
        $this->publishes([
            __DIR__.'/../Config/config.php' => config_path('freemius.php'),
        ], 'config');
        $this->mergeConfigFrom(
            __DIR__.'/../Config/config.php', 'freemius'
        );
    }

    /**
     * Register views.
     *
     * @return void
     */
    public function registerViews()
    {
        $viewPath = resource_path('views/modules/freemius');

        $sourcePath = __DIR__.'/../Resources/views';

        $this->publishes([
            $sourcePath => $viewPath
        ], 'views');

        $this->loadViewsFrom(array_merge(array_map(function ($path) {
            return $path.'/modules/freemius';
        }, \Config::get('view.paths')), [$sourcePath]), 'freemius');
    }

    /**
     * Register translations.
     *
     * @return void
     */
    public function registerTranslations()
    {
        $this->loadJsonTranslationsFrom(__DIR__.'/../Resources/lang');
    }

    /**
     * Register an additional directory of factories.
     * @source https://github.com/sebastiaanluca/laravel-resource-flow/blob/develop/src/Modules/ModuleServiceProvider.php#L66
     */
    public function registerFactories()
    {
        if (!app()->environment('production')) {
            app(Factory::class)->load(__DIR__.'/../Database/factories');
        }
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return [];
    }
}
