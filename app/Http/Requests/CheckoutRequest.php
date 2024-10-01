<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

/**
 * A form request class for handling checkout data validation.
 *
 * This class determines if a user is authorized to initiate the checkout process
 * and defines the validation rules that the provided checkout data must satisfy.
 */
class CheckoutRequest extends FormRequest
{
    /** Determine if the user is authorized to make this request. */
    public function authorize(): bool
    {
        return auth()->check();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, array|string|ValidationRule>
     */
    public function rules(): array
    {
        return [
            'email' => 'required|email',
            'phone_number' => 'required',
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'address' => 'required|string|max:255',
            'city' => 'required|string|max:255',
            'country' => 'required|string|max:255',
            'state_province' => 'sometimes|string|max:255',
            'postal_code' => 'required|string|max:255',
            'delivery_method' => 'required|in:Standard,Express',
        ];
    }
}
