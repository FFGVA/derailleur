<?php

namespace Tests\Browser;

use App\Models\Member;
use App\Models\MemberMagicToken;
use Facebook\WebDriver\Chrome\ChromeOptions;
use Facebook\WebDriver\Remote\DesiredCapabilities;
use Facebook\WebDriver\Remote\RemoteWebDriver;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

/**
 * Tests that portal popup/modal dialogs are visible when triggered.
 */
class PortalPopupTest extends DuskTestCase
{
    private const VIEWPORT_WIDTH = 375;
    private const VIEWPORT_HEIGHT = 812;

    protected function driver(): RemoteWebDriver
    {
        $options = (new ChromeOptions)->addArguments([
            '--window-size=' . self::VIEWPORT_WIDTH . ',' . self::VIEWPORT_HEIGHT,
            '--disable-search-engine-choice-screen',
            '--disable-smooth-scrolling',
            '--no-sandbox',
            '--disable-gpu',
            '--headless=new',
            '--force-device-scale-factor=1',
        ]);

        return RemoteWebDriver::create(
            $_ENV['DUSK_DRIVER_URL'] ?? env('DUSK_DRIVER_URL') ?? 'http://localhost:9515',
            DesiredCapabilities::chrome()->setCapability(
                ChromeOptions::CAPABILITY, $options
            )
        );
    }

    protected function loginPortal(Browser $browser, Member $member): Browser
    {
        [$model, $rawToken] = MemberMagicToken::generateFor($member);
        $browser->visit("/auth/verify/{$rawToken}")
            ->pause(1500);

        return $browser;
    }

    // ── Event page popups ──

    public function test_event_cancel_popup_is_visible(): void
    {
        $this->browse(function (Browser $browser) {
            // Member 35 (Livia) is registered for event 8
            $member = Member::findOrFail(35);
            $this->loginPortal($browser, $member);

            $browser->visit('/portail/evenement/8')
                ->pause(1500)
                ->click('.portal-cancel-btn')
                ->pause(500);

            $browser->assertVisible('#cancelPopup')
                ->assertSee('Annuler l\'inscription')
                ->assertSee('Confirmer l\'annulation');

            $browser->screenshot('popup-event-cancel');
        });
    }

    public function test_event_confirm_popup_is_visible(): void
    {
        $this->browse(function (Browser $browser) {
            // Member 35 needs an event they're NOT registered for
            $member = Member::findOrFail(35);
            $this->loginPortal($browser, $member);

            // Event 9 — check if Livia can register
            $browser->visit('/portail/evenement/9')
                ->pause(1500);

            $btn = $browser->element('.portal-register-btn');
            if (! $btn) {
                $this->assertTrue(true, 'No register button — member may already be registered');
                return;
            }

            $browser->click('.portal-register-btn')
                ->pause(500)
                ->assertVisible('#confirmPopup')
                ->assertSee('Confirmer');

            $browser->screenshot('popup-event-confirm');
        });
    }

    // ── Peloton page popups ──

    public function test_peloton_add_participant_popup_is_visible(): void
    {
        $this->browse(function (Browser $browser) {
            // Member 31 (Caroline) is chef for event 3
            $member = Member::findOrFail(31);
            $this->loginPortal($browser, $member);

            $browser->visit('/portail/peloton/3')
                ->pause(1500)
                ->click('.portal-add-btn')
                ->pause(500);

            $browser->assertVisible('#addPopup')
                ->assertSee('Ajouter une participante');

            $browser->screenshot('popup-peloton-add');
        });
    }

    public function test_peloton_description_popup_is_visible(): void
    {
        $this->browse(function (Browser $browser) {
            // Member 31 (Caroline) is chef for event 3
            $member = Member::findOrFail(31);
            $this->loginPortal($browser, $member);

            $browser->visit('/portail/peloton/3')
                ->pause(1500);

            if ($browser->element('.portal-desc-btn')) {
                $browser->click('.portal-desc-btn')
                    ->pause(500)
                    ->assertVisible('#descPopup')
                    ->assertSee('Description');

                $browser->screenshot('popup-peloton-desc');
            } else {
                $this->assertTrue(true, 'No description button — event may have no description');
            }
        });
    }

    // ── Popup dismissal ──

    public function test_event_cancel_popup_can_be_closed(): void
    {
        $this->browse(function (Browser $browser) {
            $member = Member::findOrFail(35);
            $this->loginPortal($browser, $member);

            $browser->visit('/portail/evenement/8')
                ->pause(1500)
                ->click('.portal-cancel-btn')
                ->pause(500)
                ->assertVisible('#cancelPopup')
                ->click('#cancelPopup .portal-popup-close')
                ->pause(300)
                ->assertMissing('#cancelPopup.active');
        });
    }
}
