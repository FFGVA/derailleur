<?php

namespace Tests\Unit\Models;

use App\Enums\MemberStatus;
use App\Models\Event;
use App\Models\Member;
use App\Models\MemberPhone;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class MemberTest extends TestCase
{
    use DatabaseTransactions;

    private function makeMember(array $overrides = []): Member
    {
        return Member::create(array_merge([
            'first_name' => 'Test',
            'last_name' => 'User',
            'email' => 'test-' . uniqid() . '@example.com',
            'statuscode' => 'A',
        ], $overrides));
    }

    public function test_can_create_member_with_required_fields(): void
    {
        $member = $this->makeMember([
            'first_name' => 'Jean',
            'last_name' => 'Dupont',
        ]);

        $this->assertDatabaseHas('members', [
            'id' => $member->id,
            'first_name' => 'Jean',
            'last_name' => 'Dupont',
        ]);
    }

    public function test_fillable_fields_work(): void
    {
        $member = $this->makeMember([
            'first_name' => 'Marie',
            'last_name' => 'Martin',
            'email' => 'marie-' . uniqid() . '@example.com',
            'date_of_birth' => '1990-05-15',
            'address' => '10 rue de Geneve',
            'postal_code' => '1200',
            'city' => 'Geneve',
            'country' => 'CH',
            'statuscode' => 'A',
            'membership_start' => '2024-01-01',
            'membership_end' => '2024-12-31',
            'notes' => 'Test notes',
            'is_invitee' => true,
            'metadata' => ['key' => 'value'],
        ]);

        $this->assertSame('Marie', $member->first_name);
        $this->assertSame('Martin', $member->last_name);
        $this->assertSame('10 rue de Geneve', $member->address);
        $this->assertSame('1200', $member->postal_code);
        $this->assertSame('Geneve', $member->city);
        $this->assertSame('CH', $member->country);
        $this->assertSame('Test notes', $member->notes);
    }

    public function test_statuscode_casts_to_member_status_enum(): void
    {
        $member = $this->makeMember(['statuscode' => 'A']);
        $member->refresh();

        $this->assertInstanceOf(MemberStatus::class, $member->statuscode);
        $this->assertSame(MemberStatus::Actif, $member->statuscode);
    }

    public function test_is_invitee_casts_to_boolean(): void
    {
        $member = $this->makeMember(['is_invitee' => 1]);
        $member->refresh();

        $this->assertIsBool($member->is_invitee);
        $this->assertTrue($member->is_invitee);
    }

    public function test_metadata_casts_to_array(): void
    {
        $meta = ['sport' => 'cyclisme', 'level' => 3];
        $member = $this->makeMember(['metadata' => $meta]);
        $member->refresh();

        $this->assertIsArray($member->metadata);
        $this->assertSame('cyclisme', $member->metadata['sport']);
        $this->assertSame(3, $member->metadata['level']);
    }

    public function test_phones_relationship_returns_has_many(): void
    {
        $member = $this->makeMember();

        $this->assertInstanceOf(HasMany::class, $member->phones());

        MemberPhone::create([
            'member_id' => $member->id,
            'phone_number' => '+41791234567',
            'label' => 'Mobile',
            'is_whatsapp' => true,
        ]);

        $this->assertCount(1, $member->phones);
        $this->assertInstanceOf(MemberPhone::class, $member->phones->first());
    }

    public function test_events_relationship_returns_belongs_to_many(): void
    {
        $member = $this->makeMember();

        $this->assertInstanceOf(BelongsToMany::class, $member->events());
    }

    public function test_led_events_relationship_returns_has_many(): void
    {
        $member = $this->makeMember();

        $this->assertInstanceOf(HasMany::class, $member->ledEvents());

        Event::create([
            'title' => 'Sortie test',
            'starts_at' => '2025-06-01 08:00:00',
            'ends_at' => '2025-06-01 12:00:00',
            'statuscode' => 'N',
            'chef_peloton_id' => $member->id,
        ]);

        $member->refresh();

        $this->assertCount(1, $member->ledEvents);
        $this->assertInstanceOf(Event::class, $member->ledEvents->first());
    }

    public function test_soft_delete_works(): void
    {
        $member = $this->makeMember(['statuscode' => 'D']);
        $memberId = $member->id;
        $member->delete();

        $this->assertSoftDeleted('members', ['id' => $memberId]);
        $this->assertNull(Member::find($memberId));
        $this->assertNotNull(Member::withTrashed()->find($memberId));
    }

    public function test_no_created_at_column(): void
    {
        $this->assertNull(Member::CREATED_AT);

        $member = $this->makeMember(['statuscode' => 'D']);

        $this->assertNull($member->created_at);
    }
}
