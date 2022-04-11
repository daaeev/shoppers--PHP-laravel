<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class EditTeammate extends FormRequest
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
            'id' => 'bail|required|integer|exists:\App\Models\Teammate,id',
            'full_name' => 'required|string|max:30',
            'description' => 'required|string|max:255',
            'position' => 'required|string|max:30',
            'image' => 'nullable|file|image',
        ];
    }
}
