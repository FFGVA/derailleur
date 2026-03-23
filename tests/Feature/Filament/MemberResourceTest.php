<?php

namespace Tests\Feature\Filament;

use App\Filament\Resources\MemberResource;
use App\Filament\Resources\MemberResource\Pages\CreateMember;
use App\Filament\Resources\MemberResource\Pages\EditMember;
use App\Filament\Resources\MemberResource\Pages\ListMembers;
use App\Filament\Resources\MemberResource\Pages\ViewMember;
use App\Models\Event;
use App\Models\Invoice;
use App\Models\Member;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Livewire\Livewire;
use Tests\TestCase;

class MemberResourceTest extends TestCase
{
    use DatabaseTransactions;

    private function makeAdmin(): User
    {
        return User::create([
            'name' => 'Admin',
            'email' => 'mr-admin-' . uniqid() . '@test.ch',
            'password' => bcrypt('password'),
            'role' => 'A',
        ]);
    }

    private function makeChef(): User
    {
        $member = $this->makeMember();
        return User::create([
            'name' => 'Chef',
            'email' => 'mr-chef-' . uniqid() . '@test.ch',
            'password' => bcrypt('password'),
            'role' => 'C',
            'member_id' => $member->id,
        ]);
    }

    private function makeMember(array $overrides = []): Member
    {
        return Member::create(array_merge([
            'first_name' => 'Aline',
            'last_name' => 'Testeur',
            'email' => 'mr-mem-' . uniqid() . '@test.ch',
            'statuscode' => 'A',
        ], $overrides));
    }

    // ── List page ──

    public function test_list_page_loads(): void
    {
        $this->actingAs($this->makeAdmin())
            ->get('/admin/members')
            ->assertStatus(200);
    }

    public function test_list_shows_members(): void
    {
        $member = $this->makeMember(['first_name' => 'Zélie', 'last_name' => 'Uniquenom']);

        $this->actingAs($this->makeAdmin())
            ->get('/admin/members')
            ->assertSee('Uniquenom');
    }

    public function test_list_navigation_exists(): void
    {
        $this->actingAs($this->makeAdmin())
            ->get('/admin/members')
            ->assertSee('Membres');
    }

    // ── Create page ──

    public function test_create_page_loads(): void
    {
        $this->actingAs($this->makeAdmin())
            ->get('/admin/members/create')
            ->assertStatus(200);
    }

    public function test_create_member_persists_data(): void
    {
        $admin = $this->makeAdmin();

        Livewire::actingAs($admin)
            ->test(CreateMember::class)
            ->fillForm([
                'first_name' => 'Nouvella',
                'last_name' => 'Créée',
                'email' => 'nouvella-' . uniqid() . '@test.ch',
                'statuscode' => 'D',
            ])
            ->call('create')
            ->assertHasNoFormErrors();

        $this->assertDatabaseHas('members', [
            'first_name' => 'Nouvella',
            'last_name' => 'Créée',
            'statuscode' => 'D',
        ]);
    }

    public function test_create_requires_first_name(): void
    {
        Livewire::actingAs($this->makeAdmin())
            ->test(CreateMember::class)
            ->fillForm([
                'first_name' => '',
                'last_name' => 'Test',
                'email' => 'req-' . uniqid() . '@test.ch',
                'statuscode' => 'D',
            ])
            ->call('create')
            ->assertHasFormErrors(['first_name' => 'required']);
    }

    public function test_create_requires_last_name(): void
    {
        Livewire::actingAs($this->makeAdmin())
            ->test(CreateMember::class)
            ->fillForm([
                'first_name' => 'Test',
                'last_name' => '',
                'email' => 'req-' . uniqid() . '@test.ch',
                'statuscode' => 'D',
            ])
            ->call('create')
            ->assertHasFormErrors(['last_name' => 'required']);
    }

    public function test_create_requires_email(): void
    {
        Livewire::actingAs($this->makeAdmin())
            ->test(CreateMember::class)
            ->fillForm([
                'first_name' => 'Test',
                'last_name' => 'Test',
                'email' => '',
                'statuscode' => 'D',
            ])
            ->call('create')
            ->assertHasFormErrors(['email' => 'required']);
    }

    public function test_create_validates_email_format(): void
    {
        Livewire::actingAs($this->makeAdmin())
            ->test(CreateMember::class)
            ->fillForm([
                'first_name' => 'Test',
                'last_name' => 'Test',
                'email' => 'not-an-email',
                'statuscode' => 'D',
            ])
            ->call('create')
            ->assertHasFormErrors(['email']);
    }

    public function test_create_rejects_duplicate_email(): void
    {
        $existing = $this->makeMember(['email' => 'duplicate@test.ch']);

        Livewire::actingAs($this->makeAdmin())
            ->test(CreateMember::class)
            ->fillForm([
                'first_name' => 'Another',
                'last_name' => 'Person',
                'email' => 'duplicate@test.ch',
                'statuscode' => 'D',
            ])
            ->call('create')
            ->assertHasFormErrors(['email']);
    }

    public function test_create_requires_statuscode(): void
    {
        Livewire::actingAs($this->makeAdmin())
            ->test(CreateMember::class)
            ->fillForm([
                'first_name' => 'Test',
                'last_name' => 'Test',
                'email' => 'req-' . uniqid() . '@test.ch',
                'statuscode' => null,
            ])
            ->call('create')
            ->assertHasFormErrors(['statuscode' => 'required']);
    }

    public function test_create_forbidden_for_chef(): void
    {
        $this->actingAs($this->makeChef())
            ->get('/admin/members/create')
            ->assertForbidden();
    }

    // ── View page ──

    public function test_view_page_shows_member(): void
    {
        $member = $this->makeMember(['first_name' => 'Visible', 'last_name' => 'Personne']);

        $this->actingAs($this->makeAdmin())
            ->get(MemberResource::getUrl('view', ['record' => $member]))
            ->assertStatus(200)
            ->assertSee('Visible')
            ->assertSee('Personne');
    }

    // ── Edit page ──

    public function test_edit_page_loads(): void
    {
        $member = $this->makeMember();

        $this->actingAs($this->makeAdmin())
            ->get(MemberResource::getUrl('edit', ['record' => $member]))
            ->assertStatus(200);
    }

    public function test_edit_saves_changes(): void
    {
        $member = $this->makeMember(['first_name' => 'Avant']);

        Livewire::actingAs($this->makeAdmin())
            ->test(EditMember::class, ['record' => $member->id])
            ->fillForm([
                'first_name' => 'Après',
            ])
            ->call('save')
            ->assertHasNoFormErrors();

        $member->refresh();
        $this->assertEquals('Après', $member->first_name);
    }

    public function test_edit_validates_email_unique(): void
    {
        $existing = $this->makeMember(['email' => 'taken@test.ch']);
        $member = $this->makeMember();

        Livewire::actingAs($this->makeAdmin())
            ->test(EditMember::class, ['record' => $member->id])
            ->fillForm(['email' => 'taken@test.ch'])
            ->call('save')
            ->assertHasFormErrors(['email']);
    }

    public function test_edit_allows_same_email_on_own_record(): void
    {
        $member = $this->makeMember(['email' => 'same@test.ch']);

        Livewire::actingAs($this->makeAdmin())
            ->test(EditMember::class, ['record' => $member->id])
            ->fillForm(['email' => 'same@test.ch'])
            ->call('save')
            ->assertHasNoFormErrors();
    }

    // ── Delete (dependency check) ──

    public function test_delete_member_without_dependencies(): void
    {
        $member = $this->makeMember();

        Livewire::actingAs($this->makeAdmin())
            ->test(EditMember::class, ['record' => $member->id])
            ->callAction('delete');

        $this->assertSoftDeleted('members', ['id' => $member->id]);
    }

    public function test_delete_blocked_when_member_has_invoices(): void
    {
        $member = $this->makeMember();
        Invoice::create([
            'member_id' => $member->id,
            'invoice_number' => Invoice::generateNumber($member),
            'amount' => 50.00,
            'statuscode' => 'N',
        ]);

        Livewire::actingAs($this->makeAdmin())
            ->test(EditMember::class, ['record' => $member->id])
            ->callAction('delete')
            ->assertNotified('Suppression impossible');

        $this->assertNotSoftDeleted('members', ['id' => $member->id]);
    }

    public function test_delete_blocked_when_member_has_events(): void
    {
        $member = $this->makeMember();
        $event = Event::create([
            'title' => 'Sortie test',
            'starts_at' => now()->addDay(),
            'statuscode' => 'P',
        ]);
        $event->members()->attach($member->id, ['status' => 'N']);

        Livewire::actingAs($this->makeAdmin())
            ->test(EditMember::class, ['record' => $member->id])
            ->callAction('delete')
            ->assertNotified('Suppression impossible');

        $this->assertNotSoftDeleted('members', ['id' => $member->id]);
    }

    public function test_delete_also_soft_deletes_phones(): void
    {
        $member = $this->makeMember();
        $member->phones()->create([
            'phone_number' => '+41 79 000 00 00',
            'label' => 'Mobile',
        ]);

        Livewire::actingAs($this->makeAdmin())
            ->test(EditMember::class, ['record' => $member->id])
            ->callAction('delete');

        $this->assertSoftDeleted('members', ['id' => $member->id]);
        $this->assertSoftDeleted('member_phones', ['member_id' => $member->id]);
    }
}
