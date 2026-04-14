<?php

namespace App\Services;

use App\Models\Category;
use App\Repositories\CategoryRepository;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;

class CategoryService
{
    protected const CATEGORY_OPTIONS_CACHE_KEY = 'categories.options';

    public function __construct(protected CategoryRepository $categoryRepository) {}

    public function getPaginatedCategories(int $perPage = 10): LengthAwarePaginator
    {
        return $this->categoryRepository->paginate($perPage);
    }

    public function getCategoryById(int $categoryId): Category
    {
        return $this->categoryRepository->findByIdOrFail($categoryId);
    }

    public function loadCategory(Category $category): Category
    {
        return $this->categoryRepository->loadRelations($category);
    }

    /**
     * @return Collection<int, Category>
     */
    public function getCachedCategoryOptions(): Collection
    {
        /** @var Collection<int, Category> $categories */
        $categories = Cache::remember(self::CATEGORY_OPTIONS_CACHE_KEY, now()->addMinutes(10), function (): Collection {
            return Category::query()->orderBy('name')->get(['id', 'name']);
        });

        return $categories;
    }

    public function createCategory(array $attributes): Category
    {
        $category = $this->categoryRepository->create($attributes)->loadCount('products');
        $this->flushCategoryCaches();

        return $category;
    }

    public function updateCategory(Category $category, array $attributes): Category
    {
        $updatedCategory = $this->categoryRepository->update($category, $attributes);
        $this->flushCategoryCaches();

        return $updatedCategory;
    }

    public function deleteCategory(Category $category): void
    {
        $this->categoryRepository->delete($category);
        $this->flushCategoryCaches();
    }

    protected function flushCategoryCaches(): void
    {
        Cache::forget(self::CATEGORY_OPTIONS_CACHE_KEY);
    }
}
