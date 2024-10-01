<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class UpdateReviewRequest extends FormRequest
{
    /** Determine if the user is authorized to make this request. */
    public function authorize(): bool
    {
        $review = $this->route('review');

        return auth()->id() === $review->user_id;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, array|string|ValidationRule>
     */
    public function rules(): array
    {
        return [
            'rating' => 'required|integer|min:1|max:5',
            'review' => 'required|string|max:500',
        ];
    }
}
