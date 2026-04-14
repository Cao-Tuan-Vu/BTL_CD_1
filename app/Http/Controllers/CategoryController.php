<?php

namespace App\Http\Controllers;

// Import các class cần thiết
use App\Http\Requests\StoreCategoryRequest; // Request validate khi tạo mới
use App\Http\Requests\UpdateCategoryRequest; // Request validate khi cập nhật
use App\Http\Resources\CategoryResource; // Resource trả dữ liệu API
use App\Models\Category; // Model Category
use App\Services\CategoryService; // Service xử lý logic
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Symfony\Component\HttpFoundation\Response;

class CategoryController extends Controller
{
    // Inject CategoryService vào controller
    public function __construct(protected CategoryService $categoryService) {}

    /**
     * Lấy danh sách danh mục (có phân trang)
     */
    public function index(Request $request): AnonymousResourceCollection
    {
        // Kiểm tra quyền xem danh sách
        $this->authorize('viewAny', Category::class);

        // Lấy số bản ghi mỗi trang, mặc định = 10
        $perPage = (int) $request->integer('per_page', 10);

        // Gọi service để lấy danh sách phân trang
        $categories = $this->categoryService->getPaginatedCategories($perPage);

        // Trả về dạng resource collection (chuẩn API)
        return CategoryResource::collection($categories);
    }

    /**
     * Tạo mới danh mục
     */
    public function store(StoreCategoryRequest $request)
    {
        // Kiểm tra quyền tạo
        $this->authorize('create', Category::class);

        // Gọi service để tạo dữ liệu (đã validate)
        $category = $this->categoryService->createCategory($request->validated());

        // Trả về dữ liệu + HTTP 201 (Created)
        return (new CategoryResource($category))
            ->response()
            ->setStatusCode(Response::HTTP_CREATED);
    }

    /**
     * Xem chi tiết 1 danh mục
     */
    public function show(Category $category): CategoryResource
    {
        // Kiểm tra quyền xem
        $this->authorize('view', $category);

        // Load thêm dữ liệu liên quan nếu có
        $category = $this->categoryService->loadCategory($category);

        // Trả về resource
        return new CategoryResource($category);
    }

    /**
     * Cập nhật danh mục
     */
    public function update(UpdateCategoryRequest $request, Category $category): CategoryResource
    {
        // Kiểm tra quyền cập nhật
        $this->authorize('update', $category);

        // Gọi service để cập nhật dữ liệu
        $category = $this->categoryService->updateCategory($category, $request->validated());

        // Trả về dữ liệu sau khi cập nhật
        return new CategoryResource($category);
    }

    /**
     * Xóa danh mục
     */
    public function destroy(Category $category): JsonResponse
    {
        // Kiểm tra quyền xóa
        $this->authorize('delete', $category);

        // Gọi service để xóa
        $this->categoryService->deleteCategory($category);

        // Trả về JSON thông báo thành công
        return response()->json([
            'message' => 'Xóa danh mục thành công.',
        ], Response::HTTP_OK);
    }
}
// End of CategoryController.php