<?php

namespace App\Http\Controllers\Web\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\ReplyContactRequest;
use App\Models\Contact;
use App\Services\ContactService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class ContactWebController extends Controller
{
    public function __construct(protected ContactService $contactService) {}

    public function index(Request $request): View
    {
        $filters = $request->only(['status', 'q']);
        $perPage = (int) $request->integer('per_page', 10);

        return view('admin.contacts.index', [
            'contacts' => $this->contactService->getAdminContacts($filters, $perPage),
            'filters' => $filters,
            'summary' => $this->contactService->getStatusSummary(),
        ]);
    }

    public function show(Contact $contact): View
    {
        return view('admin.contacts.show', [
            'contact' => $this->contactService->loadContact($contact),
        ]);
    }

    public function reply(ReplyContactRequest $request, Contact $contact): RedirectResponse
    {
        $updatedContact = $this->contactService->replyContact(
            $contact,
            (int) $request->user()?->id,
            (string) $request->validated('admin_response')
        );

        return redirect()
            ->route('admin.contacts.show', $updatedContact)
            ->with('success', 'Đã phản hồi liên hệ của khách hàng.');
    }
}
