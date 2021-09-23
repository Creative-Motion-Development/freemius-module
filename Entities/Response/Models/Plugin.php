<?php

namespace Modules\Freemius\Entities\Response\Models;

use Modules\Freemius\Entities\Response\Models\BaseModel;

/**
 * Class Plugin
 * @package App\Entity\Freemius\Response\Models
 *
 * @author  Artem Prihodko <webtemyk@yandex.ru>
 */
class Plugin extends BaseModel
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
    public $slug;

    /**
     * @var string
     */
    public $created;

    /**
     * @var string
     */
    public $icon;

    /**
     * @var bool
     */
    public $is_released;

    /**
     * @var Site[]
     */
    public $sites;
}
