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
 * Visual regression tests for portal views.
 *
 * Takes screenshots at mobile viewport (375x812) for each portal page.
 * Baseline screenshots are stored in tests/Browser/screenshots/baseline/.
 * Current screenshots are stored in tests/Browser/screenshots/current/.
 *
 * To update baselines: delete the baseline folder and run the tests.
 */
class PortalVisualTest extends DuskTestCase
{
    private const VIEWPORT_WIDTH = 375;
    private const VIEWPORT_HEIGHT = 812;
    private const BASELINE_DIR = 'tests/Browser/screenshots/baseline';
    private const CURRENT_DIR = 'tests/Browser/screenshots/current';

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

    /**
     * Log into the portal via magic link for the given member.
     */
    protected function loginPortal(Browser $browser, Member $member): Browser
    {
        [$model, $rawToken] = MemberMagicToken::generateFor($member);

        $browser->visit("/auth/verify/{$rawToken}")
            ->pause(1500);

        return $browser;
    }

    /**
     * Get the test member (active, with events).
     */
    protected function getTestMember(): Member
    {
        return Member::findOrFail(35); // Livia Wagner — active, has events
    }

    /**
     * Take a screenshot and compare with baseline.
     * If no baseline exists, creates one and passes.
     */
    protected function assertVisualMatch(Browser $browser, string $name): void
    {
        $baseDir = base_path(self::BASELINE_DIR);
        $currentDir = base_path(self::CURRENT_DIR);

        if (! is_dir($baseDir)) {
            mkdir($baseDir, 0755, true);
        }
        if (! is_dir($currentDir)) {
            mkdir($currentDir, 0755, true);
        }

        $baselinePath = "{$baseDir}/{$name}.png";
        $currentPath = "{$currentDir}/{$name}.png";

        // Take current screenshot
        $browser->screenshot("current/{$name}");

        // Copy from Dusk's screenshot location to our current dir
        $duskScreenshotPath = base_path("tests/Browser/screenshots/current/{$name}.png");
        if (file_exists($duskScreenshotPath) && realpath($duskScreenshotPath) !== realpath($currentPath)) {
            copy($duskScreenshotPath, $currentPath);
        }

        if (! file_exists($baselinePath)) {
            // No baseline — create one and pass
            copy($currentPath, $baselinePath);
            $this->assertTrue(true, "Baseline created for '{$name}'.");
            return;
        }

        // Pixel-by-pixel comparison using GD
        $baseline = imagecreatefrompng($baselinePath);
        $current = imagecreatefrompng($currentPath);

        $bw = imagesx($baseline);
        $bh = imagesy($baseline);
        $cw = imagesx($current);
        $ch = imagesy($current);

        // Compare common area (pages may scroll differently)
        $w = min($bw, $cw);
        $h = min($bh, $ch);
        $totalPixels = $w * $h;
        $diffPixels = 0;

        for ($x = 0; $x < $w; $x++) {
            for ($y = 0; $y < $h; $y++) {
                $bc = imagecolorat($baseline, $x, $y);
                $cc = imagecolorat($current, $x, $y);

                // Allow small color differences (anti-aliasing)
                $br = ($bc >> 16) & 0xFF; $bg = ($bc >> 8) & 0xFF; $bb = $bc & 0xFF;
                $cr = ($cc >> 16) & 0xFF; $cg = ($cc >> 8) & 0xFF; $cb = $cc & 0xFF;

                if (abs($br - $cr) > 10 || abs($bg - $cg) > 10 || abs($bb - $cb) > 10) {
                    $diffPixels++;
                }
            }
        }

        imagedestroy($baseline);
        imagedestroy($current);

        $diffPercent = ($totalPixels > 0) ? ($diffPixels / $totalPixels) * 100 : 0;

        // Allow up to 5% pixel difference (dynamic content like dates, QR codes)
        $this->assertLessThan(
            5.0,
            $diffPercent,
            "Visual regression detected for '{$name}': {$diffPercent}% pixels differ ({$diffPixels}/{$totalPixels})"
        );
    }

    // ── Portal page tests ──

    public function test_dashboard(): void
    {
        $this->browse(function (Browser $browser) {
            $member = $this->getTestMember();
            $this->loginPortal($browser, $member);

            $browser->visit('/portail')
                ->pause(1500);

            $this->assertVisualMatch($browser, 'portal-dashboard');
        });
    }

    public function test_adhesion(): void
    {
        $this->browse(function (Browser $browser) {
            $member = $this->getTestMember();
            $this->loginPortal($browser, $member);

            $browser->visit('/portail/adhesion')
                ->pause(1500);

            $this->assertVisualMatch($browser, 'portal-adhesion');
        });
    }

    public function test_carte(): void
    {
        $this->browse(function (Browser $browser) {
            $member = $this->getTestMember();
            $this->loginPortal($browser, $member);

            $browser->visit('/portail/carte')
                ->pause(2000); // QR code needs time to render

            $this->assertVisualMatch($browser, 'portal-carte');
        });
    }

    public function test_factures(): void
    {
        $this->browse(function (Browser $browser) {
            $member = $this->getTestMember();
            $this->loginPortal($browser, $member);

            $browser->visit('/portail/factures')
                ->pause(1500);

            $this->assertVisualMatch($browser, 'portal-factures');
        });
    }

    public function test_evenement(): void
    {
        $this->browse(function (Browser $browser) {
            $member = $this->getTestMember();
            $this->loginPortal($browser, $member);

            $browser->visit('/portail/evenement/1')
                ->pause(1500);

            $this->assertVisualMatch($browser, 'portal-evenement');
        });
    }

    public function test_protection_des_donnees(): void
    {
        $this->browse(function (Browser $browser) {
            $member = $this->getTestMember();
            $this->loginPortal($browser, $member);

            $browser->visit('/portail/protection-des-donnees')
                ->pause(1500);

            $this->assertVisualMatch($browser, 'portal-protection-des-donnees');
        });
    }
}
