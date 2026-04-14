<?php

namespace App\Http\Controllers\Web\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreProductRequest;
use App\Http\Requests\UpdateProductRequest;
use App\Models\Product;
use App\Services\CategoryService;
use App\Services\ProductService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class ProductWebController extends Controller
{
    public function __construct(
        protected ProductService $productService,
        protected CategoryService $categoryService,
    ) {}

    public function index(Request $request): View
    {
        $filters = $request->only(['category_id', 'min_price', 'max_price', 'status', 'q']);
        $perPage = (int) $request->integer('per_page', 10);

        $products = $this->productService->getPaginatedProducts($filters, $perPage);
        $categories = $this->categoryService->getCachedCategoryOptions();

        return view('admin.products.index', [
            'products' => $products,
            'categories' => $categories,
            'filters' => $filters,
            'perPage' => $perPage,
        ]);
    }

    public function create(): View
    {
        $categories = $this->categoryService->getCachedCategoryOptions();

        return view('admin.products.create', [
            'categories' => $categories,
        ]);
    }

    public function store(StoreProductRequest $request): RedirectResponse
    {
        $product = $this->productService->createProduct($request->validated());

        return redirect()
            ->route('admin.products.show', $product)
            ->with('success', 'Tạo sản phẩm thành công.');
    }

    public function show(Product $product): View
    {
        $product = $this->productService->loadProductWithReviews($product);

        return view('admin.products.show', [
            'product' => $product,
        ]);
    }

    public function edit(Product $product): View
    {
        $categories = $this->categoryService->getCachedCategoryOptions();

        return view('admin.products.edit', [
            'product' => $product,
            'categories' => $categories,
        ]);
    }

    public function update(UpdateProductRequest $request, Product $product): RedirectResponse
    {
        $updatedProduct = $this->productService->updateProduct($product, $request->validated());

        return redirect()
            ->route('admin.products.show', $updatedProduct)
            ->with('success', 'Cập nhật sản phẩm thành công.');
    }

    public function destroyMainImage(Product $product): RedirectResponse
    {
        $this->productService->removeMainImage($product);

        return redirect()
            ->route('admin.products.edit', $product)
            ->with('success', 'Đã xóa ảnh đại diện của sản phẩm.');
    }

    public function destroyGalleryImages(Product $product): RedirectResponse
    {
        $this->productService->clearGalleryImages($product);

        return redirect()
            ->route('admin.products.edit', $product)
            ->with('success', 'Đã xóa bộ ảnh chi tiết của sản phẩm.');
    }

    public function destroy(Product $product): RedirectResponse
    {
        $this->productService->deleteProduct($product);

        return redirect()
            ->route('admin.products.index')
            ->with('success', 'Xóa sản phẩm thành công.');
    }
}
