<?php

namespace Tests\Unit\Models;

use App\Models\Event;
use App\Models\Member;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class EventPricingTest extends TestCase
{
    use DatabaseTransactions;

    private function makeEvent(array $overrides = []): Event
    {
        return Event::create(array_merge([
            'title' => 'Test Event',
            'starts_at' => now()->addWeek(),
            'price' => '10.00',
            'price_non_member' => '25.00',
        ], $overrides));
    }

    private function makeMember(string $statuscode): Member
    {
        return Member::create([
            'first_name' => 'Test',
            'last_name' => 'Member',
            'email' => 'test-' . uniqid() . '@example.com',
            'statuscode' => $statuscode,
        ]);
    }

    public function test_active_member_pays_member_price(): void
    {
        $event = $this->makeEvent();
        $member = $this->makeMember('A');

        $this->assertSame('10.00', $event->priceForMember($member));
    }

    public function test_non_member_pays_non_member_price(): void
    {
        $event = $this->makeEvent();
        $member = $this->makeMember('N');

        $this->assertSame('25.00', $event->priceForMember($member));
    }

    public function test_pending_member_pays_non_member_price(): void
    {
        $event = $this->makeEvent();
        $member = $this->makeMember('P');

        $this->assertSame('25.00', $event->priceForMember($member));
    }

    public function test_enfant_pays_member_price(): void
    {
        $event = $this->makeEvent();
        $member = $this->makeMember('E');

        $this->assertSame('10.00', $event->priceForMember($member));
    }

    public function test_non_member_falls_back_to_member_price_when_no_non_member_price(): void
    {
        $event = $this->makeEvent(['price_non_member' => null]);
        $member = $this->makeMember('N');

        $this->assertSame('10.00', $event->priceForMember($member));
    }
}
