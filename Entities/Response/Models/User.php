<?php

namespace Modules\Freemius\Entities\Response\Models;

use App\Option;
use Carbon\Carbon;
use Modules\Freemius\Entities\Response\Interfaces\IResponseUser;
use Modules\Freemius\Entities\Response\Models\BaseModel;

/**
 * Class User
 * @package App\Entity\Freemius\Response\Models
 *
 * @author Artem Prihodko <webtemyk@yandex.ru>
 */
class User extends BaseModel
{
    /**
     * @var string
     */
    public $id;

    /**
     * @var string
     */
    public $created;

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
     * User constructor.
     *
     * @param $data array
     */
    public function __construct($data = [])
    {
        $this->load($data);
    }

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
    public function getCreated($date_format = 'Y-m-d H:i'): ?string
    {
        return $this->getBeautyDate($this->created, $date_format);
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
