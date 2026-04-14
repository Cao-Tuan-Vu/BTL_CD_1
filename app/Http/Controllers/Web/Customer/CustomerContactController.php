<?php

namespace App\Http\Controllers\Web\Customer;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreContactRequest;
use App\Services\ContactService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;

class CustomerContactController extends Controller
{
    public function __construct(protected ContactService $contactService) {}

    public function show(): View
    {
        $customer = request()->user();
        $contactHistory = null;

        if ($customer && $customer->role === 'customer') {
            $contactHistory = $this->contactService->getCustomerContacts($customer->id, 5);
        }

        return view('customer.contact', [
            'customer' => $customer,
            'contactHistory' => $contactHistory,
        ]);
    }

    public function store(StoreContactRequest $request): RedirectResponse
    {
        $customer = $request->user();
        $validated = $request->validated();

        $this->contactService->createContact([
            'user_id' => $customer?->id,
            'name' => $validated['name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'],
            'message' => $validated['message'],
        ]);

        return redirect()
            ->route('customer.contact')
            ->with('success', 'Đã gửi liên hệ thành công. Quản trị viên sẽ phản hồi sớm nhất.');
    }
}
