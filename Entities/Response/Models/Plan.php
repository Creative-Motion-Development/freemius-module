<?php

namespace Modules\Freemius\Entities\Response\Models;

use Modules\Freemius\Entities\Response\Models\BaseModel;

/**
 * Class Plan
 * @package App\Entity\Freemius\Response\Models
 *
 * @author  Artem Prihodko <webtemyk@yandex.ru>
 */
class Plan extends BaseModel
{
    /**
     * @var integer
     */
    public $id;

    /**
     * @var string
     */
    public $created;

    /**
     * @var string
     */
    public $description;

    /**
     * @var bool
     */
    public $is_block_features;

    /**
     * @var bool
     */
    public $is_hidden;

    /**
     * @var string
     */
    public $name;

    /**
     * @var string
     */
    public $title;

    /**
     * @var string
     */
    public $updated;
}
