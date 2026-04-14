<?php

namespace App\Http\Controllers\Web\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreCategoryRequest;
use App\Http\Requests\UpdateCategoryRequest;
use App\Models\Category;
use App\Models\Product;
use App\Services\CategoryService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class CategoryWebController extends Controller
{
    public function __construct(protected CategoryService $categoryService) {}

    public function index(Request $request): View
    {
        $perPage = (int) $request->integer('per_page', 10);
        $categories = $this->categoryService->getPaginatedCategories($perPage);

        return view('admin.categories.index', [
            'categories' => $categories,
            'perPage' => $perPage,
        ]);
    }

    public function create(): View
    {
        return view('admin.categories.create');
    }

    public function store(StoreCategoryRequest $request): RedirectResponse
    {
        $category = $this->categoryService->createCategory($request->validated());

        return redirect()
            ->route('admin.categories.show', $category)
            ->with('success', 'Tạo danh mục thành công.');
    }

    public function show(Category $category): View
    {
        $category = $this->categoryService->loadCategory($category);
        $products = Product::query()
            ->select(['id', 'name', 'price', 'stock', 'status', 'category_id', 'created_at'])
            ->where('category_id', $category->id)
            ->latest('id')
            ->paginate(10);

        return view('admin.categories.show', [
            'category' => $category,
            'products' => $products,
        ]);
    }

    public function edit(Category $category): View
    {
        return view('admin.categories.edit', [
            'category' => $category,
        ]);
    }

    public function update(UpdateCategoryRequest $request, Category $category): RedirectResponse
    {
        $updatedCategory = $this->categoryService->updateCategory($category, $request->validated());

        return redirect()
            ->route('admin.categories.show', $updatedCategory)
            ->with('success', 'Cập nhật danh mục thành công.');
    }

    public function destroy(Category $category): RedirectResponse
    {
        $this->categoryService->deleteCategory($category);

        return redirect()
            ->route('admin.categories.index')
            ->with('success', 'Xóa danh mục thành công.');
    }
}
