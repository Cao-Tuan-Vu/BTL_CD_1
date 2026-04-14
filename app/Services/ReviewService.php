<?php

namespace App\Services;

use App\Models\Review;
use App\Repositories\ReviewRepository;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Cache;

class ReviewService
{
    public function __construct(protected ReviewRepository $reviewRepository) {}

    public function getPaginatedReviews(array $filters = [], int $perPage = 10): LengthAwarePaginator
    {
        return $this->reviewRepository->paginate($filters, $perPage);
    }

    public function getReviewById(int $reviewId): Review
    {
        return $this->reviewRepository->findByIdOrFail($reviewId);
    }

    public function loadReview(Review $review): Review
    {
        return $this->reviewRepository->loadRelations($review);
    }

    public function createReview(array $attributes): Review
    {
        $review = $this->reviewRepository->create($attributes);
        $this->flushReviewRelatedCaches();

        return $this->reviewRepository->loadRelations($review);
    }

    public function updateReview(Review $review, array $attributes): Review
    {
        $updatedReview = $this->reviewRepository->update($review, $attributes);
        $this->flushReviewRelatedCaches();

        return $updatedReview;
    }

    public function deleteReview(Review $review): void
    {
        $this->reviewRepository->delete($review);
        $this->flushReviewRelatedCaches();
    }

    protected function flushReviewRelatedCaches(): void
    {
        Cache::forget('reviews.product-stats.top8');
        Cache::forget('customer.home.featured-products:8');
    }
}
