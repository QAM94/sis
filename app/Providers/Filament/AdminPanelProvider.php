<?php

namespace App\Providers\Filament;

use App\Filament\Resources\OfferedCourseResource\Pages\OfferedCourses;
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Navigation\NavigationGroup;
use Filament\Navigation\NavigationItem;
use Filament\Pages;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Filament\Widgets;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\AuthenticateSession;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;


class AdminPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->default()
            ->id('admin')
            ->path('sis')
            ->login()
            ->colors([
                'primary' => Color::Amber,
            ])
            ->sidebarWidth('14rem')
            ->sidebarCollapsibleOnDesktop()
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\\Filament\\Resources')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\\Filament\\Pages')
            ->pages([
                Pages\Dashboard::class,
            ])
            ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\\Filament\\Widgets')
            ->widgets([
                Widgets\StatsOverviewWidget::class,
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
            ])
            ->authGuard('web')
            ->navigationItems([
                /*NavigationItem::make('Course Registration')
                    ->visible(fn(): bool => auth()->user()->can('offered_course-register'))
                    ->url(fn (): string => OfferedCourses::getUrl())
                    ->icon('heroicon-o-presentation-chart-line')*/
            ])
            ->navigationGroups([
                NavigationGroup::make()
                    ->label('User Management')
                    ->icon('heroicon-o-user-group'),
                NavigationGroup::make()
                    ->label('Curriculum Management')
                    ->icon('heroicon-o-rectangle-stack'),
                NavigationGroup::make()
                    ->label('Semester Management')
                    ->icon('heroicon-o-calendar-date-range'),
                NavigationGroup::make()
                    ->label('Fees Management')
                    ->icon('heroicon-o-currency-dollar'),
            ]);
    }
}
