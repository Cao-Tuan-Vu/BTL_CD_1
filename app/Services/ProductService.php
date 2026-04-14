<?php

namespace App\Services;

use App\Models\Product;
use App\Repositories\ProductRepository;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;

class ProductService
{
    protected const FEATURED_PRODUCTS_CACHE_KEY_PREFIX = 'customer.home.featured-products';

    public function __construct(protected ProductRepository $productRepository) {}

    public function getPaginatedProducts(array $filters = [], int $perPage = 10): LengthAwarePaginator
    {
        return $this->productRepository->paginate($filters, $perPage);
    }

    public function getProductById(int $productId): Product
    {
        return $this->productRepository->findByIdOrFail($productId);
    }

    public function loadProduct(Product $product): Product
    {
        return $this->productRepository->loadForShow($product);
    }

    public function getProductWithReviewsById(int $productId): Product
    {
        return $this->productRepository->findByIdWithReviewsOrFail($productId);
    }

    public function loadProductWithReviews(Product $product): Product
    {
        return $this->productRepository->loadForDetail($product);
    }

    /**
     * @return Collection<int, Product>
     */
    public function getCachedFeaturedProducts(int $limit = 8): Collection
    {
        $limit = (int) max(1, min(20, $limit));
        $cacheKey = self::FEATURED_PRODUCTS_CACHE_KEY_PREFIX.':'.$limit;

        /** @var Collection<int, Product> $products */
        $products = Cache::remember($cacheKey, now()->addMinutes(5), function () use ($limit): Collection {
            return $this->productRepository->getActiveForHome($limit);
        });

        return $products;
    }

    /**
     * @param array<int, int|string> $excludeProductIds
     * @return Collection<int, Product>
     */
    public function getHomeProductsExcluding(array $excludeProductIds = [], int $limit = 8): Collection
    {
        $limit = (int) max(1, min(20, $limit));

        return $this->productRepository->getActiveForHome($limit, $excludeProductIds);
    }

    public function createProduct(array $attributes): Product
    {
        $attributes = $this->prepareCreateAttributes($attributes);
        $product = $this->productRepository->create($attributes);
        $this->flushProductCaches();

        return $this->productRepository->loadRelations($product);
    }

    public function updateProduct(Product $product, array $attributes): Product
    {
        $attributes = $this->prepareUpdateAttributes($product, $attributes);
        $product = $this->productRepository->update($product, $attributes);
        $this->flushProductCaches();

        return $this->productRepository->loadRelations($product);
    }

    public function deleteProduct(Product $product): void
    {
        $this->deleteStoredFiles([$product->image_path]);
        $this->deleteStoredFiles($product->gallery_images ?? []);

        $this->productRepository->delete($product);
        $this->flushProductCaches();
    }

    public function removeMainImage(Product $product): Product
    {
        $this->deleteStoredFiles([$product->image_path]);

        $updatedProduct = $this->productRepository->update($product, [
            'image_path' => null,
        ]);
        $this->flushProductCaches();

        return $this->productRepository->loadRelations($updatedProduct);
    }

    public function clearGalleryImages(Product $product): Product
    {
        $this->deleteStoredFiles($product->gallery_images ?? []);

        $updatedProduct = $this->productRepository->update($product, [
            'gallery_images' => null,
        ]);
        $this->flushProductCaches();

        return $this->productRepository->loadRelations($updatedProduct);
    }

    public function flushProductCaches(): void
    {
        foreach ([8, 12] as $limit) {
            Cache::forget(self::FEATURED_PRODUCTS_CACHE_KEY_PREFIX.':'.$limit);
        }
    }

    private function prepareCreateAttributes(array $attributes): array
    {
        if (($attributes['image'] ?? null) instanceof UploadedFile) {
            $attributes['image_path'] = $attributes['image']->store('products/main', 'public');
        }

        unset($attributes['image']);

        if (isset($attributes['gallery_images']) && is_array($attributes['gallery_images'])) {
            $attributes['gallery_images'] = $this->storeGalleryImages($attributes['gallery_images']);
        }

        return $attributes;
    }

    private function prepareUpdateAttributes(Product $product, array $attributes): array
    {
        $newGalleryPaths = null;

        if (($attributes['image'] ?? null) instanceof UploadedFile) {
            $this->deleteStoredFiles([$product->image_path]);
            $attributes['image_path'] = $attributes['image']->store('products/main', 'public');
        }

        unset($attributes['image']);

        if (isset($attributes['gallery_images']) && is_array($attributes['gallery_images']) && count($attributes['gallery_images']) > 0) {
            $this->deleteStoredFiles($product->gallery_images ?? []);
            $newGalleryPaths = $this->storeGalleryImages($attributes['gallery_images']);
        }

        unset($attributes['gallery_images']);

        if (is_array($newGalleryPaths)) {
            $attributes['gallery_images'] = $newGalleryPaths;
        }

        return $attributes;
    }

    private function storeGalleryImages(array $galleryImages): array
    {
        $storedPaths = [];

        foreach ($galleryImages as $image) {
            if (! $image instanceof UploadedFile) {
                continue;
            }

            $storedPaths[] = $image->store('products/gallery', 'public');
        }

        return $storedPaths;
    }

    private function deleteStoredFiles(array $paths): void
    {
        foreach ($paths as $path) {
            if (! is_string($path) || $path === '') {
                continue;
            }

            if (str_starts_with($path, 'http://') || str_starts_with($path, 'https://')) {
                continue;
            }

            Storage::disk('public')->delete($path);
        }
    }
}
