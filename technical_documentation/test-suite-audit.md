# Test Suite Audit — Dérailleur

**Date:** 20.04.2026
**Auditor perspective:** test designer / test strategist
**Scope:** tests/ directory, phpunit.xml, Dusk setup, factories, seeders, data-dependency analysis
**Strategic question:** *Do the tests run meaningfully, independently of any pre-existing database state, on both an empty schema and a production copy?*

---

## 1. Executive Summary

The non-Dusk suite (67 classes, ~503 methods across `tests/Feature` and `tests/Unit`) is **structurally sound** and **largely portable**: every test creates its own data, uses `DatabaseTransactions` for rollback, uses `uniqid()` to avoid email collisions, and fakes mail consistently. It will run cleanly against an empty schema.

However, four strategic problems limit confidence:

1. **Dusk tests are not portable.** They require a specific seeded admin user (`admin@ffgva.ch` / `password`), use manual tearDown cleanup instead of transactions, and will fail on an empty DB. On a production copy they may operate on — or collide with — live data.
2. **Brand strings are hardcoded in test assertions.** Ten-plus tests literal-match `ffgva_`, `FFGVA`, `Fast and Female Geneva`, `derailleur.ffgva.ch`. The currently-running `config/association.php` refactor will break all of them simultaneously.
3. **Model factories are almost entirely missing.** Only `UserFactory` exists. 190+ direct `Model::create([...])` calls duplicate fixture setup across 48 files. Adding a required column means editing ~190 call sites.
4. **Widget and "guard" tests assert surface, not behavior.** `assertSee('expir')`, `assertSuccessful()`, and `count === 1` after creating exactly one record do not verify the logic their names claim to test. These are placebo tests — they pass without proving anything.

The suite is **good at isolation, weak at leverage**. It doesn't produce false positives on the dev DB today, but it will not survive the config refactor without rework, and several tests provide no actual safety net.

**Grade breakdown:**

| Dimension | Grade | Rationale |
|---|---|---|
| Data independence (Feature/Unit) | A− | Uniqid everywhere, scoped queries, no first()/latest() without creation |
| Data independence (Browser/Dusk) | D | Hardcoded admin, manual cleanup, shared dev DB |
| Isolation & state | A− | DatabaseTransactions + Mail::fake consistent; no Http::fake needed |
| Assertion quality | C+ | Descriptive names, but ~20% of tests assert surface-level artifacts |
| Maintainability | C | No factories for domain models, 10× duplicated `makeAdmin()`, brand hardcodes |
| Coverage | — | Unconfigured. No measurement, no CI enforcement |
| Portability | C | Passes on dev DB, fails in Dusk on empty DB, will break on config refactor |

---

## 2. Evidence Collected

- **67 test classes / ~503 test methods / ~9,200 lines** across Unit (28), Feature (31), Browser (8)
- **phpunit.xml** — test DB = `agiletra_ffgva` (same as dev), no `<coverage>` block, no CI config
- **tests/TestCase.php** — 10 lines, empty body, no shared helpers
- **tests/DuskTestCase.php** — 134 lines, manual cleanup arrays, hardcoded admin login
- **database/factories/UserFactory.php** — the only factory; Member/Event/Invoice factories absent
- **database/seeders/** — DatabaseSeeder, MemberSeeder (24 fixtures), EventSeeder (5 events), InvoiceSeeder (16 invoices)
- **Seeder usage in tests** — `$this->seed(...)` appears **zero times**
- **Hardcoded production IDs** (`User::find(1)`, `Member::find(1)`) — **zero occurrences**
- **`uniqid()` in test data** — 139 occurrences
- **`Mail::fake()`** — 89+ occurrences
- **`Http::fake()`** — zero (no external HTTP dependencies)
- **`RefreshDatabase` / `DatabaseMigrations`** — zero; only `DatabaseTransactions`
- **Skipped or incomplete tests** — zero
- **External URLs in assertions** — only `derailleur.local` (Dusk vhost) and `https://ffgva.ch` (CORS header check)
- **Test count discrepancy** — CLAUDE.md says 440; actual is ~503. Either recent work added tests without updating CLAUDE.md, or the 440 figure was approximate.

---

## 3. Portability Matrix

The core question: does the suite run meaningfully on (a) an empty schema, (b) a production copy, (c) the current dev DB?

| Suite | Empty schema | Production copy | Dev DB (current) |
|---|---|---|---|
| `tests/Unit/*` | ✅ Passes | ✅ Passes (transactions roll back) | ✅ Passes |
| `tests/Feature/Api/*` | ✅ Passes | ✅ Passes | ✅ Passes |
| `tests/Feature/Auth/*` | ✅ Passes | ✅ Passes | ✅ Passes |
| `tests/Feature/Policies/*` | ✅ Passes | ✅ Passes | ✅ Passes |
| `tests/Feature/Filament/*` | ⚠️ Passes, but some assertions are placebos (see §5.4) | ⚠️ Same, plus risk of false-negative assertDontSee if prod event/member titles overlap | ✅ Passes |
| `tests/Feature/Middleware/*` | ✅ Passes | ✅ Passes | ✅ Passes |
| `tests/Feature/ICalFeedTest.php` | ⚠️ Hardcoded `FFGVA` in PRODID assertion will fail post-config-refactor | Same | ✅ Passes today |
| `tests/Feature/Adhesion*Test.php` | ✅ Passes | ✅ Passes | ✅ Passes |
| `tests/Feature/Portal*Test.php` | ✅ Passes | ✅ Passes | ✅ Passes |
| `tests/Unit/Mail/*Test.php` | ❌ **Fails** post-config-refactor — asserts `'Fast and Female Geneva'` subject literal | Same | ✅ Passes today |
| `tests/Unit/Services/InvoiceServiceTest.php`, `InvoicePdfContentTest.php`, `MemberCardServiceTest.php` | ❌ **Fails** post-config-refactor — asserts `'ffgva_…'` filename literal | Same | ✅ Passes today |
| `tests/Browser/*` (Dusk) | ❌ **Fails** — admin@ffgva.ch not seeded, no login possible | ⚠️ Runs if prod admin password is `password`, but cleanup may delete or miss real records | ✅ Passes today |

### Key implications

- On an **empty schema**, 10 of 67 classes break: 2 mail unit tests, 3 service unit tests, 1 iCal feature test, all 8 Dusk tests.
- On a **production copy**, the non-Dusk suite runs cleanly (transactions) but Dusk tests operate on live data outside any transaction.
- The **brand-hardcode failures and Dusk failures are independent**. Fix each separately.

---

## 4. Strengths (Preserve)

1. **Consistent transaction-based isolation** on 47/47 non-Dusk test files. No mixing of `DatabaseTransactions` / `RefreshDatabase`.
2. **No hardcoded IDs** (`->find(1)`) anywhere. No reliance on autoincrement values.
3. **No reliance on seeded data** — seeders are never invoked from tests (`$this->seed(...)` = 0 occurrences).
4. **Mail faking is uniform** — every mail-sending test calls `Mail::fake()` before acting.
5. **Email uniqueness via `uniqid()`** — 139 occurrences, rules out cross-test collision.
6. **Scoped count assertions** — every `->count()` assertion filters by IDs of records created in the same test.
7. **Tests don't commit DDL or break transactions** — rollback semantics hold.
8. **Descriptive test names** — `test_confirmation_creates_invoice_marked_sent`, `test_widget_hides_past_event`. Good signal-to-noise for failure output.
9. **No external service coupling** — no `Http::fake()` needed because no real HTTP is called.
10. **French assertions are consistent** — aligned with CLAUDE.md's no-i18n decision.

---

## 5. Critical Issues

### 5.1 Dusk suite is not portable

Files: `tests/Browser/*` (8 files, ~1,013 lines), `tests/DuskTestCase.php`.

Problem:

- `loginAsAdmin()` at `DuskTestCase.php:81` types a hardcoded `admin@ffgva.ch` / `password` into the login form. These credentials must exist in the DB or every Dusk test fails.
- Dusk tests do **not** use `DatabaseTransactions` (transactions and Selenium sessions are mutually exclusive — transactions live in the PHP process, browser requests run in Apache). Cleanup is manual via `$this->cleanupMemberEmails[]`, `$this->cleanupEventTitles[]`, `$this->cleanupUserEmails[]` arrays in `tearDown()`.
- If a test crashes before appending to a cleanup array, the record is orphaned.
- On a production copy, Dusk cleanup operates by `Member::where('email', $email)->forceDelete()` — safe as long as emails don't collide with real members, but the force-delete also removes invoices and pivot rows by `member_id`, which is destructive on shared data.

Impact:

- On empty DB: Dusk suite is 0% green.
- On production copy: runs only if prod admin's password literally is `password` (hopefully not). If it is, the suite mutates production data.
- On dev DB: works because seeded admin matches.

### 5.2 Brand strings are hardcoded in assertions

Impact window: **imminent**. The currently-running `config/association.php` work will make these fail.

Affected files and literals:

| File | Line | Literal |
|---|---|---|
| `tests/Unit/Services/InvoiceServiceTest.php` | 45 | `'ffgva_Dupont_Marie-facture-'` |
| `tests/Unit/Services/InvoiceServiceTest.php` | 81 | `'ffgva_De_La_Tour_Marie_Claire-facture-'` |
| `tests/Unit/Services/InvoicePdfContentTest.php` | 124 | `'ffgva_Dupont_Marie-facture-'` |
| `tests/Unit/Services/MemberCardServiceTest.php` | 50 | `'FFGVA - Membre Julie Bernard.pdf'` |
| `tests/Unit/Mail/ActivationMailTest.php` | 29 | `'Bienvenue chez Fast and Female Geneva !'` subject |
| `tests/Unit/Mail/ActivationMailTest.php` | 54 | `'derailleur.ffgva.ch/login'` link |
| `tests/Unit/Mail/AdhesionWelcomeMailTest.php` | 26, 33 | same brand/domain |
| `tests/Unit/Mail/AdhesionConfirmationMailTest.php` | 30 | `'Confirmation de ton inscription - FFGVA'` |
| `tests/Feature/ICalFeedTest.php` | 112 | `'PRODID:-//FFGVA//Derailleur//FR'` |

Every one of these will go red the moment the config refactor lands.

### 5.3 Missing domain-model factories

Only `UserFactory` exists. No `MemberFactory`, `EventFactory`, `InvoiceFactory`, `InvoiceLineFactory`, `MemberPhoneFactory`, `EventMemberFactory`.

Consequence: 190+ direct `Model::create([...])` calls across 48 test files. Each carries its own notion of "minimum valid member" or "minimum valid event", drifting over time. Example duplication: the `makeAdmin()` helper is identical across 10 Filament test files (copy-paste), and `makeMember()` / `makeExpiringMember()` vary subtly across service tests.

Impact when a required column is added (e.g. a new NOT NULL field on `members`): all 190 sites must be updated. No central fixture definition means test bitrot.

### 5.4 Placebo tests — assertions that prove nothing

Examples:

- **`tests/Feature/Filament/ExpiringMembershipsTest.php:39`** — `->assertSee('expir')`. Matches "expirer", "expiration", "expiré", any French conjugation. Passes even if the widget fails to count the just-created member, as long as the word appears anywhere on the page (likely in a header/label).
- **`tests/Feature/Filament/ExpiringMembershipsTest.php:71`** — test is named `test_widget_excludes_far_future` but only asserts `assertSuccessful()`. Never verifies the far-future member is actually excluded.
- **`tests/Feature/Filament/DashboardWidgetsTest.php:47`** — creates 3 invoices with statuses N/E/P, then asserts `assertSee('Montants ouverts')` and `assertSee('CHF')`. The label text is static HTML. Zero proof the widget computed any total.
- **`tests/Feature/Filament/CotisationsPageTest.php:115`** — test `test_send_invoice_does_not_duplicate` creates one invoice and asserts count === 1. Never triggers the duplicate-prevention guard, so the guard logic is untested.
- **`tests/Feature/Filament/UpcomingEventsWidgetTest.php:71`** — asserts `'Sortie Sans Fin'` is visible, but the test description implies also verifying events without end date *still* show up — which is what assertSee covers, so this one is OK; included here only because it's borderline.

These tests give a false signal of coverage. They pass during refactors even if logic breaks.

### 5.5 Feature/Filament widget tests risk false negatives on production copy

`UpcomingEventsWidgetTest::test_widget_hides_past_event` creates an event titled `'Sortie Passée'` and asserts `assertDontSee('Sortie Passée')`. On a production copy with any event whose title contains that substring, the assertion false-fails. Unique-uniqid titles would eliminate the risk — current code uses a static string.

Same class of issue: `UpcomingEventsWidgetTest::test_widget_hides_cancelled_events` with `'Sortie Annulée'`.

Low probability today; worth fixing because it costs nothing.

---

## 6. Medium Issues

### 6.1 Hardcoded invoice number strings in fixtures

`tests/Feature/Filament/DashboardWidgetsTest.php:40-42` uses `'T-001'`, `'T-002'`, `'T-003'`. The `invoices.invoice_number` column has a unique constraint. On dev DB these only collide if a real invoice uses that string (unlikely), but on a production copy the risk is the same and `DatabaseTransactions` will happily roll back — the *first* test's write won't collide with itself, but two concurrent runs or a crashed transaction would.

Fix: use `'T-' . uniqid()` or delegate to `Invoice::generateNumber()`.

### 6.2 10× duplicated `makeAdmin()` helper

Identical code in 10 Filament test files. Belongs in a shared trait or the base `TestCase`:

```php
trait CreatesAdmin {
    protected function makeAdmin(string $role = 'A'): User { … }
}
```

### 6.3 No time freezing

Tests use `now()`, `now()->addDay()`, `now()->subMonths(2)` throughout. Good enough for most assertions, but any test reasoning about *membership expiry dates*, *invoice dates*, or *event scheduling* is subtly non-deterministic across midnight boundaries. `Carbon::setTestNow()` is never called.

Risk: low but real for expiry-edge tests around `ExpiringMemberships` at DST transitions.

### 6.4 No coverage measurement

`phpunit.xml` has no `<coverage>` block. There's no way to answer "which code paths are untested." Given that some tests are placebos (§5.4), coverage would flag the gap even where a test nominally exists.

Recommend adding coverage with a threshold, even locally — don't need CI to use it.

### 6.5 Test DB = dev DB

`DB_DATABASE=agiletra_ffgva` in `phpunit.xml`. Tests rely entirely on transactional rollback. If a test ever triggers DDL (e.g. via a migration run inside a test, or a MariaDB trigger that DDLs), the rollback is incomplete and dev data is polluted. No such tests exist today, but the design is fragile.

Recommend `agiletra_ffgva_test` as a separate schema. Trivial to add, catches a whole class of future foot-guns.

### 6.6 Triggers are untested

CLAUDE.md: "Audit via MariaDB BEFORE UPDATE/DELETE triggers on all domain tables." No test verifies the audit tables actually receive a row on update/delete. A trigger could silently fail to install on a fresh DB and no test would notice.

### 6.7 No CI

No `.github/workflows/`, no Gitea/Jenkins config. Tests run when a developer remembers. Regressions land if the reflex fails.

Given the strict TDD workflow in CLAUDE.md, this is a governance gap — the workflow depends on tests staying green, but nothing automates the check.

---

## 7. Minor Issues

- **Test count drift**: CLAUDE.md says 440, reality is ~503. Update CLAUDE.md (or stop stating a count — let the suite speak for itself).
- **`tests/Unit/ExampleTest.php`** — Laravel's default scaffolded test; delete if unused, keep only if it's asserting something real.
- **Faker locale** — `APP_FAKER_LOCALE=fr_CH` is set but the only factory using Faker is `UserFactory` which uses `fake()->name()` / `safeEmail()`. Low impact until more factories exist.
- **`tests/Browser/Pages/HomePage.php`, `Page.php`** — Dusk page objects exist but are sparsely used. Either commit to the page-object pattern or remove them.
- **iCal test imports production-style `PRODID`** — overlapping with §5.2.

---

## 8. Recommendations

Ordered by impact-per-hour, highest first.

### P0 — Do before the config refactor lands

**R1. Replace brand hardcodes in test assertions.** For each of the 9 literals in §5.2, replace with either `config('association.*')` calls or structural assertions (`assertStringStartsWith` on a config-driven prefix, regex match on a filename pattern). This prevents the entire `config/association.php` rollout from turning the suite red.

*Effort:* 2-3 hours, ~20 edits.

### P1 — Fix portability

**R2. Make Dusk tests self-seed the admin.** Add a `setUp()` in `DuskTestCase` that checks for the admin user and creates it if missing (with `firstOrCreate`). Or: generate a per-test admin with `uniqid()` email, log in with that, clean up in `tearDown`. Eliminates the hard dependency on dev-seeded data.

*Effort:* 1-2 hours. Also makes the Dusk suite runnable in CI against a fresh DB.

**R3. Switch test DB to a dedicated schema.** Create `agiletra_ffgva_test`, add `DB_DATABASE=agiletra_ffgva_test` to `phpunit.xml`. Migrate schema once via `php artisan migrate --database=mariadb_test --path=database/migrations` (or run `create_database.sql` directly against it). No app change required.

*Effort:* 1 hour.

### P2 — Build leverage

**R4. Introduce domain factories.** Create `MemberFactory`, `EventFactory`, `InvoiceFactory`, `InvoiceLineFactory`, `MemberPhoneFactory`, `EventMemberFactory`, `EventChefFactory`. Define minimum-valid defaults + states (`->active()`, `->expiring()`, `->paid()`, `->unpaid()`, `->cancelled()`). Replace 190+ inline `Model::create([...])` calls over time — no need to do all at once; existing tests stay green, new tests use factories.

*Effort:* 1-2 days for factories + first tranche of migrations. Ongoing.

**R5. Consolidate helpers into a trait.** Move `makeAdmin()` to a `tests/Support/CreatesAdmin.php` trait; delete the 10 copies.

*Effort:* 30 minutes.

### P3 — Tighten assertion quality

**R6. Rewrite placebo tests (§5.4).** For each widget test, assert the actual computed output (count, total, specific member name). For `test_send_invoice_does_not_duplicate`, actually trigger the guarded action twice and assert no duplicate is created.

*Effort:* 4-6 hours for the ~10 identified placebos.

**R7. Unique-ify widget fixture titles.** Replace `'Sortie Passée'` / `'Sortie Annulée'` with `'Past-' . uniqid()` / `'Cancelled-' . uniqid()` — closes §5.5.

*Effort:* 30 minutes.

**R8. Add audit-trigger smoke tests.** One test per domain table that updates a row and verifies an `audit_*` row appeared. Catches trigger install failures on fresh DBs.

*Effort:* 2-3 hours.

### P4 — Governance

**R9. Enable coverage with a threshold.** Add `<coverage>` block to `phpunit.xml` with `<include>app</include>`. Run `php artisan test --coverage --min=75` locally. Drives the placebo tests to the surface (low coverage with high test count = many tests aren't exercising code).

*Effort:* 30 minutes setup; ongoing to maintain threshold.

**R10. Add CI.** GitHub Actions workflow that provisions MariaDB 10.11 (production parity), runs migrations, runs `php artisan test`, runs Dusk against a headless chromium. Blocks PRs on red. This is the governance backstop that enforces CLAUDE.md's TDD policy.

*Effort:* 1 day initial, lifetime payoff.

### P5 — Nice to have

- **R11.** Delete `tests/Unit/ExampleTest.php` if it's the Laravel default.
- **R12.** Freeze time with `Carbon::setTestNow()` in time-sensitive tests.
- **R13.** Commit to Dusk page objects or remove them.
- **R14.** Update CLAUDE.md's test count or remove the number.

---

## 9. Action Plan

### Phase A — Immediate (before the config refactor PR merges) — 1 day

- R1 (brand hardcodes) — **must land before or with the config refactor**

### Phase B — Portability (1-2 days)

- R2 (self-seeding Dusk admin)
- R3 (separate test schema)
- R5 (consolidate makeAdmin helper)
- R7 (unique widget titles)

### Phase C — Quality (3-5 days)

- R4 (domain factories — first tranche covering Member, Event, Invoice, InvoiceLine)
- R6 (rewrite placebo tests)
- R8 (audit-trigger smoke tests)

### Phase D — Governance (1-2 days)

- R9 (coverage enabled with threshold)
- R10 (CI workflow)

### Phase E — Polish (half day)

- R11, R13, R14 cleanups
- R12 time freezing where needed

Total if all executed: **~2 weeks**. If only Phase A + B: **~3 days**, and that's enough to unblock the config refactor and make Dusk portable.

---

## 10. What "Meaningful" Means for This Suite

A meaningful test, for this codebase, should:

1. **Identify failure quickly and precisely.** Descriptive name, one logical assertion per test, narrow scope. The current suite does this ~80% of the time; §5.4 identifies the 20% that don't.
2. **Survive reasonable refactors.** If the rule is "no i18n, French strings hardcoded", that's a conscious trade-off — but the *brand* strings (§5.2) violate the goal of package reuse stated in the architecture audit, and must be configurable.
3. **Prove a behavior, not a render.** Asserting a label is present proves the page rendered. Asserting a computed total is correct proves the feature works.
4. **Run without human-shaped fixture data.** The suite achieves this everywhere *except* Dusk.

The suite is roughly 70% of the way to those four bars. The 30% gap is knowable, bounded, and fixable in ~2 weeks of focused work — with Phase A being the urgent piece that blocks the in-flight config work.

---

*Report generated by strategic test-suite audit of `/opt/ffgva` test directory.*
*Coverage: 67 test classes, ~503 methods, ~9,200 lines of test code, phpunit config, Dusk setup, factories, seeders.*
