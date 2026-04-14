<?php

namespace App\Http\Controllers\Web\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\UpdateReviewRequest;
use App\Models\Product;
use App\Models\Review;
use App\Models\User;
use App\Services\ReviewService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class ReviewWebController extends Controller
{
    public function __construct(protected ReviewService $reviewService) {}

    public function index(Request $request): View
    {
        $filters = $request->only(['product_id', 'user_id']);
        $perPage = (int) $request->integer('per_page', 10);
        $reviews = $this->reviewService->getPaginatedReviews($filters, $perPage);
        $productReviewStats = Cache::remember('reviews.product-stats.top8', now()->addMinutes(5), function () {
            return Product::query()
                ->withCount('reviews')
                ->withAvg('reviews', 'rating')
                ->orderByDesc('reviews_count')
                ->limit(8)
                ->get(['id', 'name']);
        });

        $products = Cache::remember('reviews.filter.products', now()->addMinutes(5), function () {
            return Product::query()->orderBy('name')->get(['id', 'name']);
        });

        $users = Cache::remember('reviews.filter.customer-users', now()->addMinutes(5), function () {
            return User::query()
                ->where('role', 'customer')
                ->orderBy('name')
                ->get(['id', 'name']);
        });

        return view('admin.reviews.index', [
            'reviews' => $reviews,
            'filters' => $filters,
            'products' => $products,
            'users' => $users,
            'productReviewStats' => $productReviewStats,
        ]);
    }

    public function show(Review $review): View
    {
        $review = $this->reviewService->loadReview($review);

        return view('admin.reviews.show', [
            'review' => $review,
        ]);
    }

    public function edit(Review $review): View
    {
        return view('admin.reviews.edit', [
            'review' => $review->load(['product', 'user']),
            'products' => Cache::remember('reviews.filter.products', now()->addMinutes(5), function () {
                return Product::query()->orderBy('name')->get(['id', 'name']);
            }),
            'users' => Cache::remember('reviews.filter.customer-users', now()->addMinutes(5), function () {
                return User::query()
                    ->where('role', 'customer')
                    ->orderBy('name')
                    ->get(['id', 'name']);
            }),
        ]);
    }

    public function update(UpdateReviewRequest $request, Review $review): RedirectResponse
    {
        $updatedReview = $this->reviewService->updateReview($review, $request->validated());

        return redirect()
            ->route('admin.reviews.show', $updatedReview)
            ->with('success', 'Cập nhật đánh giá thành công.');
    }

    public function destroy(Review $review): RedirectResponse
    {
        $this->reviewService->deleteReview($review);

        return redirect()
            ->route('admin.reviews.index')
            ->with('success', 'Xóa đánh giá thành công.');
    }
}
