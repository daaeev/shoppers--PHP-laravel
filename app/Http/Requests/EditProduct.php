<?php

namespace App\Http\Requests;

use App\Services\Interfaces\UserRepositoryInterface;
use Illuminate\Foundation\Http\FormRequest;

class EditProduct extends FormRequest
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
            'id' => 'bail|required|integer|exists:\App\Models\Product,id',
            'name' => 'bail|required|max:255',
            'subname' => 'required|max:255',
            'description' => 'required',
            'category_id' => 'bail|required|exists:\App\Models\Category,id',
            'color_id' => 'bail|required|exists:\App\Models\Color,id',
            'size_id' => 'bail|required|exists:\App\Models\Size,id',
            'currency' => 'required|string|max:5',
            'price' => 'required|numeric|min:0',
            'discount_price' => 'nullable|numeric|min:0|lt:price',
            'count' => 'required|integer|min:0',
            'main_image' => 'nullable|file|image',
            'preview_image' => 'nullable|file|image',
        ];
    }
}
