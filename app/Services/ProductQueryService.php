<?php

namespace App\Services;

use Illuminate\Database\Eloquent\Builder;

/**
 * ProductQueryService is a service class responsible for handling query-related operations for products.
 * It provides functionalities for applying search criteria, category filters, and sorting to product queries,
 * thereby streamlining query customization and enhancing code reusability.
 */
class ProductQueryService
{
    /**
     * Apply search criteria to a product query.
     * Filters products based on a search term that matches the product's name.
     *
     * @param  Builder $query      the Eloquent query builder instance
     * @param  string  $searchTerm the search term used for filtering products
     * @return Builder the modified query builder with the search condition applied
     */
    public function applySearch(Builder $query, string $searchTerm): Builder
    {
        if ($searchTerm) {
            return $query->where('name', 'LIKE', '%' . $searchTerm . '%');
        }

        return $query;
    }

    /**
     * Apply category filter to a product query.
     * Filters products based on selected category IDs.
     *
     * @param  Builder $query              the Eloquent query builder instance
     * @param  array   $selectedCategories an array of selected category IDs for filtering
     * @return Builder the modified query builder with the category filter applied
     */
    public function applyCategoryFilter(Builder $query, array $selectedCategories): Builder
    {
        if (count($selectedCategories)) {
            return $query->whereIn('category_id', $selectedCategories);
        }

        return $query;
    }

    /**
     * Apply sorting to a product query.
     * Sorts products based on the specified sorting criteria such as rating, price ascending, or price descending.
     *
     * @param  Builder     $query the Eloquent query builder instance
     * @param  string|null $sort  the sorting criteria
     * @return Builder     the modified query builder with the sorting applied
     */
    public function applySorting(Builder $query, ?string $sort): Builder
    {
        return match ($sort) {
            'rating' => $query->withAvg('reviews', 'rating')->orderBy('reviews_avg_rating', 'desc'),
            'price_asc' => $query->orderBy('price'),
            'price_desc' => $query->orderBy('price', 'desc'),
            default => $query,
        };
    }
}
