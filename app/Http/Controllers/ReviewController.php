<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreReviewRequest;
use App\Http\Requests\UpdateReviewRequest;
use App\Http\Resources\ReviewResource;
use App\Models\Review;
use App\Services\ReviewService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Symfony\Component\HttpFoundation\Response;

class ReviewController extends Controller
{
    public function __construct(protected ReviewService $reviewService) {}

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): AnonymousResourceCollection
    {
        $this->authorize('viewAny', Review::class);

        $filters = $request->only(['product_id', 'user_id']);
        $perPage = (int) $request->integer('per_page', 10);
        $reviews = $this->reviewService->getPaginatedReviews($filters, $perPage);

        return ReviewResource::collection($reviews);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreReviewRequest $request)
    {
        $this->authorize('create', Review::class);

        $validated = $request->validated();
        $validated['user_id'] = (int) $request->user()->id;

        $review = $this->reviewService->createReview($validated);

        return (new ReviewResource($review))
            ->response()
            ->setStatusCode(Response::HTTP_CREATED);
    }

    /**
     * Display the specified resource.
     */
    public function show(Review $review): ReviewResource
    {
        $this->authorize('view', $review);

        $review = $this->reviewService->loadReview($review);

        return new ReviewResource($review);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateReviewRequest $request, Review $review): ReviewResource
    {
        $this->authorize('update', $review);

        $updatedReview = $this->reviewService->updateReview($review, $request->validated());

        return new ReviewResource($updatedReview);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Review $review): JsonResponse
    {
        $this->authorize('delete', $review);

        $this->reviewService->deleteReview($review);

        return response()->json([
            'message' => 'Xóa đánh giá thành công.',
        ], Response::HTTP_OK);
    }
}
