<?php

namespace Modules\Freemius\Entities\Response\Interfaces;

/**
 * Interface IResponse
 * @package App\Entity\Freemius\Response\Interfaces
 *
 * @author Artem Prihodko <webtemyk@yandex.ru>
 */
interface IResponse
{
    /**
     * Возвращает ID в Freemius
     *
     * @return string
     */
    public function getId(): string;
}
