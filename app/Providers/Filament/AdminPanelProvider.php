<?php

namespace App\Providers\Filament;

use Filament\Support\Enums\Alignment;
use Filament\View\PanelsRenderHook;
use Illuminate\Support\HtmlString;
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Pages;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Filament\Widgets;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;

class AdminPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->default()
            ->id('admin')
            ->path('admin')
            ->login()
            ->passwordReset()
            ->brandName('Dérailleur')
            ->brandLogo(asset('images/derailleur.png'))
            ->brandLogoHeight('2.5rem')
            ->darkModeBrandLogo(asset('images/derailleur.png'))
            ->favicon(asset('favicon.ico'))
            ->colors([
                'primary' => Color::hex('#80081C'),
                'gray' => Color::Zinc,
                'info' => Color::hex('#5b5a9e'),
                'danger' => Color::hex('#9b2c2c'),
                'success' => Color::hex('#276749'),
                'warning' => Color::hex('#b7791f'),
            ])
            ->renderHook(
                PanelsRenderHook::HEAD_END,
                fn () => new HtmlString('<meta name="robots" content="noindex, nofollow"><style>' . file_get_contents(resource_path('css/filament/admin.css')) . '</style>'),
            )
            ->renderHook(
                PanelsRenderHook::BODY_END,
                fn () => new HtmlString(
                    '<script src="/js/sg-phone-rules.js"></script>' .
                    '<script src="/js/sg-phone.js"></script>'
                ),
            )
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\\Filament\\Resources')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\\Filament\\Pages')
            ->pages([
                Pages\Dashboard::class,
            ])
            ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\\Filament\\Widgets')
            ->widgets([
                Widgets\AccountWidget::class,
            ])
            ->middleware([
                EncryptCookies::class,
                AddQueuedCookiesToResponse::class,
                StartSession::class,
                AuthenticateSession::class,
                ShareErrorsFromSession::class,
                VerifyCsrfToken::class,
                SubstituteBindings::class,
                DisableBladeIconComponents::class,
                DispatchServingFilamentEvent::class,
            ])
            ->authMiddleware([
                Authenticate::class,
                \App\Http\Middleware\SetDatabaseUserId::class,
            ]);
    }
}
