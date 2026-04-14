<?php

namespace App\Repositories;

use App\Models\Contact;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class ContactRepository
{
    public function paginateForAdmin(array $filters = [], int $perPage = 10): LengthAwarePaginator
    {
        $query = Contact::query()->with(['user', 'responder']);

        if (! empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (! empty($filters['q'])) {
            $keyword = trim((string) $filters['q']);
            $query->where(function ($builder) use ($keyword): void {
                $builder
                    ->where('name', 'like', "%{$keyword}%")
                    ->orWhere('email', 'like', "%{$keyword}%")
                    ->orWhere('phone', 'like', "%{$keyword}%")
                    ->orWhere('message', 'like', "%{$keyword}%");
            });
        }

        $perPage = (int) max(1, min(100, $perPage));

        return $query->latest('id')->paginate($perPage);
    }

    public function paginateForCustomer(int $userId, int $perPage = 10): LengthAwarePaginator
    {
        $perPage = (int) max(1, min(100, $perPage));

        return Contact::query()
            ->with('responder')
            ->where('user_id', $userId)
            ->latest('id')
            ->paginate($perPage);
    }

    public function findByIdOrFail(int $contactId): Contact
    {
        return Contact::query()->with(['user', 'responder'])->findOrFail($contactId);
    }

    public function loadRelations(Contact $contact): Contact
    {
        return $contact->load(['user', 'responder']);
    }

    public function create(array $attributes): Contact
    {
        return Contact::create($attributes);
    }

    public function update(Contact $contact, array $attributes): Contact
    {
        $contact->update($attributes);

        return $contact->refresh()->load(['user', 'responder']);
    }

    /**
     * @return array{pending:int,replied:int,total:int}
     */
    public function getStatusSummary(): array
    {
        $statusCounts = Contact::query()
            ->selectRaw('status, COUNT(*) as aggregate_count')
            ->groupBy('status')
            ->pluck('aggregate_count', 'status');

        return [
            'pending' => (int) ($statusCounts['pending'] ?? 0),
            'replied' => (int) ($statusCounts['replied'] ?? 0),
            'total' => (int) $statusCounts->sum(),
        ];
    }
}
