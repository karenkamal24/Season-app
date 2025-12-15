<?php

namespace App\Providers\Filament;

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
use App\Http\Middleware\SetLocale;
use App\Filament\Widgets\LanguageSwitcherWidget;
use Filament\Navigation\MenuItem;

class AdminPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->default()
            ->id('admin')
            ->path('admin')
            ->login()
            ->authGuard('web')
            ->colors([
                'primary' => [
                    50 => '#E6EEF5',
                    100 => '#C2D7E8',
                    200 => '#092C4C',
                    300 => '#092C4C',
                    400 => '#568FC1',
                    500 => '#092C4C', // اللون الأساسي
                    600 => '#082743',
                    700 => '#07223A',
                    800 => '#061D31',
                    900 => '#051828',
                ],
            ])

            ->brandLogo(asset('images/logo.png'))
            ->favicon(asset('images/logo.png'))
            ->userMenuItems([
                MenuItem::make()
                    ->label(function () {
                        $locale = session('locale', config('app.locale', 'en'));
                        return $locale === 'ar' ? '🇬🇧 English' : '🇸🇦 العربية';
                    })
                    ->icon('heroicon-o-language')
                    ->url(function () {
                        $locale = session('locale', config('app.locale', 'en'));
                        return route('switch-language', ['locale' => $locale === 'ar' ? 'en' : 'ar']);
                    })
                    ->openUrlInNewTab(false)
            ])
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\\Filament\\Resources')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\\Filament\\Pages')
            ->pages([
                Pages\Dashboard::class,
            ])
            ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\\Filament\\Widgets')
            ->widgets([
                Widgets\AccountWidget::class,
                Widgets\FilamentInfoWidget::class,
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
                SetLocale::class,
            ])
            ->authMiddleware([
                Authenticate::class,
            ]);
    }
}
