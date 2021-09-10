<?php

namespace App\Entity\Freemius\Response\Models;

use App\Entity\Freemius\Response\Interfaces\IResponseLicense;

/**
 * Class License
 * @package App\Entity\Freemius\Response\Models
 *
 * @author  Alexander Gorenkov <g.a.androidjc2@ya.ru>
 */
class License extends BaseModel implements IResponseLicense
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
    public function getId(): string
    {
        return $this->id;
    }

    /**
     * {@inheritDoc}
     */
    public function getPlanId(): string
    {
        return $this->plan_id;
    }

    /**
     * @return string
     */
    public function getPricingId(): string
    {
        return $this->pricing_id;
    }

    /**
     * {@inheritDoc}
     */
    public function getSubscriptionId(): ?string
    {
        return $this->subscription_id;
    }

    /**
     * {@inheritDoc}
     */
    public function getPluginId(): string
    {
        return $this->plugin_id;
    }

    /**
     * {@inheritDoc}
     */
    public function getUserId(): string
    {
        return $this->user_id;
    }

    /**
     * {@inheritDoc}
     */
    public function getExpiration(): string
    {
        return $this->expiration ?? '2030-12-31 12:21:56';
    }

    /**
     * {@inheritDoc}
     */
    public function getSecretKey(): string
    {
        return $this->secret_key;
    }
}
