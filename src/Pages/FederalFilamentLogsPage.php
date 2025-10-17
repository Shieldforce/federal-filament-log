<?php

namespace Shieldforce\FederalFilamentLog\Pages;

use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Pages\Page;
use Filament\Tables\Actions\Action;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Table;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\File;

class FederalFilamentLogsPage extends Page implements HasForms, HasTable
{
    use InteractsWithForms;
    use InteractsWithTable;

    protected static string  $view            = 'federal-filament-log::pages.index';
    protected static ?string $navigationIcon  = 'heroicon-o-list-bullet';
    protected static ?string $navigationGroup = 'Logs';
    protected static ?string $label           = 'Log';
    protected static ?string $navigationLabel = 'Logs do Sistema';
    protected static ?string $slug            = 'logs';
    protected static ?string $title           = 'Logs do Sistema';

    public ?string $search = '';

    /**
     * Lê os logs do arquivo laravel.log e retorna uma Collection
     */
    public static function getLogs(): Collection
    {
        $logFile = storage_path('logs/laravel.log');

        if (!File::exists($logFile)) {
            return collect([[
                'datetime' => now()->toDateTimeString(),
                'env' => app()->environment(),
                'level' => 'INFO',
                'message' => 'Arquivo de log vazio ou inexistente.'
            ]]);
        }

        return collect(explode("\n", File::get($logFile)))
            ->filter(fn($line) => trim($line) !== '')
            ->map(function ($line) {
                if (preg_match('/\[(.*?)\] (local|production)\.(\w+): (.*)/', $line, $m)) {
                    return [
                        'datetime' => $m[1] ?? now()->toDateTimeString(),
                        'env' => $m[2] ?? app()->environment(),
                        'level' => strtoupper($m[3] ?? 'INFO'),
                        'message' => $m[4] ?? $line,
                    ];
                }

                return [
                    'datetime' => now()->toDateTimeString(),
                    'env' => app()->environment(),
                    'level' => 'INFO',
                    'message' => $line,
                ];
            })
            ->reverse()
            ->values();
    }

    /**
     * Query obrigatória para Filament (não usada, só para satisfazer interface)
     */
    protected function getTableQuery()
    {
        return self::getLogs(); // dummy, não é Eloquent
    }

    /**
     * Retorna os registros da tabela usando Collection
     */
    protected function getTableRecordsUsing(): Collection
    {
        $logs = self::getLogs();

        // aplica filtros manuais
        $filters = $this->getCachedTableFilters();

        if (isset($filters['level'])) {
            $logs = $logs->filter(fn($log) => $log['level'] === $filters['level']);
        }

        if (isset($filters['search'])) {
            $search = strtolower($filters['search']);
            $logs = $logs->filter(fn($log) => str_contains(strtolower($log['message']), $search));
        }

        return $logs;
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('datetime')
                    ->label('Data')
                    ->sortable(),
                TextColumn::make('level')
                    ->label('Nível')
                    ->badge()
                    ->color(fn($state) => match ($state) {
                        'ERROR' => 'danger',
                        'WARNING' => 'warning',
                        'INFO' => 'info',
                        'DEBUG' => 'gray',
                        default => 'primary',
                    }),
                TextColumn::make('message')
                    ->label('Mensagem')
                    ->wrap()
                    ->searchable(),
            ])
            ->filters([
                Filter::make('search')
                    ->form([
                        \Filament\Forms\Components\TextInput::make('search')
                            ->label('Palavra-chave'),
                    ]),
                SelectFilter::make('level')
                    ->label('Nível')
                    ->options([
                        'ERROR' => 'ERROR',
                        'WARNING' => 'WARNING',
                        'INFO' => 'INFO',
                        'DEBUG' => 'DEBUG',
                    ]),
            ])
            ->filtersLayout(\Filament\Tables\Enums\FiltersLayout::AboveContentCollapsible)
            ->filtersFormColumns(3)
            ->filtersTriggerAction(fn($action) => $action->button()->label('Filtrar...'))
            ->actions([])
            ->bulkActions([])
            ->headerActions([
                Action::make('refresh')
                    ->label('Recarregar')
                    ->icon('heroicon-o-arrow-path')
                    ->color('gray')
                    ->action(fn() => $this->redirect(request()->fullUrl())),
                Action::make('clear')
                    ->label('Limpar Logs')
                    ->icon('heroicon-o-trash')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->action(function () {
                        File::put(storage_path('logs/laravel.log'), '');
                        $this->notify('success', 'Logs limpos com sucesso!');
                    }),
            ]);
    }
}
