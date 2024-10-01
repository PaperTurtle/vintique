<?php

namespace App\Http\Controllers;

use App\Http\Requests\{StoreReviewRequest, UpdateReviewRequest};
use App\Models\Review;
use Illuminate\Http\JsonResponse;

class ReviewController extends Controller
{
    public function store(StoreReviewRequest $request): JsonResponse
    {
        $review = Review::create([
            'product_id' => $request->product_id,
            'user_id' => auth()->id(),
            'rating' => $request->rating,
            'review' => $request->review,
        ]);

        return response()->json($review->load('user'));
    }

    public function update(UpdateReviewRequest $request, Review $review): JsonResponse
    {
        $review->update($request->validated());

        return response()->json($review);
    }

    /**
     * Delete an existing review.
     * Deletes the review if the authenticated user owns it.
     * Returns a JSON response with a success message.
     *
     * @param  int          $id the ID of the review to be deleted
     * @return JsonResponse returns JSON response with a success message
     */
    public function destroy(int $id): JsonResponse
    {
        $review = Review::find($id);
        $review->delete();

        return response()->json(['message' => 'Review deleted successfully']);
    }
}
