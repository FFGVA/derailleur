<?php

namespace Tests\Feature;

use App\Models\Member;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\URL;
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
    }

    public function test_valider_valid_signature_shows_green(): void
    {
        $member = $this->makeMember();
        $url = URL::temporarySignedRoute('carte.valider', now()->addMinutes(10), ['member' => $member->id]);

        $response = $this->get($url);

        $response->assertOk();
        $response->assertSee($member->first_name);
        $response->assertSee('Membre active');
    }

    public function test_valider_expired_signature_shows_expired(): void
    {
        $member = $this->makeMember();
        $url = URL::temporarySignedRoute('carte.valider', now()->subMinute(), ['member' => $member->id]);

        $response = $this->get($url);

        $response->assertOk();
        $response->assertSee('expir');
    }

    public function test_valider_inactive_member_shows_red(): void
    {
        $member = $this->makeMember(['statuscode' => 'I']);
        $url = URL::temporarySignedRoute('carte.valider', now()->addMinutes(10), ['member' => $member->id]);

        $response = $this->get($url);

        $response->assertOk();
        $response->assertSee('inactive');
    }

    public function test_valider_expired_membership_shows_red(): void
    {
        $member = $this->makeMember(['membership_end' => now()->subDay()]);
        $url = URL::temporarySignedRoute('carte.valider', now()->addMinutes(10), ['member' => $member->id]);

        $response = $this->get($url);

        $response->assertOk();
        $response->assertSee('inactive');
    }

    public function test_valider_invalid_signature(): void
    {
        $response = $this->get('/carte/valider?member=1&signature=invalid&expires=' . now()->addHour()->getTimestamp());

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
}
