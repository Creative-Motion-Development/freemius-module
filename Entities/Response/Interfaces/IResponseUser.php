<?php

namespace Modules\Freemius\Entities\Response\Interfaces;

/**
 * Пользователь Freemius
 * @package App\Entity\Freemius\Response\Interfaces
 *
 * @author Artem Prihodko <webtemyk@yandex.ru>
 */
interface IResponseUser extends IResponse
{
    /**
     * Имя пользователя
     *
     * @return string
     */
    public function getFirstName(): ?string;

    /**
     * Фамилия пользователя
     *
     * @return string
     */
    public function getLastName(): ?string;

    /**
     * Дата создания пользователя
     *
     * @param $date_format string
     * @return string
     */
    public function getCreated($date_format = ''): ?string;

    /**
     * E-mail пользователя
     *
     * @return string
     */
    public function getEmail(): string;

    /**
     * IP пользователя
     *
     * @return string
     */
    public function getIp(): ?string;

    /**
     * Аватар пользователя
     *
     * @return string
     */
    public function getPicture(): ?string;

    /**
     * Подтвержден ли пользователь
     *
     * @return bool
     */
    public function isVerified(): bool;

    /**
     * Секретный ключ пользователя
     *
     * @return string
     */
    public function getSecretKey(): string;

    /**
     * Публичный ключ пользователя
     *
     * @return string
     */
    public function getPublicKey(): string;

    /**
     * Количество оплат
     *  Если 0 - то у пользователя триал
     *
     * @return int
     */
    public function getGross(): int;

    /**
     * @return bool
     */
    public function isMarketingAllowed(): bool;
}
