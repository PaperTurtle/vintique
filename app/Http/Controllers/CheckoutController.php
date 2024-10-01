<?php

namespace App\Http\Controllers;

use App\Http\Requests\{AddToCartRequest, CheckoutRequest, UpdateCartRequest};
use App\Models\ShoppingCart;
use App\Services\{CartService, CheckoutService};
use Exception;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;

class CheckoutController extends Controller
{
    public function __construct(protected CartService $cartService, protected CheckoutService $checkoutService) {}

    public function index(): View
    {
        $cartItems = auth()->user()->cart()->with(['product', 'product.images'])->get();

        return view('checkout.index', compact('cartItems'));
    }

    public function process(): View
    {
        $cartItems = auth()->user()->cart()->with('product')->get();

        return view('checkout.process', compact('cartItems'));
    }

    public function addToCart(AddToCartRequest $request): JsonResponse
    {
        $this->authorize('addToCart', ShoppingCart::class);

        $response = $this->cartService->addToCart($request->product_id, $request->quantity);

        return response()->json($response);
    }

    public function removeFromCart(int $cartItemId): JsonResponse
    {
        $cartItem = auth()->user()->cart()->findOrFail($cartItemId);

        $this->authorize('removeFromCart', $cartItem);

        $cartItem->delete();

        return response()->json(["success" => "Product removed from cart!"]);
    }

    public function updateCart(int $itemId, UpdateCartRequest $request): JsonResponse
    {
        $cartItem = auth()->user()->cart()->findOrFail($itemId);

        $this->authorize('update', $cartItem);

        $cartItem->update([
            'quantity' => $request->quantity,
        ]);

        return response()->json(['success' => 'Cart updated successfully']);
    }

    public function processCheckout(CheckoutRequest $request): JsonResponse
    {
        $response = $this->checkoutService->processCheckout(auth()->user(), $request->validated());

        if ($response['status'] !== 'success') {
            return response()->json([
                'status' => 'error',
                'message' => $response['message'],
            ], 422);
        }

        session(['transactionDetails' => $response['transactionDetails']]);

        return response()->json([
            'status' => 'success',
            'message' => $response['message'],
            'transactionDetails' => $response['transactionDetails'],
            'redirectUrl' => route('checkout.success'),
        ]);
    }
}
