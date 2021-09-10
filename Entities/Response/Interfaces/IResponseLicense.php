<?php

namespace App\Entity\Freemius\Response\Interfaces;

/**
 * Лицензия Freemius
 * @package App\Entity\Freemius\Response\Interfaces
 *
 * @author Alexander Gorenkov <g.a.androidjc2@ya.ru>
 */
interface IResponseLicense extends IResponse
{
    /**
     * ID плана в Freemius
     *
     * @return string
     */
    public function getPlanId(): string;

    /**
     * ID подписки в Freemius
     *
     * @return string|null
     */
    public function getSubscriptionId(): ?string;

    /**
     * ID плагина в Freemius
     *
     * @return string
     */
    public function getPluginId(): string;

    /**
     * ID пользователя Freemius
     *
     * @return string
     */
    public function getUserId(): string;

    /**
     * Дата истечения лицензии
     *
     * @return string
     */
    public function getExpiration(): string;

    /**
     * Лицензионный ключ Freemius
     *
     * @return string
     */
    public function getSecretKey(): string;
}
