<?php

namespace App\Repositories;

use App\Models\Category;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class CategoryRepository
{
    public function paginate(int $perPage = 10): LengthAwarePaginator
    {
        $perPage = (int) max(1, min(100, $perPage));

        return Category::query()
            ->withCount('products')
            ->latest('id')
            ->paginate($perPage);
    }

    public function findByIdOrFail(int $id): Category
    {
        return Category::withCount('products')->findOrFail($id);
    }

    public function loadRelations(Category $category): Category
    {
        return $category->loadCount('products');
    }

    public function create(array $attributes): Category
    {
        return Category::create($attributes);
    }

    public function update(Category $category, array $attributes): Category
    {
        $category->update($attributes);

        return $category->refresh()->loadCount('products');
    }

    public function delete(Category $category): void
    {
        $category->delete();
    }
}
