<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SkuRequest extends FormRequest
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
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
            'product_id' => 'required|integer|exists:products,id',
            'price' => 'required|numeric|min:0.01',
            'count' => 'required|integer|min:0',
            'option_id' => 'nullable|array',
            'option_id.*' => 'required|integer|exists:options,id',
            'img' => 'nullable|array',
            'img.*' => 'image|mimes:jpeg,png,jpg',
        ];
    }
}
