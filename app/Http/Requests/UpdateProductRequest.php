<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

/**
 * A form request class for updating product data.
 *
 * This class defines the validation rules and authorization for updating product information.
 * It is used when processing HTTP requests related to updating existing products.
 */
class UpdateProductRequest extends FormRequest
{
    /** Determine if the user is authorized to make this request. */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, array|string|ValidationRule>
     */
    public function rules(): array
    {
        return [
            'name' => 'required|max:255',
            'description' => 'required',
            'price' => 'required|numeric|between:0,999999.99',
            'quantity' => 'required|integer|min:0',
            'images' => 'sometimes|array|max:4',
            'images.*' => 'image|mimes:jpeg,png,jpg,gif,svg|max:10240',
        ];
    }
}
