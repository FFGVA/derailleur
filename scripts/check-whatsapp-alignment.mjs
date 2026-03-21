import { chromium } from 'playwright';

const BASE = 'http://derailleur.local';

(async () => {
    const browser = await chromium.launch({ headless: true });
    const context = await browser.newContext({ viewport: { width: 1280, height: 900 } });
    const page = await context.newPage();

    // Login
    await page.goto(`${BASE}/admin/login`);
    await page.waitForLoadState('networkidle');
    const emailInput = page.locator('input').first();
    await emailInput.waitFor({ state: 'visible' });
    await emailInput.fill('admin@ffgva.ch');
    await page.locator('input[autocomplete="current-password"]').fill('password');
    await page.locator('button:has-text("Connexion")').click();
    await page.waitForURL('**/admin', { timeout: 15000 });
    console.log('Logged in');

    // Go to member 1 edit page
    await page.goto(`${BASE}/admin/members/1/edit`);
    await page.waitForLoadState('networkidle');
    await page.waitForTimeout(1500);

    // Expand collapsed repeater items if any
    const collapseButtons = await page.locator('button[x-on\\:click*="collapse"]').all();
    for (const btn of collapseButtons) {
        try { await btn.click(); } catch {}
    }
    await page.waitForTimeout(500);

    // Screenshot before measuring
    await page.screenshot({ path: '/tmp/whatsapp-alignment.png', fullPage: true });

    // Find phone inputs and toggles
    const phoneInputs = await page.locator('input[type="tel"]').all();
    console.log(`Phone inputs found: ${phoneInputs.length}`);

    // For each phone input, find the WhatsApp toggle in the same row
    for (let i = 0; i < phoneInputs.length; i++) {
        const phoneBox = await phoneInputs[i].boundingBox();
        if (!phoneBox) { console.log(`Phone ${i+1}: no bounding box`); continue; }

        // Find all toggles and pick the one closest vertically
        const allToggles = await page.locator('button[role="switch"]').all();
        let best = null;
        let bestDist = Infinity;

        for (const t of allToggles) {
            const tb = await t.boundingBox();
            if (!tb) continue;
            const d = Math.abs((phoneBox.y + phoneBox.height/2) - (tb.y + tb.height/2));
            if (d < bestDist) { bestDist = d; best = tb; }
        }

        if (!best) { console.log(`Phone ${i+1}: no toggle found`); continue; }

        const phoneMid = phoneBox.y + phoneBox.height / 2;
        const toggleMid = best.y + best.height / 2;
        const diff = Math.abs(phoneMid - toggleMid);

        console.log(`\nPhone ${i+1}:`);
        console.log(`  Input  center Y: ${phoneMid.toFixed(1)}px`);
        console.log(`  Toggle center Y: ${toggleMid.toFixed(1)}px`);
        console.log(`  Offset: ${diff.toFixed(1)}px → ${diff < 5 ? 'ALIGNED ✓' : 'MISALIGNED ✗'}`);
    }

    console.log('\nScreenshot: /tmp/whatsapp-alignment.png');
    await browser.close();
})();
