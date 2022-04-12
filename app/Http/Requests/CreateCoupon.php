<?php

namespace App\Http\Requests;

use App\Services\Interfaces\UserRepositoryInterface;
use Illuminate\Foundation\Http\FormRequest;

class CreateCoupon extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(UserRepositoryInterface $userRepository)
    {
        if ($userRepository->getAuthenticated()?->isAdmin()) {
            return true;
        }

        return false;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'percent' => 'required|integer|min:1|max:100',
            'token' => 'bail|required|string|max:30|unique:\App\Models\Coupon,token'
        ];
    }
}
