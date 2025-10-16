<?php

namespace Shieldforce\FederalFilamentLog\Pages;

use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Pages\Page;
use Filament\Tables\Actions\Action;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Enums\FiltersLayout;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Auth;
use Shieldforce\CheckoutPayment\Services\Permissions\CanPageTrait;

class FederalFilamentLogsPage extends Page implements HasForms, HasTable
{
    use CanPageTrait;
    use InteractsWithForms;
    use InteractsWithTable;

    protected static string $view = 'federal-filament-log::pages.index';

    protected static ?string $navigationIcon = 'heroicon-o-list';

    protected static ?string $navigationGroup = 'Logs';

    protected static ?string $label = 'Log';

    protected static ?string $navigationLabel = 'Log';

    protected static ?string $slug = 'logs';

    protected static ?string $title = 'Lista de Logs';

    public function mount(?int $checkoutId = null): void {}

    public function table(Table $table): Table
    {
        return $table
            ->columns([])
            ->filters([

            ], layout: FiltersLayout::AboveContentCollapsible)
            ->filtersFormColumns(3)
            ->filtersTriggerAction(
                fn (Action $action) => $action
                    ->button()
                    ->label('Filtrar...'),
            )
            ->bulkActions([])
            ->actions([]);
    }

    public static function getNavigationGroup(): ?string
    {
        return config()->get('federal-filament-log.sidebar_group');
    }

    public static function shouldRegisterNavigation(): bool
    {
        return Auth::check();
    }
}
