<?php

namespace App\Entity\Freemius;

use App\Entity\Freemius\Response\Interfaces\IResponseLicense;
use App\Entity\Freemius\Response\Interfaces\IResponseUser;
use App\Entity\Freemius\Response\Models\License;
use App\Entity\Freemius\Response\Models\User;
use App\Models\Error;
use Exception;
use Freemius_Api;
use Illuminate\Support\Str;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;

/**
 * Class Freemius
 * @package App\Entity\Freemius
 *
 * @author Alexander Gorenkov <g.a.androidjc2@ya.ru>
 */
final class Freemius
{
    public const AUTH_HEADER = 'Authorization';
    public const PLUGIN_ID_HEADER = 'PluginId';

    public const AUTH_HEADER_PATTERN = '/^Bearer\s+(.*?)$/';

    /**
     * Длительность кеширования. 12 часов
     */
    public const CACHE_DURATION = 43200;

    private Freemius_Api $freemiusApi;

    /**
     * @var string
     */
    private string $licenseHeader = '';


    /**
     * @var int
     */
    private int $pluginHeader = 0;

    /**
     * @var IResponseLicense
     */
    private IResponseLicense $licenseData;

    /**
     * @var string|null
     */
    private ?string $lastError = null;

    /**
     * @var int
     */
    private int $lastErrorCode = 0;

    /**
     * Freemius constructor.
     */
    public function __construct()
    {
        /** @noinspection PhpIncludeInspection */
        require_once app_path('Freemius/Freemius.php');

        $this->freemiusApi = new Freemius_Api(
            'developer',
            config('freemius.developer_id'),
            config('freemius.public_key'),
            config('freemius.secret_key')
        );

        $authHeader = request()->header(self::AUTH_HEADER);

        if(!$authHeader) {
            return;
        }

        if (!preg_match(self::AUTH_HEADER_PATTERN, $authHeader, $authMatches)) {
            return;
        }

        $pluginIdHeader = request()->header(self::PLUGIN_ID_HEADER);
        if($pluginIdHeader && is_numeric($pluginIdHeader)) {
            $this->setPluginId((int) $pluginIdHeader);
        } else {
            $this->setPluginId((config('freemius.plugin_id')));
        }

        $this->licenseHeader = base64_decode($authMatches[1]);
    }

    /**
     * @return string|null
     */
    public function getLastError(): ?string
    {
        return $this->lastError;
    }

    /**
     * @return int
     */
    public function getLastErrorCode(): int
    {
        return $this->lastErrorCode;
    }

    public function setPluginId(int $pluginId): Freemius {
        $this->pluginHeader = $pluginId;
        return $this;
    }

    /**
     * @return IResponseLicense|null
     */
    public function fetchLicense(): ?IResponseLicense
    {
        $licenseHeader = $this->licenseHeader;
        $pluginHeader = $this->pluginHeader;
        $fr = $this->getApi();

        try {
            $licenseResponse = cache()->remember("$licenseHeader-$pluginHeader-get-license", self::CACHE_DURATION,
                fn() => $fr->Api("plugins/$pluginHeader/licenses.json?" . http_build_query([
                    'search' => $licenseHeader,
                    'enriched' => 'true',
                    'XDEBUG_SESSION_START' => 1,
                    'filter' => 'active'
                ])));
        } catch (Exception $e) {
            logger()->error($e->getMessage());
            logger()->error($e->getTraceAsString());
            return null;
        }

        $licenseModel = new License;
        if (!$licenseModel->load($licenseResponse->licenses[0] ?? [])) {
            logger()->error(sprintf("Unable to find license %s for plugin #%s. Possible error: %s",
                $licenseHeader,
                $pluginHeader,
                $licenseResponse->error->message ?? '*empty*'
            ));
            throw new UnauthorizedHttpException('', 'Invalid license format or service is temporarily unavailable');
        }

        $this->licenseData = $licenseModel;
        return $this->licenseData;
    }

    /**
     * @return IResponseUser|null
     */
    public function fetchUser(): ?IResponseUser
    {
        $licenseHeader = $this->getLicenseHeader();
        $pluginHeader = $this->getPluginHeader();
        $licenseData = $this->getLicenseData();
        $userId = $licenseData->getUserId();
        $fr = $this->getApi();

        try {
            $userResponse = cache()->remember("$licenseHeader-$pluginHeader-get-user", self::CACHE_DURATION,
                fn() => $fr->Api("plugins/$pluginHeader/users/$userId.json"));
        } catch (Exception $e) {
            logger()->error($e->getMessage());
            logger()->error($e->getTraceAsString());
            return null;
        }

        $userModel = new User;
        if (!$userModel->load($userResponse)) {
            logger()->error(sprintf("Unable to find user for plugin #%s and license %s. Possible error: %s",
                $licenseHeader,
                $pluginHeader,
                $licenseResponse->error->message ?? '*empty*'
            ));
            throw new UnauthorizedHttpException('', 'No user found by license');
        }

        return $userModel;
    }

    public function registerTrial(string $userEmail, ?string $password, int $duration): ?string {
        $data = [
            'email' => $userEmail,
            'password' => $password ?? Str::random(),
            'is_verified' => true,
        ];

        logger()->info(sprintf("Registering user for trial. Request body: %s", json_encode($data, JSON_PRETTY_PRINT)));
        $response = $this->getApi()->Api("plugins/{$this->pluginHeader}/users.json", "POST", $data);
        logger()->info(sprintf("Registered user: %s", json_encode($response, JSON_PRETTY_PRINT)));

        if(property_exists($response, 'error')) {
            $this->lastError = $response->error->message;
            $this->lastErrorCode = Error::ERROR_CREATE_TRIAL_USER;
            return null;
        }

        $userId = $response->id;
        $planId = config('freemius.trial_plan_id');
        $pricingId = config('freemius.trial_pricing_id');
        $response = $this->getApi()->Api("plugins/{$this->pluginHeader}/plans/{$planId}/pricing/{$pricingId}/licenses.json", "POST", [
            'expires_at' => date('Y-m-d H:i:s', time() + $duration),
            'email' => $userEmail,
            'plan_id' => $planId,
            'pricing_id' => $pricingId,
            'send_email' => true
        ]);

        logger()->info(sprintf("Create license response: %s", json_encode($response, JSON_PRETTY_PRINT)));

        if(property_exists($response, 'error')) {
            $this->lastError = $response->error->message;
            $this->lastErrorCode = Error::ERROR_CREATE_TRIAL_KEY;
            return null;
        }

        return $response->secret_key;
    }

    /**
     * @return Freemius_Api
     */
    public function getApi(): Freemius_Api
    {
        return $this->freemiusApi;
    }

    /**
     * @return string
     */
    public function getLicenseHeader(): string
    {
        return $this->licenseHeader;
    }

    /**
     * @return int
     */
    public function getPluginHeader(): int
    {
        return $this->pluginHeader;
    }

    /**
     * @return IResponseLicense|null
     */
    public function getLicenseData(): ?IResponseLicense
    {
        return $this->licenseData;
    }
}
