<?php

namespace App\Http\Controllers;

// Import các class cần thiết
use App\Http\Requests\StoreReviewRequest; // Validate khi tạo đánh giá
use App\Http\Requests\UpdateReviewRequest; // Validate khi cập nhật đánh giá
use App\Http\Resources\ReviewResource; // Resource trả dữ liệu API
use App\Models\Review; // Model Review
use App\Services\ReviewService; // Service xử lý logic nghiệp vụ
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Symfony\Component\HttpFoundation\Response;

class ReviewController extends Controller
{
    // Inject ReviewService vào controller
    public function __construct(protected ReviewService $reviewService) {}

    /**
     * Lấy danh sách đánh giá (có filter + phân trang)
     */
    public function index(Request $request): AnonymousResourceCollection
    {
        // Kiểm tra quyền xem danh sách đánh giá
        $this->authorize('viewAny', Review::class);

        // Lấy điều kiện lọc:
        // product_id: lọc theo sản phẩm
        // user_id: lọc theo người đánh giá
        $filters = $request->only(['product_id', 'user_id']);

        // Số lượng bản ghi mỗi trang (mặc định = 10)
        $perPage = (int) $request->integer('per_page', 10);

        // Gọi service để lấy danh sách đánh giá
        $reviews = $this->reviewService->getPaginatedReviews($filters, $perPage);

        // Trả về dữ liệu dạng resource collection
        return ReviewResource::collection($reviews);
    }

    /**
     * Tạo mới đánh giá
     */
    public function store(StoreReviewRequest $request)
    {
        // Kiểm tra quyền tạo đánh giá
        $this->authorize('create', Review::class);

        // Lấy dữ liệu đã validate
        $validated = $request->validated();

        // Gán user_id = id của user đang đăng nhập
        $validated['user_id'] = (int) $request->user()->id;

        // Gọi service để tạo đánh giá
        $review = $this->reviewService->createReview($validated);

        // Trả về dữ liệu + HTTP 201 (Created)
        return (new ReviewResource($review))
            ->response()
            ->setStatusCode(Response::HTTP_CREATED);
    }

    /**
     * Xem chi tiết đánh giá
     */
    public function show(Review $review): ReviewResource
    {
        // Kiểm tra quyền xem đánh giá
        $this->authorize('view', $review);

        // Load thêm dữ liệu liên quan (user, product,...)
        $review = $this->reviewService->loadReview($review);

        // Trả về resource
        return new ReviewResource($review);
    }

    /**
     * Cập nhật đánh giá
     */
    public function update(UpdateReviewRequest $request, Review $review): ReviewResource
    {
        // Kiểm tra quyền cập nhật
        $this->authorize('update', $review);

        // Gọi service để cập nhật đánh giá
        $updatedReview = $this->reviewService->updateReview(
            $review,
            $request->validated()
        );

        // Trả về dữ liệu sau khi cập nhật
        return new ReviewResource($updatedReview);
    }

    /**
     * Xóa đánh giá
     */
    public function destroy(Review $review): JsonResponse
    {
        // Kiểm tra quyền xóa
        $this->authorize('delete', $review);

        // Gọi service để xóa đánh giá
        $this->reviewService->deleteReview($review);

        // Trả về JSON thông báo thành công
        return response()->json([
            'message' => 'Xóa đánh giá thành công.',
        ], Response::HTTP_OK);
    }
}