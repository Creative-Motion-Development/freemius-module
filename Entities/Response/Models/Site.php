<?php

namespace Modules\Freemius\Entities\Response\Models;

use Modules\Freemius\Entities\Response\Models\BaseModel;

/**
 * Class Site
 * @package App\Entity\Freemius\Response\Models
 *
 * @author  Artem Prihodko <webtemyk@yandex.ru>
 */
class Site extends BaseModel
{
    /**
     * @var integer
     */
    public $id;

    /**
     * @var string
     */
    public $title;

    /**
     * @var string
     */
    public $url;

    /**
     * @var string
     */
    public $created;

    /**
     * @var bool
     */
    public $is_active;

    /**
     * @var bool
     */
    public $is_premium;

    /**
     * @var integer
     */
    public $gross;

    /**
     * @var string
     */
    public $version;

    /**
     * @var string
     */
    public $platform_version;

    /**
     * @var string
     */
    public $programming_language_version;

    /**
     * @var integer
     */
    public $subscription_id;

    /**
     * @var integer
     */
    public $user_id;

    /**
     * @var integer
     */
    public $license_id;

    /**
     * @var integer
     */
    public $plugin_id;

    /**
     * @var integer
     */
    public $plan_id;

    /**
     * @var License
     */
    public $license;

    /**
     * @var Plan
     */
    public $plan;

}
