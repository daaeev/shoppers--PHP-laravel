<?php

namespace App\Http\Requests\ajax;

use App\Services\Interfaces\UserRepositoryInterface;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Symfony\Component\HttpKernel\Exception\HttpException;

class ActivateCoupon extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(UserRepositoryInterface $userRepository)
    {
        if ($userRepository->getAuthenticated()) {
            return true;
        }

        throw new HttpException(401);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'token' => 'bail|required|string|max:30|exists:\App\Models\Coupon,token',
        ];
    }

    public function failedValidation(Validator $validator)
    {
        throw new HttpException(403);
    }
}
