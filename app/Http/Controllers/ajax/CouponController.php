<?php

namespace App\Http\Controllers\ajax;

use App\Http\Requests\ajax\ActivateCoupon;
use App\Services\Interfaces\CouponsRepositoryInterface;
use App\Services\Interfaces\UserRepositoryInterface;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpKernel\Exception\HttpException;

class CouponController
{
    /**
     * Активация купона пользователем
     *
     * @param UserRepositoryInterface $userRepository
     * @param CouponsRepositoryInterface $couponRepository
     * @param ActivateCoupon $validate
     * @return Response
     */
    public function activateCoupon(
        UserRepositoryInterface $userRepository,
        CouponsRepositoryInterface $couponRepository,
        ActivateCoupon $validate
    )
    {
        $user = $userRepository->getAuthenticated();
        $coupon = $couponRepository->getFirstNotActivatedByTokenOrNull($validate->validated('token'));

        if (!$coupon) {
            throw new HttpException(404);
        }

        DB::transaction(function () use ($user, $coupon){
            $user->coupon_id = $coupon->id;
            $coupon->activated = true;

            if (!$user->save() || !$coupon->save()) {
                throw new HttpException(500);
            }
        });

        return (new Response($coupon->percent));
    }
}
