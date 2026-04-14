<?php

use App\Http\Controllers\CategoryController;
use App\Http\Controllers\ChatbotController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ReviewController;
use App\Models\Category;
use App\Models\Order;
use App\Models\Product;
use App\Models\Review;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return response()->json([
        'message' => 'API BTL_CD1 đang hoạt động.',
        'resources' => [
            'products' => '/api/products',
            'categories' => '/api/categories',
            'orders' => '/api/orders',
            'reviews' => '/api/reviews',
            'chatbot_message' => '/api/chatbot/message',
        ],
    ]);
});

Route::post('chatbot/message', [ChatbotController::class, 'message'])
    ->middleware('throttle:30,1')
    ->name('api.chatbot.message');

Route::apiResources([
    'products' => ProductController::class,
    'categories' => CategoryController::class,
    'reviews' => ReviewController::class,
]);

Route::apiResource('orders', OrderController::class)->except(['update']);

Route::middleware('api.admin')
    ->patch('orders/{order}/status', [OrderController::class, 'update'])
    ->name('orders.update-status');

Route::middleware('api.admin')->prefix('admin')->group(function (): void {
    Route::get('overview', function () {
        return response()->json([
            'message' => 'Tổng quan API quản trị',
            'stats' => [
                'products' => Product::query()->count(),
                'categories' => Category::query()->count(),
                'orders' => Order::query()->count(),
                'reviews' => Review::query()->count(),
                'revenue' => (float) Order::query()->sum('total_price'),
            ],
        ]);
    })->name('api.admin.overview');
});
