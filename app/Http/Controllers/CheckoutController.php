<?php

namespace App\Http\Controllers;

use App\Http\Requests\{AddToCartRequest, CheckoutRequest, UpdateCartRequest};
use App\Models\ShoppingCart;
use App\Services\{CartService, CheckoutService};
use Exception;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;

/**
 * CheckoutController handles operations related to managing the shopping cart and processing the checkout in an e-commerce context.
 * This includes displaying the checkout page, adding/removing items from the cart, updating cart items, and processing the final checkout transaction.
 */
class CheckoutController extends Controller
{
    public function __construct(protected CartService $cartService, protected CheckoutService $checkoutService) {}

    /**
     * Display the checkout page with items in the authenticated user's shopping cart.
     * Retrieves all cart items associated with the current authenticated user and passes them to the checkout view.
     *
     * @return View returns a view of the checkout page with cart items
     */
    public function index(): View
    {
        $cartItems = ShoppingCart::with(['product', 'product.images'])
            ->where('user_id', \auth()->id())
            ->get();

        return \view('checkout.index', \compact('cartItems'));
    }

    /**
     * Prepare the checkout process by retrieving the current user's shopping cart items
     * and displaying them on the checkout page.
     *
     * @return View returns a view of the checkout page pre-populated with the user's cart items and product details
     */
    public function process(): View
    {
        $user = \auth()->user();
        $cartItems = ShoppingCart::with('product')->where('user_id', \auth()->id())->get();

        return \view('checkout.process', \compact('user', 'cartItems'));
    }

    /**
     * Display the checkout success page.
     *
     * @return View returns a view of the checkout success page
     */
    public function success(): View
    {
        return \view('checkout.success');
    }

    /**
     * Add a product to the authenticated user's shopping cart.
     * If the user is not authenticated, they are redirected to the login page.
     * If the product already exists in the cart, its quantity is updated; otherwise, a new cart item is created.
     *
     * @param AddToCartRequest $request the request object containing product details
     *
     * @throws AuthorizationException if the user is not authenticated
     *
     * @return JsonResponse redirects back with a success message on adding the product
     */
    public function addToCart(AddToCartRequest $request): JsonResponse
    {
        $this->authorize('addToCart', ShoppingCart::class);

        $response = $this->cartService->addToCart($request->product_id, $request->quantity);

        return \response()->json($response);
    }

    /**
     * Remove an item from the shopping cart by its item ID.
     * Only affects the item specified by the provided cart item ID.
     *
     * @param int $cartItemId the unique identifier of the cart item to be removed
     *
     * @throws AuthorizationException if the user is not authenticated
     *
     * @return JsonResponse redirects back with a success message on removing the product
     */
    public function removeFromCart(int $cartItemId): JsonResponse
    {
        $cartItem = ShoppingCart::where('user_id', \auth()->id())->findOrFail($cartItemId);

        $this->authorize('removeFromCart', $cartItem);

        $cartItem->delete();

        return \response()->json(["success" => "Product removed from cart!"]);
    }

    /**
     * Update the quantity of an item in the shopping cart.
     * If the cart item exists and belongs to the authenticated user, its quantity is updated.
     * Responds with JSON indicating the success or failure of the operation.
     *
     * @param int               $itemId  the ID of the cart item to update
     * @param UpdateCartRequest $request the request object containing the new quantity
     *
     * @throws AuthorizationException if the user is not authenticated
     *
     * @return JsonResponse returns JSON response with the result of the update operation
     */
    public function updateCart(int $itemId, UpdateCartRequest $request): JsonResponse
    {
        $cartItem = ShoppingCart::where('user_id', \auth()->id())->findOrFail($itemId);

        $this->authorize('update', $cartItem);

        $cartItem->quantity = $request->quantity;
        $cartItem->save();

        return \response()->json(['success' => 'Cart updated successfully']);
    }

    /**
     * Process the checkout by creating transactions for each item in the cart and updating product quantities.
     * On success, the user's cart is cleared and redirected to a success route.
     * On failure, the transaction is rolled back and the user is redirected back with an error message.
     *
     * @throws Exception if an unexpected error occurs during the process
     *
     * @return JsonResponse redirects to a success route on successful checkout, or back with an error on failure
     */
    public function processCheckout(CheckoutRequest $request): JsonResponse
    {
        $user = \auth()->user();
        $buyerData = $request->validated();

        $response = $this->checkoutService->processCheckout($user, $buyerData);

        if ($response['status'] === 'success') {
            \session(['transactionDetails' => $response['transactionDetails']]);

            return \response()->json([
                'status' => 'success',
                'message' => $response['message'],
                'transactionDetails' => $response['transactionDetails'],
                'redirectUrl' => \route('checkout.success'),
            ]);
        }

        return \response()->json([
            'status' => 'error',
            'message' => $response['message'],
        ], 422);
    }
}
