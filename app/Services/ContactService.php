<?php

namespace App\Services;

use App\Models\Contact;
use App\Repositories\ContactRepository;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Cache;

class ContactService
{
    protected const CONTACT_STATUS_SUMMARY_CACHE_KEY = 'contacts.status-summary';

    public function __construct(protected ContactRepository $contactRepository) {}

    public function getAdminContacts(array $filters = [], int $perPage = 10): LengthAwarePaginator
    {
        return $this->contactRepository->paginateForAdmin($filters, $perPage);
    }

    public function getCustomerContacts(int $userId, int $perPage = 10): LengthAwarePaginator
    {
        return $this->contactRepository->paginateForCustomer($userId, $perPage);
    }

    public function getContactById(int $contactId): Contact
    {
        return $this->contactRepository->findByIdOrFail($contactId);
    }

    public function loadContact(Contact $contact): Contact
    {
        return $this->contactRepository->loadRelations($contact);
    }

    public function createContact(array $attributes): Contact
    {
        $attributes['status'] = 'pending';
        $this->flushContactCaches();

        return $this->contactRepository->create($attributes)->load(['user', 'responder']);
    }

    public function replyContact(Contact $contact, int $adminId, string $response): Contact
    {
        $updatedContact = $this->contactRepository->update($contact, [
            'admin_response' => trim($response),
            'status' => 'replied',
            'responded_by' => $adminId,
            'responded_at' => Carbon::now(),
        ]);
        $this->flushContactCaches();

        return $updatedContact;
    }

    /**
     * @return array{pending:int,replied:int,total:int}
     */
    public function getStatusSummary(): array
    {
        /** @var array{pending:int,replied:int,total:int} $summary */
        $summary = Cache::remember(self::CONTACT_STATUS_SUMMARY_CACHE_KEY, now()->addMinutes(2), function (): array {
            return $this->contactRepository->getStatusSummary();
        });

        return $summary;
    }

    protected function flushContactCaches(): void
    {
        Cache::forget(self::CONTACT_STATUS_SUMMARY_CACHE_KEY);
    }
}
