<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Auth;
use Tests\TestCase;

class LoginTest extends TestCase
{
    use DatabaseTransactions;

    public function test_login_page_loads(): void
    {
        $response = $this->get('/admin/login');

        $response->assertOk();
    }

    public function test_can_authenticate_with_valid_credentials(): void
    {
        $user = User::factory()->create([
            'role' => 'A',
            'email' => 'login-test-' . uniqid() . '@example.com',
            'password' => bcrypt('secret-password'),
        ]);

        $authenticated = Auth::attempt([
            'email' => $user->email,
            'password' => 'secret-password',
        ]);

        $this->assertTrue($authenticated);
        $this->assertEquals($user->id, Auth::id());
    }

    public function test_cannot_authenticate_with_wrong_password(): void
    {
        $user = User::factory()->create([
            'role' => 'A',
            'email' => 'login-fail-' . uniqid() . '@example.com',
            'password' => bcrypt('correct-password'),
        ]);

        $authenticated = Auth::attempt([
            'email' => $user->email,
            'password' => 'wrong-password',
        ]);

        $this->assertFalse($authenticated);
        $this->assertNull(Auth::id());
    }
}
