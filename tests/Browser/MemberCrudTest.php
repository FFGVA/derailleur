<?php

namespace Tests\Browser;

use App\Models\Member;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class MemberCrudTest extends DuskTestCase
{
    public function test_create_member(): void
    {
        $email = 'dusk-create-' . uniqid() . '@test.ch';
        $this->cleanupMemberEmails[] = $email;

        $this->browse(function (Browser $browser) use ($email) {
            $this->loginAsAdmin($browser);

            $browser->visit('/admin/members/create')
                ->waitFor('#data\\.first_name')
                ->type('#data\\.first_name', 'DuskCreate')
                ->type('#data\\.last_name', 'Testmembre')
                ->type('#data\\.email', $email)
                ->press('Créer')
                ->waitForLocation('/admin/members')
                ->assertSee('DuskCreate');
        });

        $this->assertDatabaseHas('members', [
            'email' => $email,
            'first_name' => 'DuskCreate',
            'last_name' => 'Testmembre',
        ]);
    }

    public function test_view_member_from_list(): void
    {
        $email = 'dusk-view-' . uniqid() . '@test.ch';
        $this->cleanupMemberEmails[] = $email;

        $member = Member::create([
            'first_name' => 'DuskView',
            'last_name' => 'Visible',
            'email' => $email,
            'statuscode' => 'A',
        ]);

        $this->browse(function (Browser $browser) use ($member, $email) {
            $this->loginAsAdmin($browser);

            // Click row to go to view page
            $browser->visit('/admin/members/' . $member->id)
                ->waitForText('DuskView')
                ->assertSee('DuskView')
                ->assertSee('Visible')
                ->assertSee($email)
                ->assertSee('Modifier');
        });
    }

    public function test_edit_member(): void
    {
        $email = 'dusk-edit-' . uniqid() . '@test.ch';
        $this->cleanupMemberEmails[] = $email;

        $member = Member::create([
            'first_name' => 'BeforeEdit',
            'last_name' => 'DuskEdit',
            'email' => $email,
            'statuscode' => 'A',
        ]);

        $this->browse(function (Browser $browser) use ($member) {
            $this->loginAsAdmin($browser);

            $browser->visit('/admin/members/' . $member->id . '/edit')
                ->waitFor('#data\\.first_name')
                ->clear('#data\\.first_name')
                ->type('#data\\.first_name', 'AfterEdit')
                ->press('Sauvegarder')
                ->waitForText('Sauvegardé');
        });

        $member->refresh();
        $this->assertEquals('AfterEdit', $member->first_name);
    }

    public function test_delete_member_without_dependencies(): void
    {
        $email = 'dusk-delete-' . uniqid() . '@test.ch';
        // No cleanup needed — will be soft-deleted by the test

        $member = Member::create([
            'first_name' => 'DuskDelete',
            'last_name' => 'NoDepend',
            'email' => $email,
            'statuscode' => 'D',
        ]);

        $this->browse(function (Browser $browser) use ($member) {
            $this->loginAsAdmin($browser);

            $browser->visit('/admin/members/' . $member->id . '/edit')
                ->waitFor('#data\\.first_name')
                ->press('Supprimer')
                ->waitFor('.fi-modal-footer')
                ->within('.fi-modal-footer', function (Browser $modal) {
                    $modal->press('Confirmer');
                })
                ->waitForLocation('/admin/members');
        });

        $this->assertSoftDeleted('members', ['id' => $member->id]);

        // Force-cleanup the soft-deleted record
        $member->forceDelete();
    }

    public function test_create_member_shows_on_list(): void
    {
        $email = 'dusk-list-' . uniqid() . '@test.ch';
        $this->cleanupMemberEmails[] = $email;

        Member::create([
            'first_name' => 'DuskList',
            'last_name' => 'CheckRow',
            'email' => $email,
            'statuscode' => 'A',
        ]);

        $this->browse(function (Browser $browser) {
            $this->loginAsAdmin($browser);

            $browser->visit('/admin/members')
                ->waitForText('DuskList')
                ->assertSee('DuskList')
                ->assertSee('CheckRow');
        });
    }
}
