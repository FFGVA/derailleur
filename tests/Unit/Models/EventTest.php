<?php

namespace Tests\Unit\Models;

use App\Enums\EventStatus;
use App\Models\Event;
use App\Models\Member;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Carbon;
use Tests\TestCase;

class EventTest extends TestCase
{
    use DatabaseTransactions;

    private function makeMember(array $overrides = []): Member
    {
        return Member::create(array_merge([
            'first_name' => 'Test',
            'last_name' => 'Member',
            'email' => 'member-' . uniqid() . '@example.com',
            'statuscode' => 'A',
        ], $overrides));
    }

    private function makeEvent(array $overrides = []): Event
    {
        return Event::create(array_merge([
            'title' => 'Test Event',
            'starts_at' => '2025-06-01 08:00:00',
            'ends_at' => '2025-06-01 12:00:00',
            'statuscode' => 'N',
        ], $overrides));
    }

    public function test_can_create_event_with_required_fields(): void
    {
        $event = $this->makeEvent(['title' => 'Sortie dominicale']);

        $this->assertDatabaseHas('events', [
            'id' => $event->id,
            'title' => 'Sortie dominicale',
        ]);
    }

    public function test_fillable_fields_work(): void
    {
        $member = $this->makeMember();

        $event = $this->makeEvent([
            'title' => 'Grand Tour',
            'description' => 'A wonderful ride',
            'location' => 'Geneve',
            'starts_at' => '2025-07-01 07:00:00',
            'ends_at' => '2025-07-01 14:00:00',
            'max_participants' => 25,
            'price' => '15.50',
            'statuscode' => 'P',
        ]);

        $this->assertSame('Grand Tour', $event->title);
        $this->assertSame('A wonderful ride', $event->description);
        $this->assertSame('Geneve', $event->location);
        $this->assertSame(25, $event->max_participants);
    }

    public function test_statuscode_casts_to_event_status_enum(): void
    {
        $event = $this->makeEvent(['statuscode' => 'P']);
        $event->refresh();

        $this->assertInstanceOf(EventStatus::class, $event->statuscode);
        $this->assertSame(EventStatus::Publie, $event->statuscode);
    }

    public function test_price_casts_to_decimal(): void
    {
        $event = $this->makeEvent(['price' => 29.99]);
        $event->refresh();

        $this->assertSame('29.99', $event->price);
    }

    public function test_price_non_member_casts_to_decimal(): void
    {
        $event = $this->makeEvent(['price_non_member' => 35.00]);
        $event->refresh();

        $this->assertSame('35.00', $event->price_non_member);
    }

    public function test_price_non_member_defaults_to_null(): void
    {
        $event = $this->makeEvent();
        $event->refresh();

        $this->assertNull($event->price_non_member);
    }

    public function test_starts_at_and_ends_at_cast_to_datetime(): void
    {
        $event = $this->makeEvent([
            'starts_at' => '2025-06-15 09:30:00',
            'ends_at' => '2025-06-15 16:00:00',
        ]);
        $event->refresh();

        $this->assertInstanceOf(Carbon::class, $event->starts_at);
        $this->assertInstanceOf(Carbon::class, $event->ends_at);
        $this->assertSame('2025-06-15 09:30:00', $event->starts_at->format('Y-m-d H:i:s'));
        $this->assertSame('2025-06-15 16:00:00', $event->ends_at->format('Y-m-d H:i:s'));
    }

    public function test_members_relationship_returns_belongs_to_many(): void
    {
        $event = $this->makeEvent();

        $this->assertInstanceOf(BelongsToMany::class, $event->members());
    }

    public function test_chefs_relationship_returns_belongs_to_many(): void
    {
        $member = $this->makeMember();
        $event = $this->makeEvent();
        \App\Models\EventChef::create(['event_id' => $event->id, 'member_id' => $member->id, 'sort_order' => 0]);

        $this->assertInstanceOf(BelongsToMany::class, $event->chefs());
        $this->assertEquals(1, $event->chefs()->count());
        $this->assertSame($member->id, $event->chefs->first()->id);
    }

    public function test_soft_delete_works(): void
    {
        $event = $this->makeEvent();
        $eventId = $event->id;
        $event->delete();

        $this->assertSoftDeleted('events', ['id' => $eventId]);
        $this->assertNull(Event::find($eventId));
        $this->assertNotNull(Event::withTrashed()->find($eventId));
    }

    public function test_no_created_at_column(): void
    {
        $this->assertNull(Event::CREATED_AT);

        $event = $this->makeEvent();

        $this->assertNull($event->created_at);
    }
}
