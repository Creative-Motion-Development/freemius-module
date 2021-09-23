<?php

namespace Modules\Freemius\Entities\Response\Models;

use Modules\Freemius\Entities\Response\Interfaces\IResponseLicense;
use Modules\Freemius\Entities\Response\Models\BaseModel;

/**
 * Class License
 * @package App\Entity\Freemius\Response\Models
 *
 * @author  Artem Prihodko <webtemyk@yandex.ru>
 */
class License extends BaseModel
{
    /**
     * @var string
     */
    public $id;

    /**
     * @var string
     */
    public $plan_id;

    /**
     * @var string|null
     */
    public $pricing_id;

    /**
     * @var string
     */
    public $subscription_id;

    /**
     * @var string
     */
    public $plugin_id;

    /**
     * @var string
     */
    public $user_id;

    /**
     * @var string
     */
    public $expiration;

    /**
     * @var string
     */
    public $secret_key;

    /**
     * {@inheritDoc}
     */
    public function getExpiration(): string
    {
        return $this->expiration ?? '';
    }

    /**
     * {@inheritDoc}
     */
    public function getRenewsIn(): string
    {
        return $this->getBeautyDate($this->getExpiration(), 'Y-m-d');

    }
}
