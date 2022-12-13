<?php

namespace Modules\Freemius\Providers;

use App\Customer;
use Modules\Freemius\Entities\Freemius;
use App\Option;
use Illuminate\Support\ServiceProvider;
use Illuminate\Database\Eloquent\Factory;
use Modules\Freemius\Entities\Response\Models\BaseModel;
use Modules\Freemius\Entities\Response\Models\Site;
use Modules\Freemius\Entities\Response\Models\User;

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
     * @var Freemius
     */
    public $freemius;

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
        $this->freemius = new Freemius();
    }

    /**
     * Module hooks.
     */
    public function hooks()
    {
        // Add module's css file to the application layout
        \Eventy::addFilter('stylesheets', function ($styles) {
            $styles[] = \Module::getPublicPath(FREEMIUS_MODULE).'/css/module.css';
            return $styles;
        });

        // Add module's JS file to the application layout
        \Eventy::addFilter('javascripts', function ($javascripts) {
            //$javascripts[] = \Module::getPublicPath(FREEMIUS_MODULE).'/js/module.js';
            return $javascripts;
        });

        // Add item to settings sections.
        \Eventy::addFilter('settings.sections', function ($sections) {
            $sections[FREEMIUS_MODULE] = ['title' => __('Freemius'), 'icon' => 'shopping-cart', 'order' => 250];

            return $sections;
        }, 16);

        // Section settings
        \Eventy::addFilter('settings.section_settings', function ($settings, $section) {

            if ($section != FREEMIUS_MODULE) {
                return $settings;
            }

            return [
                'freemius_app_id' => Option::get('freemius_app_id'),
                'freemius_public_key' => Option::get('freemius_public_key'),
                'freemius_secret_key' => Option::get('freemius_secret_key'),
            ];
        }, 20, 2);

        // Section parameters.
        \Eventy::addFilter('settings.section_params', function ($params, $section) {

            if ($section != FREEMIUS_MODULE) {
                return $params;
            }

            $params = [
                'template_vars' => [],
                'validator_rules' => [
                    'settings.freemius_app_id' => 'required|numeric',
                    'settings.freemius_public_key' => 'required',
                    'settings.freemius_secret_key' => 'required',
                ],
                'settings' => [
                    'freemius_app_id',
                    'freemius_public_key',
                    'freemius_secret_key',
                ]
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

        \Eventy::addAction('customer.set_data', [$this, 'customer_set_data'], 20, 3);

        \Eventy::addAction('customer.profile.extra', [$this, 'customer_profile_extra']);
    }

    /**
     * Save freemius data for customer
     *
     * @param $customer Customer
     * @param $data array
     * @param $replace_data array
     */
    public function customer_set_data($customer, $data, $replace_data)
    {
        $user = $this->freemius->findUserByEmail($data['emails'][0] ?? '');
        if ($user) {
            $customer->setMeta('freemius_user', $user);
        } else {
            $customer->setMeta('freemius_user', '');
        }
    }

    /**
     * Output freemius data for customer
     *
     * @param $customer Customer
     */
    public function customer_profile_extra($customer)
    {
        if (empty($customer->getMeta('freemius_user', []))) {
            $this->customer_set_data($customer, ['emails' => [$customer->getMainEmail()]], []);
            $customer->save();
        }

        $user = $this->getFreemiusUser($customer);
        if (!$user) {
            return;
        }

        $freemius_data = $this->getFreemiusData($user);

        echo \View::make('freemius::customer_fields_view', [
            'customer' => $customer,
            'freemius_user' => $user,
            'plugins' => $freemius_data,
        ])->render();
    }

    /**
     * Get Freemius data via API
     *
     * @param  Customer  $customer
     * @return User|null
     */
    public function getFreemiusUser($customer): ?User
    {
        $user = $this->freemius->loadModel('User', $customer->getMeta('freemius_user', []), "Freemius user not found");
        if (isset($user->gross)) {
            $user->gross = round($user->gross, 2);
        }
        return $user;
    }

    /**
     * Get Freemius data via API
     *
     * @param  User  $user
     * @return Site[]
     */
    public function getFreemiusData($user): ?array
    {
        $result = [];

        $plugins = $this->freemius->findPlugins();
        $sites = $this->freemius->findSitesByUser($user);
        $licenses = $this->freemius->findLicensesByUser($user);
        if ($sites) {
            foreach ((array) $sites as &$site) {
                $plugin = $plugins[$site->plugin_id];
                $plans = $this->freemius->findPlansByPlugin($site->plugin_id);

                $site->license = $site->license_id ? $licenses[$site->license_id] : [];
                $site->plan = $plans[$site->plan_id];
                $plugin->sites = $sites;

                $result[$site->plugin_id] = $plugin;
            }
        } else {
            foreach ((array) $licenses as $license) {
                $plugin = $plugins[$license->plugin_id];
                $plugin->sites = [];
                $result[$license->plugin_id] = $plugin;
            }
        }
        //$helpscout = $this->freemius->getHelpScout();

        return $result;
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
