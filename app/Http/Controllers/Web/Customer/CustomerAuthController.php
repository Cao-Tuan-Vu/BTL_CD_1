<?php

namespace App\Http\Controllers\Web\Customer;

use App\Http\Controllers\Controller;
use App\Http\Requests\CustomerLoginRequest;
use App\Http\Requests\CustomerRegisterRequest;
use App\Http\Requests\ResetCustomerPasswordRequest;
use App\Http\Requests\SendCustomerPasswordResetLinkRequest;
use App\Models\User;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class CustomerAuthController extends Controller
{
    public function showLoginForm(): View|RedirectResponse
    {
        if (Auth::check() && Auth::user()?->role === 'customer') {
            return redirect()->route('customer.home');
        }

        return view('customer.auth.login');
    }

    public function login(CustomerLoginRequest $request): RedirectResponse
    {
        $validated = $request->validated();
        $remember = (bool) ($validated['remember'] ?? false);

        if (! Auth::attempt([
            'email' => $validated['email'],
            'password' => $validated['password'],
            'role' => 'customer',
        ], $remember)) {
            return back()
                ->withInput($request->only('email', 'remember'))
                ->withErrors(['email' => 'Email hoặc mật khẩu không chính xác.']);
        }

        $request->session()->regenerate();

        return redirect()->route('customer.home');
    }

    public function showRegisterForm(): View|RedirectResponse
    {
        if (Auth::check() && Auth::user()?->role === 'customer') {
            return redirect()->route('customer.home');
        }

        return view('customer.auth.register');
    }

    public function register(CustomerRegisterRequest $request): RedirectResponse
    {
        // tài khoản A . đã được đăng ký thì ko được đky nữa
        if (Auth::check() && Auth::user()?->role === 'customer') {
            return redirect()->route('customer.home')
                ->withErrors(['auth' => 'Trang đăng ký chỉ dành cho khách mới.']);
        }

        $validated = $request->validated();

        $customer = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => $validated['password'],
            'role' => 'customer',
        ]);

        Auth::login($customer);
        $request->session()->regenerate();

        return redirect()
            ->route('customer.home')
            ->with('success', 'Đăng ký tài khoản thành công.');
    }

    public function showForgotPasswordForm(): View|RedirectResponse
    {
        if (Auth::check() && Auth::user()?->role === 'customer') {
            return redirect()->route('customer.home');
        }

        return view('customer.auth.forgot-password');
    }

    public function sendPasswordResetLink(SendCustomerPasswordResetLinkRequest $request): RedirectResponse
    {
        $email = (string) $request->validated()['email'];

        $customer = User::query()
            ->where('email', $email)
            ->where('role', 'customer')
            ->first();

        if (! $customer) {
            return back()
                ->withInput($request->only('email'))
                ->withErrors(['email' => 'Email này không thuộc tài khoản khách hàng.']);
        }

        $otp = (string) random_int(100000, 999999);

        DB::table('password_reset_tokens')->updateOrInsert(
            ['email' => $email],
            [
                'token' => Hash::make($otp),
                'created_at' => now(),
            ]
        );

        return redirect()
            ->route('customer.password.reset', ['email' => $email])
            ->with('success', 'OTP đã được tạo. Vui lòng nhập OTP để đặt lại mật khẩu.')
            ->with('password_reset_otp', $otp);
    }

    public function showResetPasswordForm(Request $request, ?string $token = null): View|RedirectResponse
    {
        $email = (string) $request->query('email', '');
        $otp = is_string($token) && preg_match('/^\d{6}$/', $token) ? $token : '';

        return view('customer.auth.reset-password', [
            'email' => $email,
            'otp' => $otp,
        ]);
    }

    public function resetPassword(ResetCustomerPasswordRequest $request): RedirectResponse
    {
        $validated = $request->validated();

        $customer = User::query()
            ->where('email', $validated['email'])
            ->where('role', 'customer')
            ->first();

        if (! $customer) {
            return back()
                ->withInput($request->except(['password', 'password_confirmation']))
                ->withErrors(['email' => 'Email này không thuộc tài khoản khách hàng.']);
        }

        $tokenRow = DB::table('password_reset_tokens')
            ->where('email', $validated['email'])
            ->first();

        if (! $tokenRow) {
            return back()
                ->withInput($request->except(['password', 'password_confirmation']))
                ->withErrors(['otp' => 'OTP không hợp lệ hoặc đã hết hạn.']);
        }

        $createdAt = $tokenRow->created_at ? Carbon::parse((string) $tokenRow->created_at) : null;
        $isExpired = ! $createdAt || $createdAt->lt(now()->subMinutes(10));

        if ($isExpired) {
            DB::table('password_reset_tokens')->where('email', $validated['email'])->delete();

            return back()
                ->withInput($request->except(['password', 'password_confirmation']))
                ->withErrors(['otp' => 'OTP không hợp lệ hoặc đã hết hạn.']);
        }

        if (! Hash::check((string) $validated['otp'], (string) $tokenRow->token)) {
            return back()
                ->withInput($request->except(['password', 'password_confirmation']))
                ->withErrors(['otp' => 'OTP không hợp lệ hoặc đã hết hạn.']);
        }

        $customer->forceFill([
            'password' => $validated['password'],
            'remember_token' => Str::random(60),
        ])->save();

        DB::table('password_reset_tokens')->where('email', $validated['email'])->delete();

        event(new PasswordReset($customer));

        return redirect()
            ->route('customer.login')
            ->with('success', 'Đặt lại mật khẩu thành công. Vui lòng đăng nhập lại.');
    }

    public function logout(): RedirectResponse
    {
        Auth::logout();

        request()->session()->invalidate();
        request()->session()->regenerateToken();

        return redirect()
            ->route('customer.home')
            ->with('success', 'Đăng xuất thành công.');
    }
}
