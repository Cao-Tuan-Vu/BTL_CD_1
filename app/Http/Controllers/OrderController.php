<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreOrderRequest;
use App\Http\Requests\UpdateOrderStatusRequest;
use App\Http\Resources\OrderResource;
use App\Models\Order;
use App\Services\OrderService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Symfony\Component\HttpFoundation\Response;

class OrderController extends Controller
{
    public function __construct(protected OrderService $orderService) {}

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): AnonymousResourceCollection
    {
        $this->authorize('viewAny', Order::class);

        $filters = $request->only(['user_id', 'status']);
        $perPage = (int) $request->integer('per_page', 10);
        $orders = $this->orderService->getPaginatedOrders($filters, $perPage);

        return OrderResource::collection($orders);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreOrderRequest $request)
    {
        $this->authorize('create', Order::class);

        $validated = $request->validated();
        $actingUser = $request->user();

        if (! $actingUser || $actingUser->role !== 'customer') {
            abort(Response::HTTP_FORBIDDEN, 'Chỉ khách hàng mới được tạo đơn hàng.');
        }

        $order = $this->orderService->createOrder(
            (int) $actingUser->id,
            $validated['items'],
            $validated['shipping_address'],
            $validated['shipping_phone'],
            $validated['payment_method'] ?? Order::PAYMENT_METHOD_CASH,
        );

        return (new OrderResource($order))
            ->response()
            ->setStatusCode(Response::HTTP_CREATED);
    }

    /**
     * Display the specified resource.
     */
    public function show(Order $order): OrderResource
    {
        $this->authorize('view', $order);

        $order = $this->orderService->loadOrder($order);

        return new OrderResource($order);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateOrderStatusRequest $request, Order $order): OrderResource
    {
        $this->authorize('update', $order);

        $updatedOrder = $this->orderService->updateOrderStatus($order, $request->validated()['status']);

        return new OrderResource($updatedOrder);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Order $order): JsonResponse
    {
        $this->authorize('delete', $order);

        $this->orderService->deleteOrder($order);

        return response()->json([
            'message' => 'Xóa đơn hàng thành công.',
        ], Response::HTTP_OK);
    }
}
