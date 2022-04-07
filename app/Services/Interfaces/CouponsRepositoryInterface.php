<?php

namespace App\Services\Interfaces;

use App\Models\Coupon;
use App\Services\Interfaces\divided\GetFirstInterface;
use App\Services\Interfaces\divided\GridInterface;

interface CouponsRepositoryInterface extends
    GridInterface,
    GetFirstInterface

{
    /**
     * Метод возвращает неактивированный купон с токеном $token, если имеется, иначе null
     *
     * @param string $token
     * @return Coupon|null
     */
    public function getFirstNotActivatedByTokenOrNull(string $token): Coupon|null;
}
