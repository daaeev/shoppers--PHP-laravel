<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CreateProduct extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'name' => 'bail|required|max:255|unique:\App\Models\Product,name',
            'subname' => 'required|max:255',
            'description' => 'required',
            'category_id' => 'bail|required|exists:\App\Models\Category,id',
            'color_id' => 'bail|required|exists:\App\Models\Color,id',
            'size_id' => 'bail|required|exists:\App\Models\Size,id',
            'currency' => 'required|string|max:5',
            'price' => 'required|numeric|min:0',
            'discount_price' => 'nullable|numeric|min:0|lt:price',
            'count' => 'required|integer|min:0',
            'main_image' => 'required|file|image',
            'preview_image' => 'nullable|file|image',
        ];
    }
}
