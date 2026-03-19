# Dérailleur — CLAUDE.md

## Project
- Club membership management app for Fast and Female Geneva (cycling club)
- Production: derailleur.ffgva.ch (Hostpoint, PHP 8.4, MariaDB 10.11)
- Local dev: PHP 8.4 (Debian), MariaDB 11.8, Apache vhost derailleur.local
- SQL must be compatible with MariaDB 10.11 (production) — avoid 11.x-only features
- GitHub: FFGVA/derailleur

## Stack
- Laravel 12 + Filament v3
- MariaDB (database: agiletra_ffgva, user: laravel)
- spatie/swiss-qr-bill (CHF invoicing — planned)
- barryvdh/laravel-dompdf (PDF generation — planned)
- Mailpit on 192.168.178.41:1025 (local dev), production SMTP via .env

## Conventions
- Language: French only — ALL UI, emails, PDFs, validation messages
- Mobile-first
- No i18n / no localization layer
- CHAR(1) statuscode fields in DB — enumerations only in PHP code (app/Enums/)
- No cascade deletes — EVER. Always check before deleting.
- Soft-delete on all domain tables
- Domain tables have only `updated_at`, no `created_at`
- Audit via MariaDB BEFORE UPDATE/DELETE triggers on all domain tables
- Colors/styling in CSS (resources/css/filament/admin.css), not inline on pages
- Branding: ffgva.ch colors — primary burgundy #80081C

## Development workflow
- **Strict TDD**: write tests FIRST, confirm they fail (red), then implement (green), refactor
- **100% green before commit**: run `php artisan test` after every change. Fix ALL failures — even unrelated ones — before committing
- Tests use DatabaseTransactions (never RefreshDatabase) against real MariaDB
- Run `php artisan test` after every change

## Data model
- Member, Event, EventMember (pivot), MemberPhone
- Members can be invitées (is_invitee flag) — guests not yet full members
- Events have a cheffe de peloton (chef_peloton_id → members)
- event_member has status (N/C/X) and present (boolean, nullable)
- Adhesion form metadata stored as JSON blob on members.metadata

## Access control
- Admin (role A): full access
- Cheffe de peloton (role C): can manage their own events (add/remove riders, change statuses), can modify members
- Policies: MemberPolicy, EventPolicy

## API endpoints
- POST /api/contact — contact form (replaces mail/chaine.php)
- POST /api/adhesion — membership application, auto-creates member with statuscode P

## Key credentials (local dev only)
- Admin login: admin@ffgva.ch / password
- Chef de peloton: sophie.dupont@ffgva.ch / password
