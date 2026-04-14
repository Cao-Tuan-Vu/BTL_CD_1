<?php

namespace App\Http\Controllers\Web\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\UpdateOrderStatusRequest;
use App\Models\Order;
use App\Models\User;
use App\Services\OrderService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;

class OrderWebController extends Controller
{
    public function __construct(protected OrderService $orderService) {}

    public function index(Request $request): View
    {
        $filters = $request->only(['user_id', 'status']);
        $perPage = (int) $request->integer('per_page', 10);

        $orders = $this->orderService->getPaginatedOrders($filters, $perPage);
        $users = $this->cachedCustomerOptions();

        return view('admin.orders.index', [
            'orders' => $orders,
            'users' => $users,
            'filters' => $filters,
            'statuses' => $this->statuses(),
        ]);
    }

    public function show(Order $order): View
    {
        $order = $this->orderService->loadOrder($order);

        return view('admin.orders.show', [
            'order' => $order,
        ]);
    }

    public function edit(Order $order): View
    {
        $order = $this->orderService->loadOrder($order);

        return view('admin.orders.edit', [
            'order' => $order,
            'statuses' => $this->statuses(),
        ]);
    }

    public function update(UpdateOrderStatusRequest $request, Order $order): RedirectResponse
    {
        $this->authorize('update', $order);

        $updatedOrder = $this->orderService->updateOrderStatus($order, $request->validated()['status']);

        return redirect()
            ->route('admin.orders.show', $updatedOrder)
            ->with('success', 'Cập nhật trạng thái đơn hàng thành công.');
    }

    public function updateStatus(UpdateOrderStatusRequest $request, Order $order): RedirectResponse
    {
        return $this->update($request, $order);
    }

    public function destroy(Order $order): RedirectResponse
    {
        $this->orderService->deleteOrder($order);

        return redirect()
            ->route('admin.orders.index')
            ->with('success', 'Xóa đơn hàng thành công.');
    }

    /**
     * @return array<int, string>
     */
    protected function statuses(): array
    {
        return Order::statuses();
    }

    /**
     * @return Collection<int, User>
     */
    protected function cachedCustomerOptions(): Collection
    {
        /** @var Collection<int, User> $users */
        $users = Cache::remember('admin.orders.customer-options', now()->addMinutes(5), function (): Collection {
            return User::query()
                ->where('role', 'customer')
                ->orderBy('name')
                ->get(['id', 'name']);
        });

        return $users;
    }
}
