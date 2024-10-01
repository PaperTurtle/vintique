<?php

namespace App\Http\Controllers;

use App\Http\Requests\{StoreProductRequest, UpdateProductRequest};
use App\Models\{Product, ProductImage};
use App\Services\{ImageService, ProductQueryService};
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Contracts\View\Factory;
use Illuminate\Http\{JsonResponse, RedirectResponse, Request};
use Illuminate\View\View;

class ProductController extends Controller
{
    public const MAX_IMAGES = 4;

    public function __construct(protected ImageService $imageService, protected ProductQueryService $productQueryService) {}


    public function index(Request $request): View
    {
        $searchTerm = $request->query('search', '');
        $selectedCategories = is_array($request->query('categories', [])) ? $request->query('categories', []) : [$request->query('categories', [])];
        $sort = $request->query('sort');

        $products = Product::query();
        $products = $this->productQueryService->applySearch($products, $searchTerm);
        $products = $this->productQueryService->applyCategoryFilter($products, $selectedCategories);
        $products = $this->productQueryService->applySorting($products, $sort);

        return view("products.index", ['products' => $products->get()]);
    }


    public function show(Product $product): View
    {
        $product->load(['reviews' => function ($query) {
            $query->orderBy('created_at', 'desc')->with('user.profile');
        }]);

        return view('products.show', [
            'product' => $product,
            'averageRating' => $product->averageRating(),
            'totalReviews' => $product->totalReviews(),
        ]);
    }

    public function create(): View
    {
        $this->authorize('create', Product::class);

        return view('products.create');
    }

    public function store(StoreProductRequest $request): RedirectResponse
    {
        $this->authorize('create', Product::class);

        $validatedData = $request->validated();

        $product = Product::create($validatedData);

        $this->imageService->processAndStoreImages($product, $request->file('images'), $validatedData['name']);

        return to_route('products.show', $product->id)->with('success', 'Product created successfully.');
    }

    public function edit(Product $product): View
    {
        $this->authorize('edit', $product);

        return view('products.edit', compact('product'));
    }

    public function update(UpdateProductRequest $request, Product $product): RedirectResponse
    {
        $this->authorize('update', $product);

        $validatedData = $request->validated();
        $product->update($validatedData);

        if ($request->hasFile('images')) {
            $currentImageCount = $product->images()->count();
            $allowedNewImages = self::MAX_IMAGES - $currentImageCount;

            if ($allowedNewImages > 0) {
                $images = array_slice($request->file('images'), 0, $allowedNewImages);
                $this->imageService->processAndStoreImages($product, $images, $validatedData['description']);
            }
        }

        return to_route('products.show', $product->id)->with('success', 'Product updated successfully.');
    }

    public function destroy(Product $product): RedirectResponse
    {
        $this->authorize('delete', $product);

        $product->delete();

        return to_route('products.index')->with('success', 'Product deleted successfully.');
    }

    public function destroyImage(Product $product, ProductImage $productImage): JsonResponse
    {
        $this->authorize('deleteImage', [$product, $productImage]);

        $this->imageService->deleteImage($productImage);

        return response()->json([
            'success' => true,
            'message' => 'Image deleted successfully.',
        ]);
    }

    public function topRatedProducts(): View
    {
        $topRatedProducts = Product::with('reviews')
            ->withAvg('reviews', 'rating')
            ->orderByDesc('reviews_avg_rating')
            ->take(3)
            ->get();

        return view('welcome', ['topRatedProducts' => $topRatedProducts]);
    }
}
