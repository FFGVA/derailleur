<?php

namespace Tests\Unit\Models;

use App\Enums\EventMemberStatus;
use App\Models\Event;
use App\Models\EventMember;
use App\Models\Member;
use Illuminate\Database\Eloquent\Relations\Pivot;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class EventMemberTest extends TestCase
{
    use DatabaseTransactions;

    private function makeMember(): Member
    {
        return Member::create([
            'first_name' => 'Pivot',
            'last_name' => 'Test',
            'email' => 'pivot-' . uniqid() . '@example.com',
            'statuscode' => 'A',
        ]);
    }

    private function makeEvent(): Event
    {
        return Event::create([
            'title' => 'Pivot Event',
            'starts_at' => '2025-06-01 08:00:00',
            'ends_at' => '2025-06-01 12:00:00',
            'statuscode' => 'N',
        ]);
    }

    public function test_it_is_a_pivot(): void
    {
        $pivot = new EventMember();

        $this->assertInstanceOf(Pivot::class, $pivot);
    }

    public function test_status_casts_to_event_member_status_enum(): void
    {
        $member = $this->makeMember();
        $event = $this->makeEvent();

        $event->members()->attach($member->id, [
            'status' => 'C',
            'present' => false,
        ]);

        $pivot = EventMember::where('event_id', $event->id)
            ->where('member_id', $member->id)
            ->first();

        $this->assertInstanceOf(EventMemberStatus::class, $pivot->status);
        $this->assertSame(EventMemberStatus::Confirme, $pivot->status);
    }

    public function test_present_casts_to_boolean(): void
    {
        $member = $this->makeMember();
        $event = $this->makeEvent();

        $event->members()->attach($member->id, [
            'status' => 'N',
            'present' => 1,
        ]);

        $pivot = EventMember::where('event_id', $event->id)
            ->where('member_id', $member->id)
            ->first();

        $this->assertIsBool($pivot->present);
        $this->assertTrue($pivot->present);
    }

    public function test_no_created_at_column(): void
    {
        $this->assertNull(EventMember::CREATED_AT);
    }
}
