# Dérailleur — Fast and Female Geneva
# Club membership management app — derailleur.ffgva.ch

## Infrastructure
- Production URL: derailleur.ffgva.ch → points to /public
- Hosted on Hostpoint (PHP 8.4, MariaDB 10)
- Local dev: Laravel Herd (macOS) / PHP 8.4 (Debian)
- GitHub: shared repo

## Stack
- Laravel 12 + Filament v4
- MariaDB
- spatie/swiss-qr-bill (CHF invoicing)
- barryvdh/laravel-dompdf (PDF generation)

## Data model
- Member, Event, EventMember

## Conventions
- Language: French only — UI, emails, PDFs, validation messages
- Mobile-first
- No i18n / no localization layer needed
- Branding: ffgva.ch colors and logo in all PDF outputs (resources/images/logo.png)
- Run `php artisan test` after every change

## Key features
- Member registration & management
- Event registration
- Membership card (PDF, emailed)
- Invoices with Swiss QR-code billing
