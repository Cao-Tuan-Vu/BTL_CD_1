<?php

namespace App\Http\Controllers;

// Import các class cần thiết
use App\Http\Requests\StoreProductRequest; // Validate khi tạo sản phẩm
use App\Http\Requests\UpdateProductRequest; // Validate khi cập nhật sản phẩm
use App\Http\Resources\ProductResource; // Resource trả dữ liệu API
use App\Models\Product; // Model Product
use App\Services\ProductService; // Service xử lý logic nghiệp vụ
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Symfony\Component\HttpFoundation\Response;

class ProductController extends Controller
{
    // Inject ProductService vào controller
    public function __construct(protected ProductService $productService) {}

    /**
     * Lấy danh sách sản phẩm (có filter + phân trang)
     */
    public function index(Request $request): AnonymousResourceCollection
    {
        // Kiểm tra quyền xem danh sách sản phẩm
        $this->authorize('viewAny', Product::class);

        // Lấy các điều kiện lọc từ request
        // category_id: lọc theo danh mục
        // min_price, max_price: lọc theo khoảng giá
        // status: trạng thái sản phẩm
        // q: từ khóa tìm kiếm
        $filters = $request->only(['category_id', 'min_price', 'max_price', 'status', 'q']);

        // Số lượng sản phẩm mỗi trang (mặc định 10)
        $perPage = (int) $request->integer('per_page', 10);

        // Gọi service để lấy danh sách sản phẩm
        $products = $this->productService->getPaginatedProducts($filters, $perPage);

        // Trả về dữ liệu dạng resource collection
        return ProductResource::collection($products);
    }

    /**
     * Tạo mới sản phẩm
     */
    public function store(StoreProductRequest $request)
    {
        // Kiểm tra quyền tạo sản phẩm
        $this->authorize('create', Product::class);

        // Gọi service để tạo sản phẩm (dữ liệu đã validate)
        $product = $this->productService->createProduct($request->validated());

        // Trả về dữ liệu + HTTP 201 (Created)
        return (new ProductResource($product))
            ->response()
            ->setStatusCode(Response::HTTP_CREATED);
    }

    /**
     * Xem chi tiết sản phẩm
     */
    public function show(Product $product): ProductResource
    {
        // Kiểm tra quyền xem sản phẩm
        $this->authorize('view', $product);

        // Load thêm dữ liệu liên quan (category, images,...)
        $product = $this->productService->loadProduct($product);

        // Trả về resource
        return new ProductResource($product);
    }

    /**
     * Cập nhật sản phẩm
     */
    public function update(UpdateProductRequest $request, Product $product): ProductResource
    {
        // Kiểm tra quyền cập nhật
        $this->authorize('update', $product);

        // Gọi service để cập nhật sản phẩm
        $product = $this->productService->updateProduct($product, $request->validated());

        // Trả về dữ liệu sau khi cập nhật
        return new ProductResource($product);
    }

    /**
     * Xóa sản phẩm
     */
    public function destroy(Product $product): JsonResponse
    {
        // Kiểm tra quyền xóa
        $this->authorize('delete', $product);

        // Gọi service để xóa sản phẩm
        $this->productService->deleteProduct($product);

        // Trả về JSON thông báo thành công
        return response()->json([
            'message' => 'Xóa sản phẩm thành công.',
        ], Response::HTTP_OK);
    }
}