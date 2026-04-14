<?php

namespace App\Http\Controllers\Web\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Order;
use App\Models\Product;
use App\Models\Review;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Cache;

class HomeController extends Controller
{
    public function index(): View
    {
        $stats = Cache::remember('admin.dashboard.stats', now()->addMinutes(1), function (): array {
            return [
                'products' => Product::query()->count(),
                'categories' => Category::query()->count(),
                'orders' => Order::query()->count(),
                'reviews' => Review::query()->count(),
                'revenue' => (float) Order::query()->sum('total_price'),
            ];
        });

        $latestOrders = Cache::remember('admin.dashboard.latest-orders', now()->addMinutes(1), function () {
            return Order::query()
                ->select(['id', 'user_id', 'status', 'total_price', 'created_at'])
                ->with('user:id,name')
                ->latest('id')
                ->limit(5)
                ->get();
        });

        return view('admin.home.index', [
            'stats' => $stats,
            'latestOrders' => $latestOrders,
        ]);
    }
}
