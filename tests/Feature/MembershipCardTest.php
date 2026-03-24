<?php

namespace Tests\Feature;

use App\Models\Member;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Cache;
use Tests\TestCase;

class MembershipCardTest extends TestCase
{
    use DatabaseTransactions;

    private function makeMember(array $overrides = []): Member
    {
        return Member::create(array_merge([
            'first_name' => 'Marie',
            'last_name' => 'Test',
            'email' => 'carte-' . uniqid() . '@test.ch',
            'statuscode' => 'A',
            'membership_end' => now()->addYear(),
        ], $overrides));
    }

    private function authenticatedGet(Member $member, string $url)
    {
        return $this->withSession([
            'portal_member_id' => $member->id,
            'portal_last_activity' => time(),
        ])->get($url);
    }

    private function createCarteToken(Member $member): string
    {
        $token = bin2hex(random_bytes(8));
        Cache::put("carte_token:{$token}", $member->id, now()->addMinutes(5));
        return $token;
    }

    public function test_carte_page_requires_auth(): void
    {
        $response = $this->get('/portail/carte');
        $response->assertRedirect();
    }

    public function test_carte_page_shows_member_name(): void
    {
        $member = $this->makeMember();
        $response = $this->authenticatedGet($member, '/portail/carte');

        $response->assertOk();
        $response->assertSee($member->first_name);
        $response->assertSee($member->last_name);
    }

    public function test_carte_page_shows_active_status(): void
    {
        $member = $this->makeMember();
        $response = $this->authenticatedGet($member, '/portail/carte');

        $response->assertSee('Membre active');
    }

    public function test_carte_page_shows_inactive_for_expired(): void
    {
        $member = $this->makeMember(['membership_end' => now()->subDay()]);
        $response = $this->authenticatedGet($member, '/portail/carte');

        $response->assertSee('inactive');
    }

    public function test_carte_qr_url_returns_json(): void
    {
        $member = $this->makeMember();
        $response = $this->authenticatedGet($member, '/portail/carte/qr-url');

        $response->assertOk();
        $response->assertJsonStructure(['url']);

        $url = $response->json('url');
        $this->assertMatchesRegularExpression('#/carte/v/[a-f0-9]{16}$#', $url);
    }

    public function test_carte_qr_url_creates_cache_token(): void
    {
        $member = $this->makeMember();
        $response = $this->authenticatedGet($member, '/portail/carte/qr-url');

        $url = $response->json('url');
        $token = basename($url);

        $this->assertEquals($member->id, Cache::get("carte_token:{$token}"));
    }

    public function test_valider_valid_token_shows_green(): void
    {
        $member = $this->makeMember();
        $token = $this->createCarteToken($member);

        $response = $this->get("/carte/v/{$token}");

        $response->assertOk();
        $response->assertSee($member->first_name);
        $response->assertSee('Membre active');
    }

    public function test_valider_expired_token_shows_expired(): void
    {
        $member = $this->makeMember();
        $token = bin2hex(random_bytes(8));
        // Don't put in cache — simulates expired

        $response = $this->get("/carte/v/{$token}");

        $response->assertOk();
        $response->assertSee('expir');
    }

    public function test_valider_inactive_member_shows_red(): void
    {
        $member = $this->makeMember(['statuscode' => 'I']);
        $token = $this->createCarteToken($member);

        $response = $this->get("/carte/v/{$token}");

        $response->assertOk();
        $response->assertSee('inactive');
    }

    public function test_valider_expired_membership_shows_red(): void
    {
        $member = $this->makeMember(['membership_end' => now()->subDay()]);
        $token = $this->createCarteToken($member);

        $response = $this->get("/carte/v/{$token}");

        $response->assertOk();
        $response->assertSee('inactive');
    }

    public function test_valider_invalid_token_format_returns_404(): void
    {
        $response = $this->get('/carte/v/tooshort');
        $response->assertNotFound();
    }

    public function test_valider_unknown_token_shows_expired(): void
    {
        $response = $this->get('/carte/v/aaaaaaaaaaaaaaaa');

        $response->assertOk();
        $response->assertSee('expir');
    }

    public function test_dashboard_shows_qr_icon(): void
    {
        $member = $this->makeMember();
        $response = $this->authenticatedGet($member, '/portail');

        $response->assertOk();
        $response->assertSee(route('portail.carte'));
    }

    public function test_carte_url_is_clean_without_query_params(): void
    {
        $member = $this->makeMember();
        $response = $this->authenticatedGet($member, '/portail/carte/qr-url');

        $url = $response->json('url');
        $this->assertStringNotContainsString('?', $url);
        $this->assertStringNotContainsString('signature', $url);
    }
}
