<?php

namespace Tests\Feature\Api;

use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\RateLimiter;
use Tests\TestCase;

class RateLimitingTest extends TestCase
{
    use DatabaseTransactions;

    protected function setUp(): void
    {
        parent::setUp();
        RateLimiter::clear(md5('form-submissions|127.0.0.1'));
    }

    private function validContactPayload(): array
    {
        return [
            'name' => 'Test',
            'email' => 'rate-' . uniqid() . '@test.ch',
            'message' => 'Hello',
        ];
    }

    private function validAdhesionPayload(): array
    {
        return [
            'nom' => 'Rate',
            'prenom' => 'Limit',
            'email' => 'rate-' . uniqid() . '@test.ch',
            'telephone' => '+41 79 000 00 00',
            'photo_ok' => true,
        ];
    }

    private function validInscriptionPayload(): array
    {
        return [
            'email' => 'rate-' . uniqid() . '@test.ch',
            'event_id' => 999999,
        ];
    }

    // ── Contact endpoint ──

    public function test_contact_allows_first_requests(): void
    {
        $response = $this->postJson('/api/contact', $this->validContactPayload());

        $this->assertNotEquals(429, $response->status());
    }

    public function test_contact_rate_limited_after_5_requests(): void
    {
        for ($i = 0; $i < 5; $i++) {
            $this->postJson('/api/contact', $this->validContactPayload());
        }

        $response = $this->postJson('/api/contact', $this->validContactPayload());
        $response->assertStatus(429);
    }

    // ── Adhesion endpoint ──

    public function test_adhesion_rate_limited_after_5_requests(): void
    {
        for ($i = 0; $i < 5; $i++) {
            $this->postJson('/api/adhesion', $this->validAdhesionPayload());
        }

        $response = $this->postJson('/api/adhesion', $this->validAdhesionPayload());
        $response->assertStatus(429);
    }

    // ── Inscription-event endpoint ──

    public function test_inscription_event_rate_limited_after_5_requests(): void
    {
        for ($i = 0; $i < 5; $i++) {
            $this->postJson('/api/inscription-event', $this->validInscriptionPayload());
        }

        $response = $this->postJson('/api/inscription-event', $this->validInscriptionPayload());
        $response->assertStatus(429);
    }

    // ── Rate limit response format ──

    public function test_rate_limit_returns_french_error(): void
    {
        for ($i = 0; $i < 5; $i++) {
            $this->postJson('/api/contact', $this->validContactPayload());
        }

        $response = $this->postJson('/api/contact', $this->validContactPayload());
        $response->assertStatus(429)
            ->assertJson(['ok' => false])
            ->assertJsonFragment(['error' => 'Trop de messages. Réessayez plus tard.']);
    }
}
