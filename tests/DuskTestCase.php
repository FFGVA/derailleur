<?php

namespace Tests;

use App\Models\Event;
use App\Models\Invoice;
use App\Models\Member;
use App\Models\User;
use Facebook\WebDriver\Chrome\ChromeOptions;
use Facebook\WebDriver\Remote\DesiredCapabilities;
use Facebook\WebDriver\Remote\RemoteWebDriver;
use Illuminate\Support\Collection;
use Laravel\Dusk\TestCase as BaseTestCase;
use PHPUnit\Framework\Attributes\BeforeClass;

abstract class DuskTestCase extends BaseTestCase
{
    /**
     * Track created records for cleanup.
     */
    protected array $cleanupMemberEmails = [];
    protected array $cleanupEventTitles = [];
    protected array $cleanupUserEmails = [];

    /**
     * Prepare for Dusk test execution.
     */
    #[BeforeClass]
    public static function prepare(): void
    {
        if (! static::runningInSail()) {
            static::startChromeDriver(['--port=9515']);
        }
    }

    /**
     * Base URL for Dusk tests — uses Apache vhost.
     */
    protected function baseUrl(): string
    {
        return 'http://derailleur.local';
    }

    /**
     * Create the RemoteWebDriver instance.
     */
    protected function driver(): RemoteWebDriver
    {
        $options = (new ChromeOptions)->addArguments(collect([
            '--window-size=1920,1080',
            '--disable-search-engine-choice-screen',
            '--disable-smooth-scrolling',
            '--no-sandbox',
        ])->unless($this->hasHeadlessDisabled(), function (Collection $items) {
            return $items->merge([
                '--disable-gpu',
                '--headless=new',
            ]);
        })->all());

        return RemoteWebDriver::create(
            $_ENV['DUSK_DRIVER_URL'] ?? env('DUSK_DRIVER_URL') ?? 'http://localhost:9515',
            DesiredCapabilities::chrome()->setCapability(
                ChromeOptions::CAPABILITY, $options
            )
        );
    }

    /**
     * Login as admin user via Filament login page.
     * Handles already-logged-in state gracefully.
     */
    protected function loginAsAdmin(\Laravel\Dusk\Browser $browser): \Laravel\Dusk\Browser
    {
        // Always visit login — if already authenticated, Filament redirects to /admin
        $browser->visit('/admin/login');
        $browser->pause(1500);

        // Check if we're still on the login page (not redirected)
        if ($browser->element('#data\\.email')) {
            $browser->type('#data\\.email', 'admin@ffgva.ch')
                ->type('#data\\.password', 'password')
                ->press('Connexion')
                ->pause(3000);
        }

        return $browser;
    }

    /**
     * Clean up test data after each test.
     */
    protected function tearDown(): void
    {
        foreach ($this->cleanupMemberEmails as $email) {
            $member = Member::where('email', $email)->first();
            if ($member) {
                $member->phones()->forceDelete();
                // Remove event_member pivot entries
                \Illuminate\Support\Facades\DB::table('event_member')
                    ->where('member_id', $member->id)->delete();
                // Remove invoices and lines
                $invoices = Invoice::where('member_id', $member->id)->get();
                foreach ($invoices as $invoice) {
                    $invoice->lines()->forceDelete();
                    \Illuminate\Support\Facades\DB::table('invoice_event')
                        ->where('invoice_id', $invoice->id)->delete();
                    if ($invoice->pdf_filename) {
                        \Illuminate\Support\Facades\Storage::delete('invoices/' . $invoice->pdf_filename);
                    }
                    $invoice->forceDelete();
                }
                $member->forceDelete();
            }
        }

        foreach ($this->cleanupEventTitles as $title) {
            $event = Event::where('title', $title)->first();
            if ($event) {
                \Illuminate\Support\Facades\DB::table('event_member')
                    ->where('event_id', $event->id)->delete();
                \Illuminate\Support\Facades\DB::table('event_chef')
                    ->where('event_id', $event->id)->delete();
                $event->forceDelete();
            }
        }

        foreach ($this->cleanupUserEmails as $email) {
            User::where('email', $email)->forceDelete();
        }

        parent::tearDown();
    }
}
