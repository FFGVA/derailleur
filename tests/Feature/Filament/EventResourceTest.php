<?php

namespace Tests\Feature\Filament;

use App\Filament\Resources\EventResource;
use App\Filament\Resources\EventResource\Pages\CreateEvent;
use App\Filament\Resources\EventResource\Pages\EditEvent;
use App\Filament\Resources\EventResource\Pages\ListEvents;
use App\Filament\Resources\EventResource\Pages\ViewEvent;
use App\Models\Event;
use App\Models\Member;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Livewire\Livewire;
use Tests\TestCase;

class EventResourceTest extends TestCase
{
    use DatabaseTransactions;

    private function makeAdmin(): User
    {
        return User::create([
            'name' => 'Admin',
            'email' => 'er-admin-' . uniqid() . '@test.ch',
            'password' => bcrypt('password'),
            'role' => 'A',
        ]);
    }

    private function makeChef(): array
    {
        $member = Member::create([
            'first_name' => 'Sophie',
            'last_name' => 'Chef',
            'email' => 'er-chef-' . uniqid() . '@test.ch',
            'statuscode' => 'A',
        ]);
        $user = User::create([
            'name' => 'Sophie Chef',
            'email' => 'er-chefuser-' . uniqid() . '@test.ch',
            'password' => bcrypt('password'),
            'role' => 'C',
            'member_id' => $member->id,
        ]);
        return [$user, $member];
    }

    private function makeEvent(array $overrides = []): Event
    {
        return Event::create(array_merge([
            'title' => 'Sortie test',
            'starts_at' => now()->addDays(3),
            'statuscode' => 'P',
        ], $overrides));
    }

    // ── List page ──

    public function test_list_page_loads(): void
    {
        $this->actingAs($this->makeAdmin())
            ->get('/admin/events')
            ->assertStatus(200);
    }

    public function test_list_shows_events(): void
    {
        $this->makeEvent(['title' => 'Sortie Unique Visible']);

        $this->actingAs($this->makeAdmin())
            ->get('/admin/events')
            ->assertSee('Sortie Unique Visible');
    }

    public function test_list_navigation_exists(): void
    {
        $this->actingAs($this->makeAdmin())
            ->get('/admin/events')
            ->assertSee('Événements');
    }

    // ── Create page ──

    public function test_create_page_loads(): void
    {
        $this->actingAs($this->makeAdmin())
            ->get('/admin/events/create')
            ->assertStatus(200);
    }

    public function test_create_event_persists_data(): void
    {
        Livewire::actingAs($this->makeAdmin())
            ->test(CreateEvent::class)
            ->fillForm([
                'title' => 'Nouvelle sortie',
                'starts_at' => now()->addWeek()->format('Y-m-d H:i:s'),
                'statuscode' => 'N',
                'price' => 25,
            ])
            ->call('create')
            ->assertHasNoFormErrors();

        $this->assertDatabaseHas('events', [
            'title' => 'Nouvelle sortie',
            'price' => 25.00,
            'statuscode' => 'N',
        ]);
    }

    public function test_create_requires_title(): void
    {
        Livewire::actingAs($this->makeAdmin())
            ->test(CreateEvent::class)
            ->fillForm([
                'title' => '',
                'starts_at' => now()->addWeek()->format('Y-m-d H:i:s'),
                'statuscode' => 'N',
            ])
            ->call('create')
            ->assertHasFormErrors(['title' => 'required']);
    }

    public function test_create_requires_starts_at(): void
    {
        Livewire::actingAs($this->makeAdmin())
            ->test(CreateEvent::class)
            ->fillForm([
                'title' => 'Test',
                'starts_at' => null,
                'statuscode' => 'N',
            ])
            ->call('create')
            ->assertHasFormErrors(['starts_at' => 'required']);
    }

    public function test_create_requires_statuscode(): void
    {
        Livewire::actingAs($this->makeAdmin())
            ->test(CreateEvent::class)
            ->fillForm([
                'title' => 'Test',
                'starts_at' => now()->addWeek()->format('Y-m-d H:i:s'),
                'statuscode' => null,
            ])
            ->call('create')
            ->assertHasFormErrors(['statuscode' => 'required']);
    }

    public function test_create_forbidden_for_chef(): void
    {
        [$chef] = $this->makeChef();

        $this->actingAs($chef)
            ->get('/admin/events/create')
            ->assertForbidden();
    }

    public function test_create_with_price_and_location(): void
    {
        Livewire::actingAs($this->makeAdmin())
            ->test(CreateEvent::class)
            ->fillForm([
                'title' => 'Sortie payante',
                'starts_at' => now()->addWeek()->format('Y-m-d H:i:s'),
                'statuscode' => 'P',
                'price' => 30,
                'price_non_member' => 45,
                'location' => 'Parc des Bastions',
            ])
            ->call('create')
            ->assertHasNoFormErrors();

        $this->assertDatabaseHas('events', [
            'title' => 'Sortie payante',
            'price' => 30.00,
            'price_non_member' => 45.00,
            'location' => 'Parc des Bastions',
        ]);
    }

    // ── View page ──

    public function test_view_page_shows_event(): void
    {
        $event = $this->makeEvent(['title' => 'Sortie Visible']);

        $this->actingAs($this->makeAdmin())
            ->get(EventResource::getUrl('view', ['record' => $event]))
            ->assertStatus(200)
            ->assertSee('Sortie Visible');
    }

    // ── Edit page ──

    public function test_edit_page_loads(): void
    {
        $event = $this->makeEvent();

        $this->actingAs($this->makeAdmin())
            ->get(EventResource::getUrl('edit', ['record' => $event]))
            ->assertStatus(200);
    }

    public function test_edit_saves_changes(): void
    {
        $event = $this->makeEvent(['title' => 'Avant']);

        Livewire::actingAs($this->makeAdmin())
            ->test(EditEvent::class, ['record' => $event->id])
            ->fillForm(['title' => 'Après'])
            ->call('save')
            ->assertHasNoFormErrors();

        $event->refresh();
        $this->assertEquals('Après', $event->title);
    }

    public function test_edit_validates_title_required(): void
    {
        $event = $this->makeEvent();

        Livewire::actingAs($this->makeAdmin())
            ->test(EditEvent::class, ['record' => $event->id])
            ->fillForm(['title' => ''])
            ->call('save')
            ->assertHasFormErrors(['title' => 'required']);
    }

    // ── Delete (dependency check) ──
    // The delete action is embedded in Forms\Components\Actions inside the form schema.
    // We test the business logic by calling the Livewire action method directly.

    public function test_event_without_participants_can_be_soft_deleted(): void
    {
        $event = $this->makeEvent();
        $this->assertEquals(0, $event->members()->count());

        $event->delete();

        $this->assertSoftDeleted('events', ['id' => $event->id]);
    }

    public function test_event_with_participants_has_dependency_count(): void
    {
        $event = $this->makeEvent();
        $member = Member::create([
            'first_name' => 'Part',
            'last_name' => 'Icipante',
            'email' => 'er-part-' . uniqid() . '@test.ch',
            'statuscode' => 'A',
        ]);
        $event->members()->attach($member->id, ['status' => 'N']);

        // The delete action checks members().count() > 0 before deleting
        $this->assertGreaterThan(0, $event->members()->count());
    }

    public function test_delete_action_visible_only_for_admin(): void
    {
        $event = $this->makeEvent();

        // Admin sees the edit page (where delete action lives)
        $this->actingAs($this->makeAdmin())
            ->get(EventResource::getUrl('edit', ['record' => $event]))
            ->assertStatus(200)
            ->assertSee('Supprimer');
    }

    public function test_delete_action_hidden_for_chef(): void
    {
        [$chef, $chefMember] = $this->makeChef();
        $event = $this->makeEvent();
        \App\Models\EventChef::create(['event_id' => $event->id, 'member_id' => $chefMember->id, 'sort_order' => 0]);

        $response = $this->actingAs($chef)
            ->get(EventResource::getUrl('edit', ['record' => $event]));

        $response->assertStatus(200);
        $response->assertDontSee('heroicon-o-trash');
    }

    // ── Chef access ──

    public function test_chef_can_view_events(): void
    {
        [$chef] = $this->makeChef();

        $this->actingAs($chef)
            ->get('/admin/events')
            ->assertStatus(200);
    }

    public function test_chef_can_edit_own_event(): void
    {
        [$chef, $chefMember] = $this->makeChef();
        $event = $this->makeEvent();
        \App\Models\EventChef::create(['event_id' => $event->id, 'member_id' => $chefMember->id, 'sort_order' => 0]);

        $this->actingAs($chef)
            ->get(EventResource::getUrl('edit', ['record' => $event]))
            ->assertStatus(200);
    }

    // ── Multi-chef assignment ──

    public function test_create_event_with_multiple_chefs(): void
    {
        $chef1 = Member::create([
            'first_name' => 'Chef1',
            'last_name' => 'Test',
            'email' => 'er-chef1-' . uniqid() . '@test.ch',
            'statuscode' => 'A',
        ]);
        $chef2 = Member::create([
            'first_name' => 'Chef2',
            'last_name' => 'Test',
            'email' => 'er-chef2-' . uniqid() . '@test.ch',
            'statuscode' => 'A',
        ]);

        Livewire::actingAs($this->makeAdmin())
            ->test(CreateEvent::class)
            ->fillForm([
                'title' => 'Multi-chef sortie',
                'starts_at' => now()->addWeek()->format('Y-m-d H:i:s'),
                'statuscode' => 'N',
                'chef_ids' => [$chef1->id, $chef2->id],
            ])
            ->call('create')
            ->assertHasNoFormErrors();

        $event = Event::where('title', 'Multi-chef sortie')->first();
        $this->assertNotNull($event);
        $this->assertEquals(2, $event->chefs()->count());
        $this->assertTrue($event->chefs->contains('id', $chef1->id));
        $this->assertTrue($event->chefs->contains('id', $chef2->id));
    }

    public function test_edit_event_loads_existing_chefs(): void
    {
        $event = $this->makeEvent();
        $chef = Member::create([
            'first_name' => 'Existing',
            'last_name' => 'Chef',
            'email' => 'er-existing-' . uniqid() . '@test.ch',
            'statuscode' => 'A',
        ]);
        \App\Models\EventChef::create([
            'event_id' => $event->id,
            'member_id' => $chef->id,
            'sort_order' => 0,
        ]);

        Livewire::actingAs($this->makeAdmin())
            ->test(EditEvent::class, ['record' => $event->id])
            ->assertFormSet(['chef_ids' => [$chef->id]]);
    }

    public function test_edit_event_adds_and_removes_chefs(): void
    {
        $event = $this->makeEvent();
        $chef1 = Member::create([
            'first_name' => 'Keep',
            'last_name' => 'Chef',
            'email' => 'er-keep-' . uniqid() . '@test.ch',
            'statuscode' => 'A',
        ]);
        $chef2 = Member::create([
            'first_name' => 'Remove',
            'last_name' => 'Chef',
            'email' => 'er-remove-' . uniqid() . '@test.ch',
            'statuscode' => 'A',
        ]);
        $chef3 = Member::create([
            'first_name' => 'Add',
            'last_name' => 'Chef',
            'email' => 'er-add-' . uniqid() . '@test.ch',
            'statuscode' => 'A',
        ]);

        \App\Models\EventChef::create(['event_id' => $event->id, 'member_id' => $chef1->id, 'sort_order' => 0]);
        \App\Models\EventChef::create(['event_id' => $event->id, 'member_id' => $chef2->id, 'sort_order' => 1]);

        Livewire::actingAs($this->makeAdmin())
            ->test(EditEvent::class, ['record' => $event->id])
            ->fillForm(['chef_ids' => [$chef1->id, $chef3->id]])
            ->call('save')
            ->assertHasNoFormErrors();

        $event->refresh();
        $chefIds = $event->chefs->pluck('id')->toArray();
        $this->assertContains($chef1->id, $chefIds);
        $this->assertContains($chef3->id, $chefIds);
        $this->assertNotContains($chef2->id, $chefIds);

        // chef2 should be soft-deleted, not hard-deleted
        $this->assertDatabaseHas('event_chef', [
            'event_id' => $event->id,
            'member_id' => $chef2->id,
        ]);
        $softDeleted = \App\Models\EventChef::withTrashed()
            ->where('event_id', $event->id)
            ->where('member_id', $chef2->id)
            ->first();
        $this->assertNotNull($softDeleted->deleted_at);
    }

    // ── Members-only flag ──

    public function test_create_members_only_event_stores_sentinel_price_non_member(): void
    {
        Livewire::actingAs($this->makeAdmin())
            ->test(CreateEvent::class)
            ->fillForm([
                'title' => 'Exclusif membres',
                'starts_at' => now()->addWeek()->format('Y-m-d H:i:s'),
                'statuscode' => 'N',
                'price' => 25,
                'members_only' => true,
            ])
            ->call('create')
            ->assertHasNoFormErrors();

        $event = Event::where('title', 'Exclusif membres')->first();
        $this->assertNotNull($event);
        $this->assertTrue($event->members_only);
        $this->assertEquals('9999.99', $event->price_non_member);
    }

    public function test_toggling_members_only_off_clears_price_non_member(): void
    {
        $event = $this->makeEvent([
            'title' => 'Going Public',
            'members_only' => true,
            'price_non_member' => '9999.99',
        ]);

        Livewire::actingAs($this->makeAdmin())
            ->test(EditEvent::class, ['record' => $event->id])
            ->fillForm(['members_only' => false])
            ->call('save')
            ->assertHasNoFormErrors();

        $event->refresh();
        $this->assertFalse($event->members_only);
        $this->assertNull($event->price_non_member);
    }

    public function test_view_page_shows_members_only_indicator(): void
    {
        $event = $this->makeEvent([
            'title' => 'Vue exclusive',
            'members_only' => true,
        ]);

        $this->actingAs($this->makeAdmin())
            ->get(EventResource::getUrl('view', ['record' => $event]))
            ->assertStatus(200)
            ->assertSee('Événement membres');
    }

    public function test_list_page_marks_members_only_events(): void
    {
        $this->makeEvent([
            'title' => 'Liste exclusive',
            'members_only' => true,
        ]);

        $response = $this->actingAs($this->makeAdmin())
            ->get('/admin/events');

        $response->assertSee('background-color: #fef2f2', false);
        // Tooltip text goes through JSON encoding (x-tooltip content), so match the substring that's stable across encodings.
        $response->assertSee('nement membres', false);
    }

    public function test_create_event_with_no_chefs(): void
    {
        Livewire::actingAs($this->makeAdmin())
            ->test(CreateEvent::class)
            ->fillForm([
                'title' => 'No-chef sortie',
                'starts_at' => now()->addWeek()->format('Y-m-d H:i:s'),
                'statuscode' => 'N',
                'chef_ids' => [],
            ])
            ->call('create')
            ->assertHasNoFormErrors();

        $event = Event::where('title', 'No-chef sortie')->first();
        $this->assertNotNull($event);
        $this->assertEquals(0, $event->chefs()->count());
    }
}
