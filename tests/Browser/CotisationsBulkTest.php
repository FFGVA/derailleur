<?php

namespace Tests\Browser;

use App\Models\Member;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class CotisationsBulkTest extends DuskTestCase
{
    public function test_cotisations_page_shows_expiring_members(): void
    {
        $email = 'dusk-cot-' . uniqid() . '@test.ch';
        $this->cleanupMemberEmails[] = $email;

        Member::create([
            'first_name' => 'DuskCotis',
            'last_name' => 'Expiring',
            'email' => $email,
            'statuscode' => 'A',
            'membership_start' => now()->subYear(),
            'membership_end' => now()->endOfMonth(),
            'address' => 'Rue Test 1',
            'postal_code' => '1200',
            'city' => 'Genève',
            'country' => 'CH',
        ]);

        $this->browse(function (Browser $browser) {
            $this->loginAsAdmin($browser);

            $browser->visit('/admin/cotisations')
                ->waitForText('Cotisations')
                ->assertSee('DuskCotis')
                ->assertSee('Expiring');
        });
    }

    public function test_cotisations_page_shows_send_action(): void
    {
        $email = 'dusk-cot-action-' . uniqid() . '@test.ch';
        $this->cleanupMemberEmails[] = $email;

        Member::create([
            'first_name' => 'DuskAction',
            'last_name' => 'Send',
            'email' => $email,
            'statuscode' => 'A',
            'membership_start' => now()->subYear(),
            'membership_end' => now()->endOfMonth(),
            'address' => 'Rue Test 1',
            'postal_code' => '1200',
            'city' => 'Genève',
            'country' => 'CH',
        ]);

        $this->browse(function (Browser $browser) {
            $this->loginAsAdmin($browser);

            $browser->visit('/admin/cotisations')
                ->waitForText('DuskAction')
                ->assertSee('DuskAction')
                // The "Envoyer" action should be available
                ->assertSee('Envoyer');
        });
    }
}
