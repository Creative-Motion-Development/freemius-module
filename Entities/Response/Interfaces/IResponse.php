<?php

namespace App\Entity\Freemius\Response\Interfaces;

/**
 * Interface IResponse
 * @package App\Entity\Freemius\Response\Interfaces
 *
 * @author Alexander Gorenkov <g.a.androidjc2@ya.ru>
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
