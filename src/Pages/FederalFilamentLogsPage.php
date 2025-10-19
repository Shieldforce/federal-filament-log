<?php

namespace Shieldforce\FederalFilamentLog\Pages;

use Filament\Forms\Components\Grid;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
use Filament\Pages\Page;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Livewire\WithPagination;
use Illuminate\Pagination\LengthAwarePaginator;
use Shieldforce\FederalFilamentLog\Services\Permissions\CanPageTrait;

class FederalFilamentLogsPage extends Page implements HasForms
{
    use CanPageTrait;

    use InteractsWithForms;
    use WithPagination;

    protected static string  $view            = 'federal-filament-log::pages.logs';
    protected static ?string $navigationIcon  = 'heroicon-o-list-bullet';
    protected static ?string $navigationGroup = 'Logs';
    protected static ?string $label           = 'Log';
    protected static ?string $navigationLabel = 'Logs do Sistema';
    protected static ?string $slug            = 'logs';
    protected static ?string $title           = 'Logs do Sistema';

    public ?string $search = null;
    public ?string $tipo   = null;
    public ?string $data   = null;

    public array $result = [];

    protected int $perPage = 20; // Quantidade por página

    protected function getFormSchema(): array
    {
        return [
            Grid::make(3)->schema([
                TextInput::make('search')
                    ->label('Palavra-chave')
                    ->placeholder('Buscar mensagem...'),

                Select::make('tipo')
                    ->label('Tipo/Nível')
                    ->options([
                        ''          => 'Todos',
                        'emergency' => 'EMERGENCY',
                        'alert'     => 'ALERT',
                        'critical'  => 'CRITICAL',
                        'error'     => 'ERROR',
                        'warning'   => 'WARNING',
                        'notice'    => 'NOTICE',
                        'info'      => 'INFO',
                        'debug'     => 'DEBUG',
                    ])
                    ->placeholder('Todos'),

                TextInput::make('data')
                    ->label('Data')
                    ->placeholder('YYYY-MM-DD'),
            ]),
        ];
    }

    public function mount(): void
    {
        $this->form->fill([
            'search' => $this->search,
            'tipo'   => $this->tipo,
            'data'   => $this->data,
        ]);

        $this->filtrar();
    }

    public function updated($propertyName)
    {
        // Reinicia a paginação ao mudar filtro
        $this->resetPage();
        $this->filtrar();
    }

    public function filtrar()
    {
        $logs = $this->getData();

        if ($this->search) {
            $logs = array_filter($logs, fn($item) =>
            Str::contains(strtolower($item['message']), strtolower($this->search))
            );
        }

        if ($this->tipo) {
            $logs = array_filter($logs, fn($item) =>
                strtolower($item['level']) === strtolower($this->tipo)
            );
        }

        if ($this->data) {
            $logs = array_filter($logs, fn($item) =>
            Str::startsWith($item['datetime'], $this->data)
            );
        }

        $this->result = array_values($logs);
    }

    /**
     * Retorna logs paginados, mantendo filtros
     */
    public function getPaginatedLogsProperty()
    {
        $page    = $this->getPage();
        $offset  = ($page - 1) * $this->perPage;
        $items   = array_slice($this->result, $offset, $this->perPage);

        return new LengthAwarePaginator(
            $items,
            count($this->result),
            $this->perPage,
            $page,
            ['path' => request()->url(), 'query' => request()->query()]
        );
    }

    protected function getData(): array
    {
        $logFile = storage_path('logs/laravel.log');

        if (!File::exists($logFile)) {
            return [[
                'datetime' => now()->toDateTimeString(),
                'env'      => app()->environment(),
                'level'    => 'INFO',
                'message'  => 'Arquivo de log vazio ou inexistente.',
            ]];
        }

        $content = File::get($logFile);
        $lines   = explode(PHP_EOL, $content);
        $logs    = [];

        foreach ($lines as $line) {
            if (preg_match('/\[(.*?)\] (\w+)\.(\w+): (.*)/', $line, $matches)) {
                $logs[] = [
                    'datetime' => $matches[1],
                    'env'      => $matches[2],
                    'level'    => strtoupper($matches[3]),
                    'message'  => $matches[4],
                ];
            }
        }

        return array_reverse($logs); // mostra os mais recentes primeiro
    }
}
