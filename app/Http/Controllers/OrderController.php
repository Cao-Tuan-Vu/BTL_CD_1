<?php

namespace App\Http\Controllers;

// Import các class cần thiết
use App\Http\Requests\StoreOrderRequest; // Validate khi tạo đơn hàng
use App\Http\Requests\UpdateOrderStatusRequest; // Validate khi cập nhật trạng thái
use App\Http\Resources\OrderResource; // Resource trả dữ liệu API
use App\Models\Order; // Model Order
use App\Services\OrderService; // Service xử lý logic nghiệp vụ
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Symfony\Component\HttpFoundation\Response;

class OrderController extends Controller
{
    // Inject OrderService vào controller
    public function __construct(protected OrderService $orderService) {}

    /**
     * Lấy danh sách đơn hàng (có filter + phân trang)
     */
    public function index(Request $request): AnonymousResourceCollection
    {
        // Kiểm tra quyền xem danh sách đơn hàng
        $this->authorize('viewAny', Order::class);

        // Lấy các điều kiện lọc (user_id, status)
        $filters = $request->only(['user_id', 'status']);

        // Số bản ghi mỗi trang (mặc định = 10)
        $perPage = (int) $request->integer('per_page', 10);

        // Gọi service để lấy danh sách đơn hàng
        $orders = $this->orderService->getPaginatedOrders($filters, $perPage);

        // Trả về dữ liệu dạng resource collection
        return OrderResource::collection($orders);
    }

    /**
     * Tạo mới đơn hàng
     */
    public function store(StoreOrderRequest $request)
    {
        // Kiểm tra quyền tạo đơn
        $this->authorize('create', Order::class);

        // Lấy dữ liệu đã validate
        $validated = $request->validated();

        // Lấy user đang đăng nhập
        $actingUser = $request->user();

        // Kiểm tra: chỉ customer mới được đặt hàng
        if (! $actingUser || $actingUser->role !== 'customer') {
            abort(Response::HTTP_FORBIDDEN, 'Chỉ khách hàng mới được tạo đơn hàng.');
        }

        // Gọi service để tạo đơn hàng
        $order = $this->orderService->createOrder(
            (int) $actingUser->id, // ID người đặt
            $validated['items'], // Danh sách sản phẩm
            $validated['shipping_address'], // Địa chỉ giao hàng
            $validated['shipping_phone'], // SĐT giao hàng
            $validated['payment_method'] ?? Order::PAYMENT_METHOD_CASH, // Phương thức thanh toán (mặc định tiền mặt)
        );

        // Trả về dữ liệu + HTTP 201 (Created)
        return (new OrderResource($order))
            ->response()
            ->setStatusCode(Response::HTTP_CREATED);
    }

    /**
     * Xem chi tiết đơn hàng
     */
    public function show(Order $order): OrderResource
    {
        // Kiểm tra quyền xem đơn hàng
        $this->authorize('view', $order);

        // Load thêm thông tin liên quan (items, user...)
        $order = $this->orderService->loadOrder($order);

        // Trả về resource
        return new OrderResource($order);
    }

    /**
     * Cập nhật trạng thái đơn hàng
     */
    public function update(UpdateOrderStatusRequest $request, Order $order): OrderResource
    {
        // Kiểm tra quyền cập nhật
        $this->authorize('update', $order);

        // Gọi service để cập nhật trạng thái
        $updatedOrder = $this->orderService->updateOrderStatus(
            $order,
            $request->validated()['status']
        );

        // Trả về dữ liệu sau khi cập nhật
        return new OrderResource($updatedOrder);
    }

    /**
     * Xóa đơn hàng
     */
    public function destroy(Order $order): JsonResponse
    {
        // Kiểm tra quyền xóa
        $this->authorize('delete', $order);

        // Gọi service để xóa đơn
        $this->orderService->deleteOrder($order);

        // Trả về JSON thông báo thành công
        return response()->json([
            'message' => 'Xóa đơn hàng thành công.',
        ], Response::HTTP_OK);
    }
}