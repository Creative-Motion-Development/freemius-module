<?php

namespace App\Entity\Freemius\Response\Models;

use Illuminate\Database\Eloquent\Model;
use ReflectionClass;
use ReflectionException;
use ReflectionProperty;

/**
 * Class BaseModel
 * @package App\Entity\Freemius\Response\Models
 *
 * @author Alexander Gorenkov <g.a.androidjc2@ya.ru>
 */
class BaseModel
{
    /**
     * @param $data
     * @return bool
     */
    public function load($data): bool {
        if(is_object($data)) {
            $data = get_object_vars($data);
        }

        if(empty($data)) {
            return false;
        }

        if(isset($data['error'])) {
            return false;
        }

        try {
            $reflection = new ReflectionClass($this);
            foreach($reflection->getProperties(ReflectionProperty::IS_PUBLIC) as $property) {
                if(!isset($data[$property->getName()])) {
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
}
