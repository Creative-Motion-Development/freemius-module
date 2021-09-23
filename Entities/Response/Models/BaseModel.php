<?php

namespace Modules\Freemius\Entities\Response\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use ReflectionClass;
use ReflectionException;
use ReflectionProperty;

/**
 * Class BaseModel
 * @package App\Entity\Freemius\Response\Models
 *
 * @author Artem Prihodko <webtemyk@yandex.ru>
 */
class BaseModel
{
    private $date_format = 'Y-m-d H:i';

    /**
     * @param $data
     * @return bool
     */
    public function load($data): bool
    {
        if (is_object($data)) {
            $data = get_object_vars($data);
        }

        if (empty($data)) {
            return false;
        }

        if (isset($data['error'])) {
            return false;
        }

        try {
            $reflection = new ReflectionClass($this);
            foreach ($reflection->getProperties(ReflectionProperty::IS_PUBLIC) as $property) {
                if (!isset($data[$property->getName()])) {
                    continue;
                }

                $property->setValue($this, $data[$property->getName()]);
            }
        } catch (ReflectionException $exception) {
            logger()->error($exception->getMessage());
            logger()->error($exception->getTraceAsString());
            return false;
        }

        return true;
    }

    /**
     * {@inheritDoc}
     */
    public function getBeautyDate($date, $date_format = ''): ?string
    {
        if (!$date) {
            return "LIFETIME";
        }
        if (!$date_format) {
            $date_format = $this->date_format;
        }
        $date = \App\User::dateFormat(new Carbon($date, auth()->user()->getAttribute('timezone')), $date_format);
        return $date ?? '';
    }

}
