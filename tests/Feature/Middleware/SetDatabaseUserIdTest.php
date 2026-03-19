<?php

namespace Tests\Feature\Middleware;

use App\Http\Middleware\SetDatabaseUserId;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;
use Tests\TestCase;

class SetDatabaseUserIdTest extends TestCase
{
    use DatabaseTransactions;

    public function test_middleware_sets_current_user_id_in_database_session(): void
    {
        $user = User::factory()->create([
            'role' => 'A',
            'email' => 'middleware-test-' . uniqid() . '@example.com',
        ]);

        // Register a temporary test route that uses the middleware
        Route::middleware(['web', SetDatabaseUserId::class])
            ->get('/__test-middleware-uid', function () {
                $result = DB::select('SELECT @current_user_id as uid');

                return response()->json(['uid' => $result[0]->uid]);
            });

        $response = $this->actingAs($user)->getJson('/__test-middleware-uid');

        $response->assertOk();
        $response->assertJson(['uid' => $user->id]);
    }
}
