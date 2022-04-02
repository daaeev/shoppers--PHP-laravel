<?php

namespace App\Http\Requests\ajax;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;

class Cart extends FormRequest
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
            'product_id' => 'bail|required|integer|exists:\App\Models\Product,id',
        ];
    }

    public function failedValidation(Validator $validator)
    {
        throw new \Exception('', 404);
    }
}
