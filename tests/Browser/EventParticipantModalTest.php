<?php

namespace Tests\Browser;

use App\Models\Event;
use App\Models\Member;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class EventParticipantModalTest extends DuskTestCase
{
    public function test_participant_list_shows_status(): void
    {
        $title = 'DuskModal-' . uniqid();
        $this->cleanupEventTitles[] = $title;
        $email = 'dusk-modal-' . uniqid() . '@test.ch';
        $this->cleanupMemberEmails[] = $email;

        $event = Event::create([
            'title' => $title,
            'starts_at' => now()->addWeek(),
            'statuscode' => 'P',
        ]);

        $member = Member::create([
            'first_name' => 'DuskModal',
            'last_name' => 'Participant',
            'email' => $email,
            'statuscode' => 'A',
        ]);

        $event->members()->attach($member->id, ['status' => 'N']);

        $this->browse(function (Browser $browser) use ($event) {
            $this->loginAsAdmin($browser);

            $browser->visit('/admin/events/' . $event->id)
                ->waitForText('DuskModal')
                ->assertSee('DuskModal')
                ->assertSee('Participant')
                // Status badge 'Inscrite' (N) should be visible
                ->assertSee('Inscrite');
        });
    }

    public function test_participant_presence_toggle_shows(): void
    {
        $title = 'DuskPresence-' . uniqid();
        $this->cleanupEventTitles[] = $title;
        $email = 'dusk-pres-' . uniqid() . '@test.ch';
        $this->cleanupMemberEmails[] = $email;

        $event = Event::create([
            'title' => $title,
            'starts_at' => now()->addWeek(),
            'statuscode' => 'P',
        ]);

        $member = Member::create([
            'first_name' => 'DuskPresence',
            'last_name' => 'Toggle',
            'email' => $email,
            'statuscode' => 'A',
        ]);

        $event->members()->attach($member->id, ['status' => 'C', 'present' => true]);

        $this->browse(function (Browser $browser) use ($event) {
            $this->loginAsAdmin($browser);

            $browser->visit('/admin/events/' . $event->id)
                ->waitForText('DuskPresence')
                ->assertSee('DuskPresence');
        });
    }
}
