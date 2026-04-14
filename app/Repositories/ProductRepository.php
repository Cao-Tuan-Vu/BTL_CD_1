<?php

namespace App\Repositories;

use App\Models\Product;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

class ProductRepository
{
    /**
     * @param array<int, int|string> $excludeProductIds
     * @return Collection<int, Product>
     */
    public function getActiveForHome(int $limit = 8, array $excludeProductIds = []): Collection
    {
        $limit = (int) max(1, min(20, $limit));
        $excludeProductIds = collect($excludeProductIds)
            ->map(static fn ($productId): int => (int) $productId)
            ->filter(static fn (int $productId): bool => $productId > 0)
            ->unique()
            ->values()
            ->all();

        $query = Product::query()
            ->select([
                'id',
                'name',
                'description',
                'image_path',
                'gallery_images',
                'price',
                'status',
                'stock',
                'category_id',
            ])
            ->with('category:id,name')
            ->withCount('reviews')
            ->withAvg('reviews', 'rating')
            ->where('status', 'active');

        if ($excludeProductIds !== []) {
            $query->whereNotIn('id', $excludeProductIds);
        }

        return $query->latest('id')->limit($limit)->get();
    }

    public function paginate(array $filters = [], int $perPage = 10): LengthAwarePaginator
    {
        $query = Product::query()
            ->select([
                'id',
                'name',
                'description',
                'image_path',
                'gallery_images',
                'price',
                'status',
                'stock',
                'category_id',
                'created_at',
                'updated_at',
            ])
            ->with('category:id,name')
            ->withCount('reviews')
            ->withAvg('reviews', 'rating');

        if (! empty($filters['category_id'])) {
            $query->where('category_id', $filters['category_id']);
        }

        if (! empty($filters['min_price'])) {
            $query->where('price', '>=', $filters['min_price']);
        }

        if (! empty($filters['max_price'])) {
            $query->where('price', '<=', $filters['max_price']);
        }

        if (! empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (! empty($filters['q'])) {
            $search = trim((string) $filters['q']);
            $query->where(function ($builder) use ($search): void {
                $builder->where('name', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%");
            });
        }

        $perPage = (int) max(1, min(100, $perPage));

        return $query->latest('id')->paginate($perPage);
    }

    public function findByIdOrFail(int $id): Product
    {
        return Product::query()
            ->with('category:id,name')
            ->withCount('reviews')
            ->withAvg('reviews', 'rating')
            ->findOrFail($id);
    }

    public function findByIdWithReviewsOrFail(int $id): Product
    {
        return Product::query()
            ->withCount('reviews')
            ->withAvg('reviews', 'rating')
            ->with([
                'category:id,name',
                'reviews' => function ($builder): void {
                    $builder
                        ->select(['id', 'product_id', 'user_id', 'rating', 'comment', 'created_at'])
                        ->with('user:id,name')
                        ->latest('id');
                },
            ])
            ->findOrFail($id);
    }

    public function loadForShow(Product $product): Product
    {
        return $product
            ->load('category:id,name')
            ->loadCount('reviews')
            ->loadAvg('reviews', 'rating');
    }

    public function loadForDetail(Product $product): Product
    {
        return $product
            ->load([
                'category:id,name',
                'reviews' => function ($builder): void {
                    $builder
                        ->select(['id', 'product_id', 'user_id', 'rating', 'comment', 'created_at'])
                        ->with('user:id,name')
                        ->latest('id');
                },
            ])
            ->loadCount('reviews')
            ->loadAvg('reviews', 'rating');
    }

    public function create(array $attributes): Product
    {
        return Product::create($attributes);
    }

    public function update(Product $product, array $attributes): Product
    {
        $product->update($attributes);

        return $product->refresh();
    }

    public function delete(Product $product): void
    {
        $product->delete();
    }

    public function getByIdsForUpdate(array $productIds): Collection
    {
        return Product::query()
            ->whereIn('id', $productIds)
            ->lockForUpdate()
            ->get()
            ->keyBy('id');
    }

    public function decrementStock(Product $product, int $quantity): Product
    {
        $product->decrement('stock', $quantity);

        return $product->refresh();
    }

    public function loadRelations(Product $product): Product
    {
        return $product->load('category');
    }
}
