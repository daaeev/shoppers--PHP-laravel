<?php

namespace App\Http\Requests;

use App\Services\Interfaces\UserRepositoryInterface;
use Illuminate\Foundation\Http\FormRequest;

class CreateCategory extends FormRequest
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
            'name' => 'bail|required|max:255|unique:\App\Models\Category,name',
        ];
    }
}
