<?php

namespace Shieldforce\FederalFilamentLog\Pages;

use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Pages\Page;
use Filament\Tables\Actions\Action;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\File;

class FederalFilamentLogsPage extends Page implements HasForms, HasTable
{
    use InteractsWithForms;
    use InteractsWithTable;

    protected static string $view = 'federal-filament-log::pages.index';

    protected static ?string $navigationIcon = 'heroicon-o-list-bullet';

    protected static ?string $navigationGroup = 'Logs';

    protected static ?string $label = 'Log';

    protected static ?string $navigationLabel = 'Logs do Sistema';

    protected static ?string $slug = 'logs';

    protected static ?string $title = 'Logs do Sistema';

    public ?string $search = '';

    /**
     * Lê os logs do arquivo laravel.log e retorna uma Collection
     */
    public static function getLogs(): Collection
    {
        $logFile = storage_path('logs/laravel.log');

        if (! File::exists($logFile)) {
            return collect([['level' => 'info', 'message' => 'Arquivo de log vazio ou inexistente.']]);
        }

        $lines = collect(explode("\n", File::get($logFile)))
            ->filter(fn ($line) => trim($line) !== '')
            ->map(function ($line) {
                // Tenta identificar o nível e mensagem
                if (preg_match('/\[(.*?)\] (local|production)\.(\w+): (.*)/', $line, $matches)) {
                    return [
                        'datetime' => $matches[1] ?? '',
                        'env' => $matches[2] ?? '',
                        'level' => strtoupper($matches[3] ?? 'INFO'),
                        'message' => $matches[4] ?? '',
                    ];
                }

                return [
                    'datetime' => now()->format('Y-m-d H:i:s'),
                    'env' => app()->environment(),
                    'level' => 'INFO',
                    'message' => $line,
                ];
            })
            ->reverse() // mostra os logs mais recentes primeiro
            ->values();

        return $lines;
    }

    /**
     * Define a tabela
     */
    public function table(Table $table): Table
    {
        return $table
            ->columns([
                // ... suas colunas
            ])
            ->data(fn () => self::getLogs()) // ✅ usa a collection direto aqui
            ->filtersLayout(\Filament\Tables\Enums\FiltersLayout::AboveContentCollapsible)
            ->filtersFormColumns(3)
            ->filtersTriggerAction(fn (Action $action) => $action->button()->label('Filtrar...'))
            ->actions([])
            ->bulkActions([])
            ->headerActions([
                Action::make('refresh')
                    ->label('Recarregar')
                    ->icon('heroicon-o-arrow-path')
                    ->color('gray')
                    ->action(fn () => $this->dispatch('$refresh')),

                Action::make('clear')
                    ->label('Limpar Logs')
                    ->icon('heroicon-o-trash')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->action(function () {
                        \Illuminate\Support\Facades\File::put(storage_path('logs/laravel.log'), '');
                        $this->notify('success', 'Logs limpos com sucesso!');
                    }),
            ]);
    }
}
