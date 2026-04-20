# Architecture Audit Report — Dérailleur

**Date:** 20.04.2026
**Scope:** Full codebase analysis for quality, modularity, adaptability
**Strategic goal:** Create a reusable package for other associations
**Frameworks applied:** TOGAF, SOLID principles, Clean Code

---

## Executive Summary

The codebase is functional and well-tested (440 tests, 1063 assertions) but has significant architectural debt that blocks reusability. The main issues are:

1. **Business logic scattered across controllers and Filament classes** — violates Single Responsibility Principle
2. **Invoice/email workflow duplicated in 6 locations** — violates DRY
3. **Branding hardcoded in 39+ files** — blocks rebranding
4. **300+ lines of duplicated CSS in blade views** — no centralized styling
5. **All services use static methods** — blocks dependency injection and testability via mocking

**Rebranding effort today:** 3-4 weeks of manual find/replace across 50+ files.
**Target after refactoring:** Change config + 1 CSS file + logo.

---

## 1. SOLID Violations

### 1.1 Single Responsibility Principle (SRP)

#### InvoiceService — God Service (372 lines)
`app/Services/InvoiceService.php` handles 5 distinct responsibilities:

| Responsibility | Methods | Lines |
|---|---|---|
| Invoice creation | `createCotisation()`, `createEvent()`, `createAutre()` | 25-102 |
| PDF generation | `generatePdf()` | 108-249 |
| QR code generation | `generateQrCodeBase64()`, `buildQrBill()` | 262-318 |
| Business rules | `computeMembershipEnd()` | 324-334 |
| Payment processing | `onCotisationPaid()` | 339-366 |

**Recommendation:** Split into:
- `InvoiceCreationService` — creation workflows
- `InvoicePdfService` — PDF generation (141 lines of FPDF code)
- `InvoicePaymentService` — payment processing, membership activation
- `QrBillService` — QR code generation

#### PortalController — Fat Controller (640+ lines)
`app/Http/Controllers/PortalController.php` handles:
- Dashboard, adhesion, carte, factures, événements
- Event registration (inscrire, annuler)
- Peloton management (5 methods with authorization)
- Card validation, QR URL generation

**Recommendation:** Split into:
- `PortalDashboardController`
- `PortalAdhesionController`
- `PortalEventController`
- `PortalPelotonController`
- `PortalCarteController`

### 1.2 Open/Closed Principle (OCP)

**Violation:** Status transitions are hardcoded in controllers and services with `if/elseif` chains.

Example — `FormController.php:55-89`:
```php
if (! $member) { /* create P */ }
elseif ($member->getRawOriginal('statuscode') === 'P') { /* update */ }
// then:
if ($member->getRawOriginal('statuscode') === 'N') { /* direct invoice */ }
else { /* activation email */ }
```

**Recommendation:** Implement a `MemberStatusMachine` or strategy pattern. New statuses should not require modifying existing code.

### 1.3 Liskov Substitution Principle (LSP)

**No violations found.** Models and services don't use inheritance in ways that break substitutability.

### 1.4 Interface Segregation Principle (ISP)

**Violation:** No interfaces defined anywhere. All services are concrete static classes.

For package reusability, key services need interfaces:
- `InvoiceGeneratorInterface` — allows associations to customize invoice format
- `MemberCardGeneratorInterface` — different card designs
- `NotificationServiceInterface` — different email templates/channels

### 1.5 Dependency Inversion Principle (DIP)

**Violation:** All services use static methods — impossible to inject alternatives.

```php
// Current (tight coupling):
InvoiceService::createCotisation($member, $year);

// Target (injectable):
$this->invoiceService->createCotisation($member, $year);
```

**Impact:** An association cannot replace the PDF generator, QR bill provider, or email sender without modifying the service class directly.

---

## 2. DRY Violations — Code Duplication

### 2.1 Invoice Creation + Email Workflow (6 locations)

The same 8-line pattern is copy-pasted in 6 places:

```php
$result = InvoiceService::create*($member, ...);
$invoice = Invoice::where('invoice_number', $result['invoice_number'])->first();
$qrBase64 = InvoiceService::generateQrCodeBase64($invoice);
Mail::send(new InvoiceMail($invoice, $result['pdf'], $result['filename'], $qrBase64, ...));
$invoice->update(['statuscode' => 'E']);
```

**Locations:**
1. `AdhesionActivationController.php:60-72`
2. `EventRegistrationController.php:193-200`
3. `PortalController.php:192-202`
4. `PortalController.php:386-393`
5. `PortalController.php:599-606`
6. `FormController.php:102-111`
7. `MembersRelationManager.php:229-244` (Filament)
8. `Cotisations.php:150-184` (Filament)
9. `MemberResource.php:102-122` (Filament)

**Fix:** Create `InvoiceEmailService::createAndSend(Member $member, string $type, ...)`.

### 2.2 Event Registration Flow (3 locations)

Event member creation + price check + invoice/confirmation:
1. `EventRegistrationController.php:168-204`
2. `PortalController.php:359-405`
3. `PortalController.php:569-613`

**Fix:** Create `EventRegistrationService::register(Member, Event)`.

### 2.3 Phone Create/Update (3 locations)

```php
$phone = $member->phones()->first();
if ($phone) { $phone->update([...]); }
else { MemberPhone::create([...]); }
```

1. `FormController.php:82-91`
2. `PortalController.php:177-186`
3. `EventRegistrationController.php:150-156`

**Fix:** Add `Member::setPhone(string $number, string $label)` method or service.

### 2.4 Payment Date Modal (2 locations)

Identical modal with date regex validation:
1. `Cotisations.php:197-215`
2. `InvoiceResource.php:260-278`

**Fix:** Extract to `PaymentDateForm::schema()` or a reusable Action class.

### 2.5 iCal Event Formatting (2 locations in same file)

`ICalService.php` — `generate()` and `generateFeed()` duplicate event-to-ical conversion.

**Fix:** Extract `private static function formatEvent(Event $event): array`.

### 2.6 Active Status Array (5 locations)

`['A', 'P', 'N', 'E']` repeated in:
1. `PortalAuth.php:32`
2. `PortalAuthController.php:29`
3. `PortalAuthController.php:59`
4. `EventRegistrationController.php:38`
5. `Api/EventRegistrationController.php:40`

**Fix:** Define `Member::PORTAL_ACCESSIBLE_STATUSES` constant.

---

## 3. Hardcoded Values — Rebranding Blockers

### 3.1 Email Addresses

| Value | Occurrences | Location |
|---|---|---|
| `'noreply@ffgva.ch'` | 11 mail classes | From address in every Mailable |
| `'Fast and Female Geneva - Ne pas répondre'` | 11 mail classes | From name |
| `'fastandfemalegva@etik.com'` | 8 mail classes | Reply-to, hardcoded |

**Fix:** Create `BaseMailable` with from/reply-to from config:
```php
abstract class BaseMailable extends Mailable {
    protected function baseEnvelope(): array {
        return [
            'from' => new Address(config('mail.from.address'), config('mail.from.name')),
            'replyTo' => [new Address(config('ffgva.contact_email'))],
        ];
    }
}
```

### 3.2 Brand Colors

| Color | Hex | Usage | Files |
|---|---|---|---|
| Primary burgundy | `#80081C` | Buttons, headers, links, badges | 25+ blade views, admin.css |
| Hover burgundy | `#660616` | Button hover states | 5+ views |
| Beige background | `#f5f1e9` | Email/portal backgrounds | 10+ views |
| PDF brand color | `RGB(128,8,28)` | Invoice PDF | InvoiceService.php (3 locations) |

**Fix:** Define CSS variables in layout + config for PDF:
```css
:root {
    --color-primary: #80081C;
    --color-primary-hover: #660616;
    --color-bg: #f5f1e9;
}
```

### 3.3 Organization Data

| Data | Hardcoded | Config | Configurable via env |
|---|---|---|---|
| Organization name | Views, emails | `config/ffgva.php` | No (hardcoded fallback) |
| Website URL | `emails/layout.blade.php` | No | No |
| Contact email | 4 views | `config/ffgva.php` | No (hardcoded) |
| IBAN | — | `config/ffgva.php` | Yes |
| Creditor address | — | `config/ffgva.php` | Yes |
| Logo path | `asset('images/logo-ffgva.png')` | No | No |
| Currency (CHF) | 50+ inline references | No | No |
| Date format (d.m.Y) | 30+ inline references | No | No |

### 3.4 French Strings

All UI text, email content, validation messages, and notifications are hardcoded in French across:
- 13 email blade views
- 8 portal blade views
- 12 Filament resource/page files
- 5 controllers

**Assessment:** The CLAUDE.md explicitly states "No i18n / no localization layer". For package reuse, a minimal translation layer (even just config-based string replacement) would be needed.

---

## 4. Architectural Patterns — Assessment

### 4.1 What Works Well

| Pattern | Assessment |
|---|---|
| **Enum-based status codes** | Clean, type-safe, consistent `getLabel()`/`getColor()` methods |
| **SetsModifiedBy trait** | Applied consistently across all 9 domain models |
| **Soft deletes** | Applied consistently on all domain models |
| **Audit triggers** | MariaDB BEFORE UPDATE/DELETE triggers on all domain tables |
| **Config for business values** | IBAN, cotisation amount, creditor address in `config/ffgva.php` |
| **Test coverage** | 440 tests with DatabaseTransactions, no reliance on seed data |
| **Route organization** | Clean separation of public, portal, admin, API routes |
| **Domain model** | Clear entities (Member, Event, Invoice) with proper relationships |

### 4.2 What Needs Improvement

| Pattern | Issue | Impact |
|---|---|---|
| **Static services** | No DI, no interfaces, untestable via mocking | Blocks extension |
| **Controller-as-orchestrator** | Business logic in controllers instead of services | Blocks reuse |
| **Filament-as-business-logic** | Modal actions contain workflows | Tight coupling to Filament |
| **Inline CSS** | 300+ lines in blade `@section('styles')` blocks | Unmaintainable |
| **No base Mailable** | 11 classes repeat from/reply-to | Branding scattered |
| **Mixed timestamp patterns** | Some models use `const CREATED_AT = null`, others `$timestamps = false` | Inconsistency |
| **Mixed cast styles** | Some use `protected $casts` property, others `casts()` method | Inconsistency |

---

## 5. Model Layer Analysis

### 5.1 Consistency Issues

| Pattern | Models using it | Models not using it | Issue |
|---|---|---|---|
| `const CREATED_AT = null` | Member, Event, Invoice, InvoiceLine, MemberPhone, EventChef, EventMember, MemberStrava | MemberMagicToken, PortalAuditLog | MMT/PAL use `$timestamps = false` |
| `protected function casts(): array` | Member, Event, Invoice, InvoiceLine, MemberPhone, EventChef, EventMember | MemberMagicToken, PortalAuditLog | MMT/PAL use `protected $casts` property |
| `SetsModifiedBy` trait | All 9 domain models | User, MemberMagicToken, PortalAuditLog | Correct (non-domain) |
| `SoftDeletes` trait | All 9 domain models | User, MemberMagicToken, PortalAuditLog | Correct |

### 5.2 Enum Usage Inconsistency

**Problem:** Models cast statuscode to enums, but services/controllers compare with raw strings.

```php
// Model casts (correct):
'statuscode' => MemberStatus::class,

// Service comparison (wrong — uses raw string):
if ($invoice->getRawOriginal('type') !== 'C') { return; }  // InvoiceService.php:341

// Controller comparison (wrong — uses raw string):
$member->getRawOriginal('statuscode') === 'P'  // FormController.php:74

// Filament comparison (wrong — uses raw string):
->whereIn('event_member.status', ['N', 'C'])  // MembersRelationManager.php:142
```

**Count:** 40+ raw string comparisons that should use enum values.

### 5.3 Missing Enum: InvoiceType.getColor()

`InvoiceType` and `UserRole` are missing `getColor()` methods, breaking pattern consistency with other enums.

---

## 6. Service Layer Analysis

### 6.1 Missing Services (business logic in wrong layer)

| Missing Service | Logic currently in | Methods needed |
|---|---|---|
| `AdhesionService` | FormController, AdhesionActivationController, PortalController | `submitAdhesion()`, `confirmEmail()`, `processPayment()` |
| `EventRegistrationService` | EventRegistrationController, PortalController, MembersRelationManager | `register()`, `cancel()`, `resendEmail()` |
| `InvoiceEmailService` | 9 locations (see 2.1) | `createAndSend()` |
| `MemberPhoneService` | 3 controllers | `createOrUpdate()` |

### 6.2 InvoiceService Return Value Anti-Pattern

All `create*()` methods return `['pdf' => ..., 'filename' => ..., 'invoice_number' => ...]`. Callers then query the invoice by number:

```php
$result = InvoiceService::createCotisation($member, $year);
$invoice = Invoice::where('invoice_number', $result['invoice_number'])->first();
```

**Should return the Invoice model directly** — the caller needs the model, not the number.

---

## 7. View Layer Analysis

### 7.1 Portal CSS Duplication

Estimated 300+ lines of CSS duplicated across portal blade views in `@section('styles')` blocks:

| View | CSS lines | Duplicated classes |
|---|---|---|
| `dashboard.blade.php` | ~117 | `.portal-nav-btn`, badge styles |
| `adhesion.blade.php` | ~99 | Card styles, button styles |
| `peloton.blade.php` | ~56 | Badge styles, card styles |
| `carte.blade.php` | ~61 | Card styles, button styles |
| `evenement.blade.php` | ~50 | Badge styles, card styles |
| `factures.blade.php` | ~40 | Table styles, badge styles |
| `peloton-member.blade.php` | ~50 | Badge styles, info card |
| `peloton-event.blade.php` | ~50 | Badge styles, participant list |
| `protection-des-donnees.blade.php` | ~45 | Card styles |

**Fix:** Extract to `resources/css/portal.css`, compile via Vite, include in `portail/layout.blade.php`.

### 7.2 Email Inline Styles

Email views necessarily use inline styles (email client compatibility), but colors and spacing are hardcoded. For rebranding, a preprocessor or config-driven approach would help:

```php
// In layout.blade.php, use config:
style="background-color: {{ config('ffgva.theme.primary') }};"
```

---

## 8. Rebranding Effort Matrix

### Current State (manual effort)

| Task | Files to change | Estimated effort |
|---|---|---|
| Change brand color | 39 files | 4 hours |
| Change organization name | 15 files | 2 hours |
| Change email addresses | 11 mail classes + config | 1 hour |
| Change logo | 1 asset + 15 references | 1 hour |
| Change IBAN/address | config + PDF template | 30 min |
| Change currency | 50+ references | 4 hours |
| Change date format | 30+ references | 3 hours |
| Change email text (French) | 13 email views | 4 hours |
| Change portal text | 8 portal views | 3 hours |
| **Total** | | **~22 hours** |

### Target State (after refactoring)

| Task | Changes needed | Estimated effort |
|---|---|---|
| Change brand color | 1 CSS file + config | 5 min |
| Change organization name | .env | 1 min |
| Change email addresses | .env | 1 min |
| Change logo | 1 asset file | 5 min |
| Change IBAN/address | .env | 1 min |
| Change currency | config | 5 min |
| Change date format | config | 5 min |
| Change email/portal text | Override view files | 2 hours |
| **Total** | | **~2.5 hours** |

---

## 9. Recommended Refactoring Roadmap

### Phase 1: Extract Services (2-3 days)
1. Create `InvoiceEmailService::createAndSend()` — eliminate 9-location duplication
2. Create `EventRegistrationService::register()` — eliminate 3-location duplication
3. Create `AdhesionService` — centralize adhesion workflow
4. Move `onCotisationPaid()` to `InvoicePaymentService`
5. Split `InvoiceService` into creation, PDF, QR services
6. Return Invoice model from creation methods instead of array

### Phase 2: Centralize Configuration (1-2 days)
1. Create `BaseMailable` with from/reply-to from config
2. Move all email addresses to config with env fallbacks
3. Add theme config: colors, logo path, currency, date format, website URL
4. Define `Member::PORTAL_ACCESSIBLE_STATUSES` constant
5. Replace raw status string comparisons with enum references

### Phase 3: Centralize Styling (1-2 days)
1. Extract portal CSS to `resources/css/portal.css`
2. Define CSS custom properties for brand colors
3. Use config values in email blade templates for colors
4. Move inline SVG icon handling to proper Filament components

### Phase 4: Interfaces & DI (2-3 days)
1. Define interfaces: `InvoiceGeneratorInterface`, `MemberCardGeneratorInterface`
2. Convert static services to injectable classes
3. Register in service provider with default implementations
4. Allow override via service container binding

### Phase 5: Package Structure (3-5 days)
1. Extract core domain (models, services, enums) to `packages/association-manager`
2. Keep FFGVA-specific views, branding, config in main app
3. Define extension points: custom PDF templates, email templates, card designs
4. Document configuration and customization guide

---

## 10. Strengths to Preserve

1. **Comprehensive test suite** — 440 tests, all using DatabaseTransactions (no RefreshDatabase)
2. **Clean enum implementation** — type-safe status codes with labels and colors
3. **Audit trail** — MariaDB triggers on all domain tables
4. **Consistent trait usage** — SetsModifiedBy on all domain models
5. **Proper soft deletes** — with manual pivot table soft-delete scoping
6. **No cascade deletes** — enforced by design
7. **Separation of portal from admin** — distinct auth, middleware, views
8. **Swiss QR-bill integration** — well-implemented via sprain/swiss-qr-bill
9. **Configuration for business values** — IBAN, cotisation amount, creditor data in config

---

*Report generated by architecture analysis of /opt/ffgva codebase.*
*Analysis covers: 11 models, 5 services, 8 enums, 10 controllers, 13 mail classes, 12 Filament resources/pages/widgets, 25+ blade views, 440 tests.*
