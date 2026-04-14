<?php

namespace App\Http\Controllers\Web\Customer;

use App\Http\Controllers\Controller;
use App\Http\Requests\UpdateCustomerPasswordRequest;
use App\Http\Requests\UpdateCustomerProfileRequest;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;

class CustomerProfileController extends Controller
{
    public function show(): View
    {
        return view('customer.auth.profile', [
            'customer' => request()->user(),
        ]);
    }

    public function updateProfile(UpdateCustomerProfileRequest $request): RedirectResponse
    {
        $request->user()->update($request->validated());

        return back()->with('success', 'Cập nhật hồ sơ thành công.');
    }

    public function updatePassword(UpdateCustomerPasswordRequest $request): RedirectResponse
    {
        $request->user()->update([
            'password' => $request->validated()['password'],
        ]);

        return back()->with('success', 'Doi mật khẩu thành công.');
    }
}
