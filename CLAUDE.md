# Dérailleur — CLAUDE.md

## Project
- Club membership management app for Fast and Female Geneva (cycling club)
- Production: derailleur.ffgva.ch (Hostpoint, PHP 8.4, MariaDB 10.11)
- Local dev: PHP 8.4 (Debian), MariaDB 11.8, Apache vhost derailleur.local
- SQL must be compatible with MariaDB 10.11 (production) — avoid 11.x-only features
- GitHub: FFGVA/derailleur

## Stack
- Laravel 12 + Filament v3
- MariaDB (database: agiletra_ffgva, user: laravel, password: 0hbPQ2kDB2fa1QCw)
- sprain/swiss-qr-bill + fpdf/fpdf (native PDF invoices with QR payment slip)
- Mailpit on 192.168.178.41:1025 (local dev), production SMTP sendmail via .env
- Phone formatting: PhoneFormatter service (PHP) + sg-phone.js/sg-phone-rules.js (client-side)

## Conventions
- Language: French only — ALL UI, emails, PDFs, validation messages. Tutoiement in member-facing emails.
- Mobile-first responsive design
- No i18n / no localization layer
- CHAR(1) statuscode fields in DB — enumerations only in PHP code (app/Enums/)
- No cascade deletes — EVER. Always check dependencies before deleting.
- Soft-delete on all domain tables
- Domain tables have only `updated_at`, no `created_at`
- Audit via MariaDB BEFORE UPDATE/DELETE triggers on all domain tables
- Colors/styling in CSS (resources/css/filament/admin.css), not inline on pages
- Branding: ffgva.ch colors — primary burgundy #80081C, beige #f5f1e9
- Decimal point (not comma) — Swiss convention
- Date format: dd.mm.yyyy (dots, not slashes) — Swiss convention. Applies everywhere: table columns (`->date('d.m.Y')`), DatePicker/DateTimePicker (`->displayFormat('d.m.Y')`), PDFs, emails, text inputs
- No cookies on homepage (static index.html)
- noindex/nofollow on all pages, robots.txt denies all crawlers

## UX patterns
- **Read-only view first**: Members, Events, Invoices have dedicated view pages (Infolists, not disabled forms). Click row → view page. "Modifier" button to enter edit mode.
- **No input boxes on view pages**: Use Filament Infolists for clean text display with icons.
- **View pages have separate forms**: Do not share form() between view and edit — too complex. Each has its own layout.
- **Top navigation**: No sidebar. Navigation in top bar. User menu has quick links.
- **Delete button**: In form actions bar next to save/cancel, or in-form Actions component. Always check dependencies first — show error notification if blocked.
- **Phone numbers**: Table layout in member list (phone | WhatsApp icon columns aligned). tel: links and wa.me/ WhatsApp links.
- **Status badges clickable**: In event participant list, clicking the status badge opens a modal to change it. Presence is a 3-state toggle (✓/✗/—).
- **Compact forms**: Use 4-12 column grids for proportional field sizing. No unnecessary whitespace.

## Development workflow
- **Strict TDD**: write tests FIRST, confirm they fail (red), then implement (green), refactor
- **100% green before commit**: run `php artisan test` after every change. Fix ALL failures — even unrelated ones — before committing
- Tests use DatabaseTransactions (never RefreshDatabase) against real MariaDB
- Run `php artisan test` after every change
- Do not rely on Laravel migrations for production — use database/create_database.sql directly
- Migrations exist for local dev convenience only
- **Wiki**: GitHub wiki (https://github.com/FFGVA/derailleur/wiki) contains functional and technical documentation. Update the wiki on every push when changes affect documented behavior. Wiki repo is at /tmp/derailleur.wiki (clone with `git clone https://github.com/FFGVA/derailleur.wiki.git /tmp/derailleur.wiki`). Functional pages: Membres, Événements, Facturation, Parcours adhésion, Rôles et permissions, Tableau de bord. Technical pages: Architecture, Modèle de données, API, Emails, Déploiement.

## Data model
- **Member**: member_number (4-digit high-watermark), first_name, last_name, email, statuscode (D/A/I/P/N), is_invitee, metadata (JSON), activation_token, membership_start/end, modified_by_id → users
- **MemberPhone**: phone_number, label, is_whatsapp, sort_order. Phone formatting on save via PhoneFormatter.
- **Event**: title, starts_at, ends_at, location, chef_peloton_id → members, statuscode (N/P/X/T), price, max_participants
- **EventMember** (pivot): status (N/C/X), present (boolean nullable)
- **Invoice**: member_id, type (C/E/A), cotisation_year (for type C), invoice_number ({year}-{member_id}-{seq}), amount (sum of lines), statuscode (N/E/P/X)
- **InvoiceLine**: invoice_id, description, amount, sort_order
- **invoice_event** (pivot): invoice_id ↔ event_id (for type E invoices, supports multiple events)
- All domain tables have: modified_by_id → users, updated_at, deleted_at, audit table + triggers

## Invoicing
- Type C (Cotisation): cotisation_year field, auto-generated line "Cotisation annuelle {year}"
- Type E (Événement): linked to events via invoice_event pivot, line per event with title + date
- Type A (Autre): manually added lines with description + amount
- Amount = sum of all lines (stored, not computed on read)
- PDF: FPDF native (not DomPDF), QR payment slip via sprain/swiss-qr-bill FpdfOutput
- Invoice files stored in storage/app/invoices/
- Filename: ffgva_{Nom_Prénom}-facture-{invoice_number}.pdf
- On payment: member number assigned via high-watermark

## Email flow (adhesion)
1. POST /api/adhesion → creates member (statuscode P) + sends AdhesionWelcomeMail (activation link) + AdhesionMail (admin notification)
2. Click activation link → validates token → sends AdhesionConfirmationMail with QR-bill invoice PDF attached
3. Member stays P until admin manually activates after payment
- From: "Fast and Female Geneva - Ne pas répondre" <noreply@ffgva.ch>
- Confirmation email has reply-to: fastandfemalegva@etik.com
- Branded HTML layout with logo, beige background #f5f1e9

## Access control
- Admin (role A): full access
- Cheffe de peloton (role C): can manage their own events (add/remove riders, change statuses), can modify members
- Policies: MemberPolicy, EventPolicy
- Password reset via Filament built-in (signed URLs)

## API endpoints
- POST /api/contact — contact form with CORS, honeypot, rate limiting
- POST /api/adhesion — membership application, auto-creates member + phone + activation token
- GET /adhesion/confirmer?token=&email= — email confirmation, sends invoice

## Deployment
- Script: scripts/deploy.sh (FTP via lftp to Hostpoint)
- Credentials: scripts/ftp.conf (gitignored)
- Production .env: .env.production (gitignored)
- Database: database/create_database.sql (full schema), database/drop_all.sql (clean slate), database/seed_users.sql (admin users)
- Static homepage: public/index.html (no PHP, no cookies)
- Standalone mail scripts: mail/chaine.php, mail/guidon.php (kept in public/ until Hugo site migrates to /api/)

## Configuration
- config/ffgva.php: IBAN, cotisation amount, creditor address, activation expiry
- IBAN: CH9580808004931084283
- Creditor: Fast and Female Geneva, Chemin de Pinchat 42C, 1234 Vessy

## Key credentials (local dev only)
- Admin login: admin@ffgva.ch / password
- Chef de peloton: sophie.dupont@ffgva.ch / password
- Livia Wagner: livia.wagner@gmail.com / hydro2lique
- DB: laravel / 0hbPQ2kDB2fa1QCw on 127.0.0.1:3306 / agiletra_ffgva
