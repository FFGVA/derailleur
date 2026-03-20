<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Filament\Pages\Auth\PasswordReset\RequestPasswordReset;
use Filament\Pages\Auth\PasswordReset\ResetPassword;
use Filament\Notifications\Auth\ResetPassword as ResetPasswordNotification;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Password;
use Livewire\Livewire;
use Tests\TestCase;

class PasswordResetTest extends TestCase
{
    use DatabaseTransactions;

    private function makeUser(): User
    {
        return User::create([
            'name' => 'Test Reset',
            'email' => 'reset-' . uniqid() . '@test.ch',
            'password' => bcrypt('oldpassword'),
            'role' => 'A',
        ]);
    }

    public function test_password_reset_request_page_loads(): void
    {
        $response = $this->get('/admin/password-reset/request');
        $response->assertStatus(200);
    }

    public function test_password_reset_link_can_be_requested(): void
    {
        Notification::fake();
        $user = $this->makeUser();

        Livewire::test(RequestPasswordReset::class)
            ->fillForm(['email' => $user->email])
            ->call('request');

        Notification::assertSentTo($user, ResetPasswordNotification::class);
    }

    public function test_password_reset_link_not_sent_for_unknown_email(): void
    {
        Notification::fake();

        Livewire::test(RequestPasswordReset::class)
            ->fillForm(['email' => 'nonexistent@test.ch'])
            ->call('request');

        Notification::assertNothingSent();
    }

    public function test_password_reset_url_is_signed(): void
    {
        Notification::fake();
        $user = $this->makeUser();

        Livewire::test(RequestPasswordReset::class)
            ->fillForm(['email' => $user->email])
            ->call('request');

        Notification::assertSentTo($user, ResetPasswordNotification::class, function ($notification) use ($user) {
            $url = $notification->toMail($user)->actionUrl;
            $this->assertStringContainsString('signature=', $url);
            $this->assertStringContainsString('token=', $url);
            return true;
        });
    }

    public function test_password_can_be_reset_with_valid_token(): void
    {
        $user = $this->makeUser();
        $token = Password::createToken($user);

        Livewire::test(ResetPassword::class, ['token' => $token])
            ->fillForm([
                'email' => $user->email,
                'password' => 'newpassword123',
                'passwordConfirmation' => 'newpassword123',
            ])
            ->call('resetPassword');

        $this->assertTrue(Hash::check('newpassword123', $user->fresh()->password));
    }
}
