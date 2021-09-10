<?php

namespace App\Entity\Freemius\Response\Models;

use App\Entity\Freemius\Response\Interfaces\IResponseUser;

/**
 * Class User
 * @package App\Entity\Freemius\Response\Models
 *
 * @author Alexander Gorenkov <g.a.androidjc2@ya.ru>
 */
class User extends BaseModel implements IResponseUser
{
    /**
     * @var string
     */
    public $id;

    /**
     * @var string
     */
    public $first;

    /**
     * @var string
     */
    public $last;

    /**
     * @var string
     */
    public $email;

    /**
     * @var string
     */
    public $picture;

    /**
     * @var string
     */
    public $ip;

    /**
     * @var string
     */
    public $secret_key;

    /**
     * @var string
     */
    public $public_key;

    /**
     * @var bool
     */
    public $is_verified;

    /**
     * @var int
     */
    public $gross;

    /**
     * @var int
     */
    public $is_marketing_allowed;

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
    public function getFirstName(): ?string
    {
        return $this->first;
    }

    /**
     * {@inheritDoc}
     */
    public function getLastName(): ?string
    {
        return $this->last;
    }

    /**
     * {@inheritDoc}
     */
    public function getEmail(): string
    {
        return $this->email;
    }

    /**
     * {@inheritDoc}
     */
    public function getIp(): ?string
    {
        return $this->ip;
    }

    /**
     * {@inheritDoc}
     */
    public function getPicture(): ?string
    {
        return $this->picture;
    }

    /**
     * {@inheritDoc}
     */
    public function isVerified(): bool
    {
        return $this->is_verified;
    }

    /**
     * {@inheritDoc}
     */
    public function getSecretKey(): string
    {
        return $this->secret_key;
    }

    /**
     * {@inheritDoc}
     */
    public function getPublicKey(): string
    {
        return $this->public_key;
    }

    /**
     * {@inheritDoc}
     */
    public function getGross(): int
    {
        return $this->gross;
    }

    /**
     * {@inheritDoc}
     */
    public function isMarketingAllowed(): bool
    {
        return (bool) $this->is_marketing_allowed;
    }
}
