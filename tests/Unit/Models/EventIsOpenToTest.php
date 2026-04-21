<?php

namespace Tests\Unit\Models;

use App\Models\Event;
use App\Models\Member;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class EventIsOpenToTest extends TestCase
{
    use DatabaseTransactions;

    private function makeEvent(bool $membersOnly): Event
    {
        return Event::create([
            'title' => 'Test Event',
            'starts_at' => now()->addWeek(),
            'price' => '0',
            'members_only' => $membersOnly,
        ]);
    }

    private function makeMember(string $statuscode): Member
    {
        return Member::create([
            'first_name' => 'Test',
            'last_name' => 'Member',
            'email' => 'openTo-' . uniqid() . '@example.com',
            'statuscode' => $statuscode,
        ]);
    }

    public function test_non_members_only_event_is_open_to_all(): void
    {
        $event = $this->makeEvent(false);

        $this->assertTrue($event->isOpenTo($this->makeMember('A')));
        $this->assertTrue($event->isOpenTo($this->makeMember('E')));
        $this->assertTrue($event->isOpenTo($this->makeMember('N')));
        $this->assertTrue($event->isOpenTo($this->makeMember('P')));
    }

    public function test_members_only_event_is_open_to_actif(): void
    {
        $event = $this->makeEvent(true);
        $this->assertTrue($event->isOpenTo($this->makeMember('A')));
    }

    public function test_members_only_event_is_open_to_enfant(): void
    {
        $event = $this->makeEvent(true);
        $this->assertTrue($event->isOpenTo($this->makeMember('E')));
    }

    public function test_members_only_event_is_closed_to_non_member(): void
    {
        $event = $this->makeEvent(true);
        $this->assertFalse($event->isOpenTo($this->makeMember('N')));
    }

    public function test_members_only_event_is_closed_to_pending(): void
    {
        $event = $this->makeEvent(true);
        $this->assertFalse($event->isOpenTo($this->makeMember('P')));
    }

    public function test_members_only_flag_is_cast_to_boolean(): void
    {
        $event = $this->makeEvent(true);
        $event->refresh();
        $this->assertTrue($event->members_only);
        $this->assertIsBool($event->members_only);
    }
}
