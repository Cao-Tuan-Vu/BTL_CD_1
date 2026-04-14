<?php

namespace App\Repositories;

use App\Models\Review;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class ReviewRepository
{
    public function paginate(array $filters = [], int $perPage = 10): LengthAwarePaginator
    {
        $query = Review::query()
            ->select(['id', 'product_id', 'user_id', 'rating', 'comment', 'created_at', 'updated_at'])
            ->with(['user:id,name,email', 'product:id,name']);

        if (! empty($filters['product_id'])) {
            $query->where('product_id', $filters['product_id']);
        }

        if (! empty($filters['user_id'])) {
            $query->where('user_id', $filters['user_id']);
        }

        $perPage = (int) max(1, min(100, $perPage));

        return $query->latest('id')->paginate($perPage);
    }

    public function findByIdOrFail(int $id): Review
    {
        return Review::query()
            ->with(['user:id,name,email', 'product:id,name'])
            ->findOrFail($id);
    }

    public function create(array $attributes): Review
    {
        return Review::create($attributes);
    }

    public function update(Review $review, array $attributes): Review
    {
        $review->update($attributes);

        return $review->refresh()->load(['user:id,name,email', 'product:id,name']);
    }

    public function delete(Review $review): void
    {
        $review->delete();
    }

    public function loadRelations(Review $review): Review
    {
        return $review->load(['user:id,name,email', 'product:id,name']);
    }
}
