<?php

namespace Modules\Freemius\Entities;

use Modules\Freemius\Entities\Response\Interfaces\IResponseLicense;
use Modules\Freemius\Entities\Response\Interfaces\IResponseUser;
use Modules\Freemius\Entities\Response\Models\License;
use Modules\Freemius\Entities\Response\Models\Plan;
use Modules\Freemius\Entities\Response\Models\Plugin;
use Modules\Freemius\Entities\Response\Models\Site;
use Modules\Freemius\Entities\Response\Models\User;

//use App\Models\Error;
use App\Option;
use Exception;
use Freemius_Api;
use Illuminate\Support\Str;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;

/**
 * Class Freemius
 * @package App\Entity\Freemius
 *
 * @author Artem Prihodko <webtemyk@yandex.ru>
 */
final class Freemius
{
    /**
     * Длительность кеширования. 24 часа
     */
    public const CACHE_DURATION = 86400;

    /**
     * Длительность кеширования. 12 часов
     */
    public const DASHBOARD_CACHE_DURATION = 31536000000;

    private Freemius_Api $api;

    /**
     * @var int|string
     */
    public $store_id = 0;

    /**
     * Freemius constructor.
     */
    public function __construct()
    {
        $freemius_app_id = Option::get('freemius_app_id', null);
        $freemius_public_key = Option::get('freemius_public_key', null);
        $freemius_secret_key = Option::get('freemius_secret_key', null);

        if ($freemius_app_id && $freemius_public_key && $freemius_secret_key) {
            $this->api = new Freemius_Api(
                'developer',
                abs((int) $freemius_app_id),
                $freemius_public_key,
                $freemius_secret_key
            );

            try {
                $dashboard = cache()->remember("freemius-dashboard", self::DASHBOARD_CACHE_DURATION,
                    fn() => $this->api->Api("dashboard.json?format=json"));
            } catch (Exception $e) {
                logger()->error($e->getMessage());
                logger()->error($e->getTraceAsString());
            }

            if (isset($dashboard->stores[0]->id)) {
                $this->store_id = $dashboard->stores[0]->id;
            } else {
                cache()->delete("freemius-dashboard");
            }
        }
    }

    /**
     * @param  string  $modelClassName
     * @param  array  $data
     * @param  string  $errorMessage
     * @return User|Site[]|License[]|Plan[]
     */
    public function loadModel(string $modelClassName, $data, string $errorMessage = '')
    {
        $modelClassName = "Modules\Freemius\Entities\Response\Models\\$modelClassName";
        if (is_array($data) && isset($data[0])) {
            foreach ($data as $item) {
                $model = new $modelClassName;
                if ($model->load($item ?? [])) {
                    if (isset($item->id)) {
                        $result[$item->id] = $model;
                    } else {
                        $result[] = $model;
                    }
                } else {
                    logger()->error($errorMessage);
                }
            }
        } else {
            $model = new $modelClassName;
            if ($model->load($data ?? [])) {
                $result = $model;
            } else {
                logger()->error($errorMessage);
            }
        }

        return $result ?? null;
    }

    /**
     * @param  string  $email
     * @param  bool  $cache
     * @return User|false
     */
    public function findUserByEmail($email, $cache = true)
    {
        if ($this->store_id && $email) {
            try {
                if ($cache) {
                    $users = cache()->remember("freemius-{$this->store_id}-users-{$email}", self::CACHE_DURATION,
                        fn() => $this->api->Api("stores/{$this->store_id}/users.json?format=json&search={$email}"));
                } else {
                    $users = $this->api->Api("stores/{$this->store_id}/users.json?format=json&search={$email}");
                }
            } catch (Exception $e) {
                logger()->error($e->getMessage());
                logger()->error($e->getTraceAsString());
                return null;
            }

            $user = isset($users->users[0]) ? get_object_vars($users->users[0]) : [];

            $result = $this->loadModel('User', $user, "Unable to find user");

        }

        return $result ?? false;
    }

    /**
     * @return Plugin[]
     */
    public function findPlugins(): ?array
    {
        if ($this->store_id) {
            try {
                $data = cache()->remember("freemius-{$this->store_id}-plugins", self::CACHE_DURATION,
                    fn() => $this->api->Api("plugins.json?format=json&all=true"));
            } catch (Exception $e) {
                logger()->error($e->getMessage());
                logger()->error($e->getTraceAsString());
                return null;
            }
        }

        $result = $this->loadModel('Plugin', $data->plugins ?? [], "No plugins found - ".count($data->plugins ?? []));

        return $result;
    }

    /**
     * @param $user User|int|string
     * @return License[]
     */
    public function findLicensesByUser($user): ?array
    {
        if ($user instanceof User) {
            $user_id = $user->id;
        } else {
            $user_id = $user;
        }

        if ($this->store_id) {
            try {
                $data = cache()->remember("freemius-{$this->store_id}-user-{$user_id}-licenses",
                    self::CACHE_DURATION,
                    fn(
                    ) => $this->api->Api("stores/{$this->store_id}/users/{$user_id}/licenses.json?format=json&count=100"));
            } catch (Exception $e) {
                logger()->error($e->getMessage());
                logger()->error($e->getTraceAsString());
                return null;
            }
        }

        $result = $this->loadModel('License', $data->licenses ?? [],
            "No licenses found - ".count($data->licenses ?? []));

        return $result;
    }

    /**
     * @param $user User|int|string
     * @return Site[]
     */
    public function findSitesByUser($user): ?array
    {
        if ($user instanceof User) {
            $user_id = $user->id;
        } else {
            $user_id = $user;
        }

        if ($this->store_id) {
            try {
                $data = cache()->remember("freemius-{$this->store_id}-user-{$user_id}-sites", self::CACHE_DURATION,
                    fn() => $this->api->Api("stores/{$this->store_id}/installs.json?format=json&user_id={$user_id}"));
            } catch (Exception $e) {
                logger()->error($e->getMessage());
                logger()->error($e->getTraceAsString());
                return null;
            }
        }

        $result = $this->loadModel('Site', $data->installs ?? [], "No installs found - ".count($data->installs ?? []));

        return $result;
    }

    /**
     * @param $plugin int|string
     * @return Plan[]
     */
    public function findPlansByPlugin($plugin): ?array
    {
        if ($this->store_id) {
            try {
                $data = cache()->remember("freemius-{$this->store_id}-plugin-{$plugin}-plans", self::CACHE_DURATION,
                    fn() => $this->api->Api("plugins/{$plugin}/plans.json?format=json"));
            } catch (Exception $e) {
                logger()->error($e->getMessage());
                logger()->error($e->getTraceAsString());
                return null;
            }
        }

        $result = $this->loadModel('Plan', $data->plans ?? [], "No plans found for plugin {$plugin}");

        return $result;
    }

    /**
     * @return array
     */
    public function getHelpScout(): ?array
    {
        // You can find your developer ID here: https://dashboard.freemius.com/#!/profile/.
        $developer_id = 2868;
        $plugin_id = 2315;
        $helpscout_secret_key = '0eee495f267b7ef0cca7a3606cfdeef5';
        // Your customer's email address (the one who submitted the ticket).
        $customer_email_address = 'gostischev@gmail.com';

        $ch = curl_init("https://api.freemius.com/v1/developers/{$developer_id}/plugins/{$plugin_id}/helpscout.json");

        $data = json_encode(array('customer' => array('email' => $customer_email_address)));
        $signature = base64_encode(hash_hmac('sha1', $data, $helpscout_secret_key, true));
        $headers = array(
            'X_HELPSCOUT_SIGNATURE: '.$signature,
            'Content-Type: application/json'
        );

        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        //curl_setopt($ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

        $result = curl_exec($ch);

        return json_decode($result, true);
    }

    /**
     * @return Freemius_Api
     */
    public function getApi(): Freemius_Api
    {
        return $this->api;
    }

}
