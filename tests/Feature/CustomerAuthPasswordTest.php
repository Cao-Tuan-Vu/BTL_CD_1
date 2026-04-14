<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class CustomerAuthPasswordTest extends TestCase
{
    use RefreshDatabase;

    public function test_customer_registration_requires_password_confirmation_and_strong_rules(): void
    {
        $response = $this
            ->from('/customer/register')
            ->post('/customer/register', [
                'name' => 'Khach Hang A',
                'email' => 'customer-a@example.com',
                'password' => 'abc123!',
                'password_confirmation' => 'abc123!',
            ]);

        $response->assertRedirect('/customer/register');
        $response->assertSessionHasErrors(['password']);

        $this->assertDatabaseMissing('users', [
            'email' => 'customer-a@example.com',
        ]);
    }

    public function test_customer_can_register_with_valid_password_policy(): void
    {
        $response = $this->post('/customer/register', [
            'name' => 'Khach Hang B',
            'email' => 'customer-b@example.com',
            'password' => 'Abcde1!',
            'password_confirmation' => 'Abcde1!',
        ]);

        $response->assertRedirect('/customer');
        $this->assertAuthenticated();

        $user = User::query()->where('email', 'customer-b@example.com')->first();

        $this->assertNotNull($user);
        $this->assertSame('customer', $user->role);
        $this->assertTrue(Hash::check('Abcde1!', (string) $user->password));
    }

    public function test_forgot_password_generates_otp_only_for_customer_accounts(): void
    {
        $customer = User::factory()->create([
            'role' => 'customer',
            'email' => 'customer-forgot@example.com',
        ]);

        $admin = User::factory()->create([
            'role' => 'admin',
            'email' => 'admin-forgot@example.com',
        ]);

        $customerResponse = $this
            ->from('/customer/forgot-password')
            ->post('/customer/forgot-password', [
                'email' => $customer->email,
            ]);

        $customerResponse->assertRedirect(route('customer.password.reset', ['email' => $customer->email]));
        $customerResponse->assertSessionHasNoErrors();
        $customerResponse->assertSessionHas('success');
        $customerResponse->assertSessionHas('password_reset_otp');

        $otp = (string) session('password_reset_otp');
        $this->assertMatchesRegularExpression('/^\d{6}$/', $otp);

        $tokenRow = DB::table('password_reset_tokens')
            ->where('email', $customer->email)
            ->first();

        $this->assertNotNull($tokenRow);
        $this->assertTrue(Hash::check($otp, (string) $tokenRow->token));

        $adminResponse = $this
            ->from('/customer/forgot-password')
            ->post('/customer/forgot-password', [
                'email' => $admin->email,
            ]);

        $adminResponse->assertRedirect('/customer/forgot-password');
        $adminResponse->assertSessionHasErrors(['email']);

        $this->assertDatabaseMissing('password_reset_tokens', [
            'email' => $admin->email,
        ]);
    }

    public function test_customer_can_reset_password_with_valid_otp_and_policy(): void
    {
        $customer = User::factory()->create([
            'role' => 'customer',
            'email' => 'customer-reset@example.com',
            'password' => 'OldPass1!',
        ]);

        DB::table('password_reset_tokens')->updateOrInsert(
            ['email' => $customer->email],
            [
                'token' => Hash::make('123456'),
                'created_at' => now(),
            ]
        );

        $response = $this
            ->from('/customer/reset-password?email='.$customer->email)
            ->post('/customer/reset-password', [
                'email' => $customer->email,
                'otp' => '123456',
                'password' => 'NewPass1!',
                'password_confirmation' => 'NewPass1!',
            ]);

        $response->assertRedirect('/customer/login');
        $response->assertSessionHas('success');

        $customer->refresh();
        $this->assertTrue(Hash::check('NewPass1!', (string) $customer->password));

        $this->assertDatabaseMissing('password_reset_tokens', [
            'email' => $customer->email,
        ]);

        $loginResponse = $this->post('/customer/login', [
            'email' => $customer->email,
            'password' => 'NewPass1!',
        ]);

        $loginResponse->assertRedirect('/customer');
    }

    public function test_customer_reset_password_rejects_weak_password(): void
    {
        $customer = User::factory()->create([
            'role' => 'customer',
            'email' => 'customer-reset-weak@example.com',
            'password' => 'OldPass1!',
        ]);

        DB::table('password_reset_tokens')->updateOrInsert(
            ['email' => $customer->email],
            [
                'token' => Hash::make('654321'),
                'created_at' => now(),
            ]
        );

        $response = $this
            ->from('/customer/reset-password?email='.$customer->email)
            ->post('/customer/reset-password', [
                'email' => $customer->email,
                'otp' => '654321',
                'password' => 'weak1!',
                'password_confirmation' => 'weak1!',
            ]);

        $response->assertRedirect();
        $response->assertSessionHasErrors(['password']);

        $customer->refresh();
        $this->assertTrue(Hash::check('OldPass1!', (string) $customer->password));
    }

    public function test_customer_reset_password_rejects_invalid_otp(): void
    {
        $customer = User::factory()->create([
            'role' => 'customer',
            'email' => 'customer-reset-invalid-otp@example.com',
            'password' => 'OldPass1!',
        ]);

        DB::table('password_reset_tokens')->updateOrInsert(
            ['email' => $customer->email],
            [
                'token' => Hash::make('111111'),
                'created_at' => now(),
            ]
        );

        $response = $this
            ->from('/customer/reset-password?email='.$customer->email)
            ->post('/customer/reset-password', [
                'email' => $customer->email,
                'otp' => '222222',
                'password' => 'NewPass1!',
                'password_confirmation' => 'NewPass1!',
            ]);

        $response->assertRedirect();
        $response->assertSessionHasErrors(['otp']);

        $customer->refresh();
        $this->assertTrue(Hash::check('OldPass1!', (string) $customer->password));
    }

    public function test_admin_email_cannot_reset_password_in_customer_flow(): void
    {
        $admin = User::factory()->create([
            'role' => 'admin',
            'email' => 'admin-reset@example.com',
            'password' => 'AdminOld1!',
        ]);

        DB::table('password_reset_tokens')->updateOrInsert(
            ['email' => $admin->email],
            [
                'token' => Hash::make('333333'),
                'created_at' => now(),
            ]
        );

        $response = $this
            ->from('/customer/reset-password?email='.$admin->email)
            ->post('/customer/reset-password', [
                'email' => $admin->email,
                'otp' => '333333',
                'password' => 'AdminNew1!',
                'password_confirmation' => 'AdminNew1!',
            ]);

        $response->assertRedirect();
        $response->assertSessionHasErrors(['email']);

        $admin->refresh();
        $this->assertTrue(Hash::check('AdminOld1!', (string) $admin->password));
    }
}
