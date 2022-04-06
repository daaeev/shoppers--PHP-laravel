<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\CreateCoupon;
use App\Http\Requests\DeleteCoupon;
use App\Models\Coupon;
use App\Services\Interfaces\CouponsRepositoryInterface;
use App\Services\traits\ReturnWithRedirectAndFlash;

class CouponController extends Controller
{
    use ReturnWithRedirectAndFlash;

    /**
     * Создание купона
     *
     * @param Coupon $model
     * @param CreateCoupon $validate
     * @return mixed
     */
    public function createCoupon(
        Coupon $model,
        CreateCoupon $validate
    )
    {
        $model->setRawAttributes($validate->validated());

        if (!$model->save()) {
            return $this->withRedirectAndFlash(
                'status_failed',
                'Coupon save failed',
                route('admin.coupons'),
                $this->request
            );
        }

        return $this->withRedirectAndFlash(
            'status_success',
            'Coupon save success',
            route('admin.coupons'),
            $this->request
        );
    }

    /**
     * Удаление купона
     *
     * @param CouponsRepositoryInterface $couponRepository
     * @param DeleteCoupon $validate
     * @return mixed
     */
    public function deleteCoupon(
        CouponsRepositoryInterface $couponRepository,
        DeleteCoupon $validate
    )
    {
        $id = $validate->validated('id');
        $model = $couponRepository->getFirstOrNull($id);

        if (!$model->delete()) {
            return $this->withRedirectAndFlash(
                'status_failed',
                'Coupon delete failed',
                route('admin.coupons'),
                $this->request
            );
        }

        return $this->withRedirectAndFlash(
            'status_success',
            'Coupon delete success',
            route('admin.coupons'),
            $this->request
        );
    }
}
