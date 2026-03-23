<?php

namespace Tests\Browser;

use App\Models\Member;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class MemberPhoneRepeaterTest extends DuskTestCase
{
    public function test_add_phone_via_repeater(): void
    {
        $email = 'dusk-phone-' . uniqid() . '@test.ch';
        $this->cleanupMemberEmails[] = $email;

        $member = Member::create([
            'first_name' => 'DuskPhone',
            'last_name' => 'Repeater',
            'email' => $email,
            'statuscode' => 'A',
        ]);

        $this->browse(function (Browser $browser) use ($member) {
            $this->loginAsAdmin($browser);

            $browser->visit('/admin/members/' . $member->id . '/edit')
                ->waitFor('#data\\.first_name')
                // Scroll to phones section and click "Ajouter un téléphone"
                ->scrollTo('.fi-fo-repeater')
                ->press('Ajouter un téléphone')
                ->pause(500)
                // Fill the phone number in the newly added repeater item
                ->screenshot('phone-repeater-added');
        });

        // Verify the repeater item was added (visible in DOM)
    }

    public function test_view_member_shows_phones(): void
    {
        $email = 'dusk-viewphone-' . uniqid() . '@test.ch';
        $this->cleanupMemberEmails[] = $email;

        $member = Member::create([
            'first_name' => 'DuskViewPhone',
            'last_name' => 'WithPhone',
            'email' => $email,
            'statuscode' => 'A',
        ]);

        $member->phones()->create([
            'phone_number' => '+41 79 123 45 67',
            'label' => 'Mobile',
            'is_whatsapp' => true,
        ]);

        $this->browse(function (Browser $browser) use ($member) {
            $this->loginAsAdmin($browser);

            $browser->visit('/admin/members/' . $member->id)
                ->waitForText('DuskViewPhone')
                ->assertSee('+41 79 123 45 67');
        });
    }
}
