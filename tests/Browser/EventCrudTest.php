<?php

namespace Tests\Browser;

use App\Models\Event;
use App\Models\Member;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class EventCrudTest extends DuskTestCase
{
    public function test_create_event(): void
    {
        $title = 'DuskSortie-' . uniqid();
        $this->cleanupEventTitles[] = $title;

        $this->browse(function (Browser $browser) use ($title) {
            $this->loginAsAdmin($browser);

            $browser->visit('/admin/events/create')
                ->waitFor('#data\\.title')
                ->type('#data\\.title', $title)
                // starts_at is a DateTimePicker — type into the native input
                ->type('#data\\.starts_at', now()->addWeek()->format('Y-m-d\TH:i'))
                ->press('Créer')
                ->waitForLocation('/admin/events')
                ->assertSee($title);
        });

        $this->assertDatabaseHas('events', ['title' => $title]);
    }

    public function test_view_event(): void
    {
        $title = 'DuskView-' . uniqid();
        $this->cleanupEventTitles[] = $title;

        $event = Event::create([
            'title' => $title,
            'starts_at' => now()->addWeek(),
            'statuscode' => 'P',
            'location' => 'Genève',
        ]);

        $this->browse(function (Browser $browser) use ($event, $title) {
            $this->loginAsAdmin($browser);

            $browser->visit('/admin/events/' . $event->id)
                ->waitForText($title)
                ->assertSee($title)
                ->assertSee('Genève')
                ->assertSee('Modifier');
        });
    }

    public function test_edit_event(): void
    {
        $title = 'DuskEditBefore-' . uniqid();
        $this->cleanupEventTitles[] = $title;
        $newTitle = 'DuskEditAfter-' . uniqid();
        $this->cleanupEventTitles[] = $newTitle;

        $event = Event::create([
            'title' => $title,
            'starts_at' => now()->addWeek(),
            'statuscode' => 'N',
        ]);

        $this->browse(function (Browser $browser) use ($event, $newTitle) {
            $this->loginAsAdmin($browser);

            $browser->visit('/admin/events/' . $event->id . '/edit')
                ->waitFor('#data\\.title')
                ->clear('#data\\.title')
                ->type('#data\\.title', $newTitle)
                ->press('Sauvegarder')
                ->waitForText('Sauvegardé');
        });

        $event->refresh();
        $this->assertEquals($newTitle, $event->title);
    }

    public function test_event_list_shows_events(): void
    {
        $title = 'DuskListEvt-' . uniqid();
        $this->cleanupEventTitles[] = $title;

        Event::create([
            'title' => $title,
            'starts_at' => now()->addWeek(),
            'statuscode' => 'P',
        ]);

        $this->browse(function (Browser $browser) use ($title) {
            $this->loginAsAdmin($browser);

            $browser->visit('/admin/events')
                ->waitForText($title)
                ->assertSee($title);
        });
    }

    public function test_add_participant_via_relation_manager(): void
    {
        $title = 'DuskRelMgr-' . uniqid();
        $this->cleanupEventTitles[] = $title;
        $email = 'dusk-part-' . uniqid() . '@test.ch';
        $this->cleanupMemberEmails[] = $email;

        $event = Event::create([
            'title' => $title,
            'starts_at' => now()->addWeek(),
            'statuscode' => 'P',
            'price' => 0,
        ]);

        $member = Member::create([
            'first_name' => 'DuskParticipante',
            'last_name' => 'RelTest',
            'email' => $email,
            'statuscode' => 'A',
        ]);

        $this->browse(function (Browser $browser) use ($event, $member) {
            $this->loginAsAdmin($browser);

            // View page shows the Participantes relation manager
            $browser->visit('/admin/events/' . $event->id)
                ->waitForText('Participantes')
                ->assertSee('Participantes');

            // Manually attach via DB, then verify display
            $event->members()->attach($member->id, ['status' => 'N']);

            $browser->visit('/admin/events/' . $event->id)
                ->waitForText('DuskParticipante')
                ->assertSee('DuskParticipante')
                ->assertSee('RelTest');
        });
    }
}
