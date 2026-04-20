# Architecture Audit Report — Dérailleur

**Date:** 20.04.2026
**Scope:** Full codebase analysis for quality, modularity, adaptability
**Strategic goal:** Assess reusability as a package for other association deployments
**Frameworks applied:** TOGAF, SOLID principles, Clean Code
**Constraint:** No i18n layer (per CLAUDE.md) — French-only UI is by design, not a defect

---

## 1. Executive Summary

The codebase is functional, well-tested (475 tests, 1171 assertions), and has undergone significant refactoring. The main architectural issues blocking reusability are:

1. **87 hardcoded brand color references** (`#80081C`) across blade views and CSS — `config('association.colors')` exists but is unused in views
2. **4 deprecated delegates** in InvoiceService that should be removed once callers are migrated
3. **Business logic in Filament classes** — Excel export (60 lines), email resending, payment processing inline in modal actions
4. **Policies exist but are never invoked** — 8 manual `abort(403)` calls instead of `$this->authorize()`
5. **2 enum bugs** — `DashboardController.php:18` missing `->value`, `UpcomingEvents.php:22` uses raw string `'X'`

| Metric | Current | Target |
|---|---|---|
| Rebranding effort | ~8 hours (87 color refs, 10 CHF refs, org name in 2 mail subjects) | ~30 min (edit config/association.php + CSS variables + logo) |
| Files to touch for rebrand | ~25 | 3 (config, CSS, logo asset) |
| Test coverage | 475 tests, 1171 assertions | Maintained |

---

## 2. SOLID Violations

### 2.1 Single Responsibility Principle (SRP)

**InvoiceService still holds creation + deprecated delegates (142 lines)**

`app/Services/InvoiceService.php` retains 4 deprecated facade methods that delegate to other services:
- `generatePdf()` → `InvoicePdfService::generate()` (line 115)
- `generateQrCodeBase64()` → `QrBillService::generateQrCodeBase64()` (line 121)
- `onCotisationPaid()` → `InvoicePaymentService::onCotisationPaid()` (line 127)
- `computeMembershipEnd()` → `InvoicePaymentService::computeMembershipEnd()` (line 133)

**Recommendation:** Remove deprecated delegates once all test files are migrated to call the target services directly. Currently 22 test references still use `InvoiceService::` for these methods.

**MembersRelationManager contains Excel export logic (60 lines)**

`app/Filament/Resources/EventResource/RelationManagers/MembersRelationManager.php:154-211` — full OpenSpout writer with headers, data formatting, file management.

**Recommendation:** Extract to `ExcelExportService::exportParticipants(Event $event)`.

### 2.2 Open/Closed Principle (OCP)

Status transitions are handled via direct `->update(['statuscode' => ...])` calls scattered across services and controllers. Adding a new status requires editing multiple files.

**Locations of direct status writes:**
- `app/Services/AdhesionService.php:34,102` — P, N
- `app/Services/InvoicePaymentService.php:37` — A
- `app/Services/EventRegistrationService.php:33-34` — N, C
- `app/Services/InvoiceEmailService.php:44` — E (invoice)

**Assessment:** For the current scope (6 member statuses, stable), this is acceptable. A state machine would add complexity without clear benefit unless the status set grows. Noted but not recommended for immediate action.

### 2.3 Liskov Substitution Principle (LSP)

No violations. Models don't use inheritance beyond Eloquent. Services are concrete static classes.

### 2.4 Interface Segregation Principle (ISP)

No interfaces defined. All services are concrete static classes. For package reuse, key extension points would benefit from interfaces:
- `InvoiceGeneratorInterface` — custom invoice formats
- `MemberCardGeneratorInterface` — custom card designs

**Assessment:** Not blocking current operations. Required when extracting to package.

### 2.5 Dependency Inversion Principle (DIP)

All 11 services use exclusively static methods — impossible to inject alternatives via constructor.

**Assessment:** Static services are appropriate for the current single-deployment model. Converting to injectable would require changing every caller. Defer to package extraction phase.

---

## 3. DRY Violations

### 3.1 Deprecated Delegates in InvoiceService (4 locations)

```php
// app/Services/InvoiceService.php:115-136
/** @deprecated Use InvoicePdfService::generate() */
public static function generatePdf(Invoice $invoice): array { return InvoicePdfService::generate($invoice); }
/** @deprecated Use QrBillService::generateQrCodeBase64() */
public static function generateQrCodeBase64(Invoice $invoice): ?string { ... }
/** @deprecated Use InvoicePaymentService::onCotisationPaid() */
public static function onCotisationPaid(Invoice $invoice): void { ... }
/** @deprecated Use InvoicePaymentService::computeMembershipEnd() */
public static function computeMembershipEnd(\DateTimeInterface $periodStart): \Carbon\Carbon { ... }
```

**Test files still using deprecated paths (22 references):**
- `tests/Unit/Services/InvoiceServiceTest.php` — 5 calls to `generatePdf()`
- `tests/Unit/Services/InvoicePdfContentTest.php` — 7 calls via `InvoiceService::` prefix
- `tests/Unit/Models/InvoiceTest.php` — 3 calls to `onCotisationPaid()`
- `tests/Feature/MembershipRequestFlowTest.php` — 3 calls to `onCotisationPaid()`
- `tests/Feature/Filament/CotisationsPageTest.php` — 2 calls to `computeMembershipEnd()`

**Fix:** Update test imports to target services, then remove deprecated methods.

### 3.2 Phone Create/Update Pattern (3 locations)

```php
$phone = $member->phones()->first();
if ($phone) { $phone->update(['phone_number' => ...]); }
else { MemberPhone::create([...]); }
```

1. `app/Services/AdhesionService.php:54-63`
2. `app/Http/Controllers/EventRegistrationController.php:147-153`
3. `app/Http/Controllers/Portal/AdhesionController.php:60-69` (adhesionUpdate)

**Fix:** Add `Member::setPhone(string $number, string $label)` method.

### 3.3 Email Resend Pattern in Filament (2 locations)

Invoice PDF regeneration + QR + Mail::send pattern appears in:
1. `app/Filament/Resources/EventResource/RelationManagers/MembersRelationManager.php:265-274`
2. `app/Filament/Resources/InvoiceResource/Pages/ViewInvoice.php:140-154`

**Fix:** Use `InvoiceEmailService::sendExisting()` which already encapsulates this.

---

## 4. Hardcoded Values — Rebranding Blockers

### 4.1 Brand Color #80081C

| Location type | Count | Files |
|---|---|---|
| Email blade views | 30 | layout, adhesion, invoice, event-confirmation, event-reminder, event-registration-new, member-update-request, activation, adhesion-confirmation |
| Portal blade views | 38 | layout, dashboard, adhesion-inscription, evenement, peloton-event, carte, adhesion, factures |
| portal.css (x2) | 8 | resources/css/portal.css (4), public/css/portal.css (4) |
| admin.css | 0 | Uses CSS variables ✓ |
| PHP services | 0 | Uses `config('association.colors.pdf_brand_rgb')` ✓ |
| **Total** | **87** | |

**Config key exists but unused in views:** `config('association.colors.primary')` = `'#80081C'`

### 4.2 Background Color #f5f1e9

| Location type | Count |
|---|---|
| Email views | 6 |
| Portal layout | 1 |
| Portal views | 10 |
| **Total** | **17** |

### 4.3 Organization Name

| Value | Count | Location |
|---|---|---|
| `'Fast and Female Geneva'` in config | 4 | `config/association.php:20,30,32,36` |
| Hardcoded in mail subjects | 2 | `ActivationMail.php:22`, `AdhesionWelcomeMail.php:21` |
| In email layout (config-driven) | 2 | `layout.blade.php:6,16` ✓ |
| In portal layout title | 1 | `portail/layout.blade.php:7` (hardcoded) |

### 4.4 Currency "CHF"

| Location | Count |
|---|---|
| `app/Services/InvoicePdfService.php` | 3 (lines 118, 131, 138) |
| `app/Filament/Resources/InvoiceResource.php` | 2 (lines 65, 67) |
| `app/Services/InvoiceService.php` | 0 |
| Blade views | ~15 |
| **Total PHP** | **10** |

`config('association.currency')` exists but is only used in `QrBillService.php:39`.

### 4.5 Email Addresses

All centralized in `config/association.php` and accessed via `BaseMailable` helpers. Zero hardcoded email addresses in `app/Mail/` or controllers. ✓

---

## 5. Architectural Patterns Assessment

### What Works Well

| Pattern | Assessment |
|---|---|
| **BaseMailable** | All 13 mail classes extend it; from/reply-to centralized via config |
| **SetsModifiedBy trait** | Applied consistently on all 9 domain models |
| **Soft deletes** | All 9 domain models; manual pivot scoping where needed |
| **Audit triggers** | MariaDB BEFORE UPDATE/DELETE on all domain tables |
| **Enum getLabel()/getColor()** | All 8 enums implement both methods consistently |
| **Service extraction** | InvoiceEmailService, EventRegistrationService, AdhesionService, InvoicePaymentService eliminate prior duplication |
| **Portal controller split** | 6 focused controllers (44-198 lines each) vs prior 596-line monolith |
| **PaymentDateForm** | Reusable form schema replaces 3 identical modal definitions |
| **ICalService::formatEvent()** | Shared VEVENT builder eliminates duplication |
| **config/association.php** | Single file for identity/branding (18 keys) |
| **Test suite** | 475 tests, DatabaseTransactions, no seed-data dependencies |
| **Visual regression tests** | Dusk-based mobile screenshots with pixel comparison |

### What Needs Improvement

| Pattern | Issue | Impact |
|---|---|---|
| **Brand colors in views** | 87 occurrences of `#80081C` in blade/CSS | Rebrand requires editing 25+ files |
| **Unused config keys** | `colors.primary`, `currency` defined but not used in views | Config exists but doesn't deliver value |
| **Policies unused** | EventPolicy, MemberPolicy exist but 0 `$this->authorize()` calls | Authorization logic duplicated as `abort(403)` |
| **Deprecated delegates** | 4 methods in InvoiceService delegate to other services | Dead code, confusing call paths |
| **Static-only services** | All 11 services use static methods | Blocks DI, mocking, extension |
| **Inline validation** | 7 controllers do `$request->validate()` vs 3 form requests | Inconsistent pattern |
| **portal.css duplication** | Identical file in `resources/css/` and `public/css/` | No build pipeline, manual sync |

---

## 6. Model Layer Analysis

### 6.1 Consistency

| Model | CREATED_AT | Casts | SetsModifiedBy | SoftDeletes |
|---|---|---|---|---|
| Member | `const = null` ✓ | method ✓ | ✓ | ✓ |
| Event | `const = null` ✓ | method ✓ | ✓ | ✓ |
| Invoice | `const = null` ✓ | method ✓ | ✓ | ✓ |
| InvoiceLine | `const = null` ✓ | method ✓ | ✓ | ✓ |
| MemberPhone | `const = null` ✓ | method ✓ | ✓ | ✓ |
| EventChef | `const = null` ✓ | **none** ✗ | ✓ | ✓ |
| EventMember | `const = null` ✓ | method ✓ | ✓ | ✓ |
| MemberStrava | `const = null` ✓ | method ✓ | ✓ | ✓ |
| User | default | method ✓ | — | — |
| MemberMagicToken | `$timestamps = false` | method ✓ | — | — |
| PortalAuditLog | `$timestamps = false` | method ✓ | — | — |

**Issues:**
- EventChef missing casts (no cast defined at all)
- MemberMagicToken/PortalAuditLog use `$timestamps = false` (correct — tables have `created_at` only, no `updated_at`)

### 6.2 Enum Usage — Raw String Comparisons

**Bugs found (2):**
1. `app/Http/Controllers/Portal/DashboardController.php:18` — `->where('statuscode', EventStatus::Publie)` missing `->value` — compares enum object to string column
2. `app/Filament/Widgets/UpcomingEvents.php:22` — `->where('statuscode', '!=', 'X')` — raw string instead of `EventStatus::Annule->value`

**Constants using raw strings (intentional, used in whereIn):**
- `Member::PORTAL_ACCESSIBLE_STATUSES = ['A', 'P', 'N', 'E']` (Member.php:38)
- `Member::ACTIVE_STATUSES = ['A', 'E']` (Member.php:41)

**getRawOriginal() calls:** 29 total across app/ — these are necessary for Eloquent casting bypass but could be reduced with query scopes.

---

## 7. Service Layer Analysis

### 7.1 Service Inventory

| Service | Lines | Methods | Responsibility |
|---|---|---|---|
| InvoiceService | 142 | 9 (4 deprecated) | Invoice creation + deprecated delegates |
| InvoicePdfService | 169 | 2 | PDF generation with FPDF |
| InvoiceEmailService | 63 | 3 | Create invoice + send email |
| InvoicePaymentService | 62 | 2 | Payment processing + membership |
| QrBillService | 75 | 2 | Swiss QR bill generation |
| EventRegistrationService | 52 | 1 | Event registration workflow |
| AdhesionService | 114 | 3 | Adhesion workflow |
| ICalService | 92 | 3 | iCal generation |
| PhoneFormatter | 148 | 1 | Phone number formatting |
| MemberCardService | 95 | 2 | Membership card PDF |
| PortalAudit | 21 | 1 | Audit logging |
| **Total** | **1,033** | **29** | |

### 7.2 Missing Service Extractions

| Logic | Currently in | Recommended |
|---|---|---|
| Excel export (60 lines) | MembersRelationManager.php:154-211 | ExcelExportService |
| Email resend (inline) | MembersRelationManager.php:265-274 | Use InvoiceEmailService::sendExisting() |
| Strava OAuth (40 lines) | StravaController.php:40-80 | StravaService |
| Phone create/update | 3 controllers/services | Member::setPhone() |

### 7.3 Deprecated Delegate Anti-Pattern

InvoiceService has 4 `@deprecated` methods that exist solely for backward compatibility with tests. These add 20 lines of indirection and confuse the dependency graph.

---

## 8. View Layer Analysis

### 8.1 Portal CSS — Remaining Inline Styles

| View | CSS in @section('styles') | Already in portal.css |
|---|---|---|
| peloton-event.blade.php | ~150 lines | ~10 lines |
| adhesion-inscription.blade.php | ~120 lines | ~15 lines |
| evenement.blade.php | ~100 lines | ~20 lines |
| adhesion-edit.blade.php | ~80 lines | 0 |
| dashboard.blade.php | ~60 lines | ~40 lines |
| login.blade.php | ~45 lines | 0 |
| adhesion.blade.php | ~40 lines | ~10 lines |
| carte.blade.php | ~30 lines | 0 |
| factures.blade.php | ~25 lines | ~10 lines |
| inscription-event-nouveau.blade.php | ~60 lines | 0 |
| **Total remaining inline** | **~710 lines** | |
| **portal.css** | **222 lines** | |

### 8.2 Email Inline Colors

Email templates require inline styles (email client compatibility), but colors should reference config:

```blade
{{-- Current (hardcoded): --}}
style="background-color: #80081C;"

{{-- Target (config-driven): --}}
style="background-color: {{ config('association.colors.primary') }};"
```

**Occurrences:** 30 inline `#80081C` + 6 inline `#f5f1e9` across 11 email templates.

---

## 9. Rebranding Effort Matrix

### Current State

| Task | Files | Effort |
|---|---|---|
| Change brand color (#80081C) | 25+ blade/CSS files (87 occurrences) | 4 hours |
| Change org name | 2 mail subjects + portal title + config | 30 min |
| Change email addresses | config/association.php only | 5 min |
| Change logo | 1 asset file | 5 min |
| Change IBAN/address | config/association.php only | 5 min |
| Change currency (CHF) | 10 PHP + 15 blade references | 2 hours |
| Change creditor/invoice PDF | config-driven ✓ | 0 |
| **Total** | | **~7 hours** |

### Target State (after CSS variables + email config)

| Task | Changes | Effort |
|---|---|---|
| Change brand color | 1 CSS file (variables) + config/association.php | 10 min |
| Change org name | config/association.php | 1 min |
| Change email addresses | config/association.php | 1 min |
| Change logo | 1 asset file | 5 min |
| Change IBAN/address | config/association.php | 1 min |
| Change currency | config/association.php (if views use config) | 5 min |
| **Total** | | **~25 min** |

---

## 10. Recommended Refactoring Roadmap

### Phase 3: Brand Colors in Views (1-2 days)
1. Define CSS custom properties in portal layout: `--color-primary`, `--color-primary-hover`, `--color-bg`
2. Replace 87 hardcoded `#80081C` references in blade/CSS with variables
3. Replace 17 `#f5f1e9` references with variable
4. Email templates: use `{{ config('association.colors.primary') }}` for inline styles
5. Remove duplicate `public/css/portal.css` — use build output or symlink

### Phase 4: Cleanup (1 day)
1. Fix 2 enum bugs (DashboardController.php:18, UpcomingEvents.php:22)
2. Remove 4 deprecated delegates from InvoiceService (update 22 test references)
3. Replace 8 manual `abort(403)` with `$this->authorize()` using existing policies
4. Extract phone create/update to `Member::setPhone()`
5. Wire `config('association.currency')` to remaining 10 hardcoded `'CHF'` in PHP
6. Use `config('association.name')` in 2 mail subjects + portal title

### Phase 5: Service Completeness (1 day)
1. Extract Excel export from MembersRelationManager to ExcelExportService
2. Replace inline email resend in MembersRelationManager with `InvoiceEmailService::sendExisting()`
3. Convert 7 inline validations to form requests (or consolidate)

### Phase 6: Package Extraction (3-5 days, when needed)
1. Define interfaces for extension points (InvoicePdfGenerator, MemberCardGenerator)
2. Convert static services to injectable (register in ServiceProvider)
3. Extract core domain to `packages/association-manager`
4. Keep FFGVA-specific views, config, assets in main app

---

## 11. Strengths to Preserve

1. **475-test suite** with DatabaseTransactions (no RefreshDatabase, no seed-data dependency)
2. **Visual regression tests** — Dusk mobile screenshots with pixel comparison
3. **BaseMailable pattern** — all 13 mail classes extend it, from/reply-to centralized
4. **config/association.php** — single file for identity/branding (18 keys)
5. **SetsModifiedBy trait** — consistent across all 9 domain models
6. **MariaDB audit triggers** — BEFORE UPDATE/DELETE on all domain tables
7. **No cascade deletes** — enforced by design
8. **Enum consistency** — all 8 enums have getLabel() + getColor()
9. **Service separation** — 11 focused services (avg 94 lines each)
10. **Portal controller split** — 6 controllers, none over 200 lines
11. **PaymentDateForm** — reusable form schema pattern
12. **Swiss QR-bill integration** — clean separation in QrBillService

---

*Analysis covers: 11 models, 11 services, 8 enums, 13 controllers, 14 mail classes, 25 Filament resources/pages/widgets, 47 blade views, 2 policies, 3 form requests, 2 middleware, 67 tests (475 test cases, 1171 assertions). Generated 20.04.2026.*
