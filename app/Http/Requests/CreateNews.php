<?php

namespace App\Http\Requests;

use App\Services\Interfaces\UserRepositoryInterface;
use Illuminate\Foundation\Http\FormRequest;

class CreateNews extends FormRequest
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
            'title' => 'required|string',
            'content' => 'required|string',
        ];
    }
}
