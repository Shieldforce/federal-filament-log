<?php

namespace Shieldforce\FederalFilamentLog\Pages;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Pages\Page;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class FederalFilamentLogsPage extends Page
{
    protected static string $view = 'federal-filament-log::pages.logs';

    protected static ?string $navigationIcon = 'heroicon-o-list-bullet';

    protected static ?string $navigationGroup = 'Logs';

    protected static ?string $label = 'Log';

    protected static ?string $navigationLabel = 'Logs do Sistema';

    protected static ?string $slug = 'logs';

    protected static ?string $title = 'Logs do Sistema';

    public ?string $search = null;

    public ?string $tipo = null;

    public ?string $data = null;

    public ?array $result = [];

    public function mount(): void
    {
        $this->filtrar();
    }

    protected function getFiltroSchema(): array
    {
        return [
            TextInput::make('search')
                ->label('Palavra-chave')
                ->placeholder('Buscar mensagem...'),

            Select::make('tipo')
                ->label('Tipo/Nível')
                ->options([
                    'emergency' => 'EMERGENCY',
                    'alert' => 'ALERT',
                    'critical' => 'CRITICAL',
                    'error' => 'ERROR',
                    'warning' => 'WARNING',
                    'notice' => 'NOTICE',
                    'info' => 'INFO',
                    'debug' => 'DEBUG',
                ])
                ->placeholder('Todos'),

            TextInput::make('data')
                ->label('Data')
                ->placeholder('YYYY-MM-DD'),
        ];
    }

    public function updated($propertyName)
    {
        $this->filtrar();
    }

    public function filtrar()
    {
        $logs = $this->getData();

        if ($this->search) {
            $logs = array_filter($logs, fn ($item) => Str::contains(strtolower($item['message']), strtolower($this->search)));
        }

        if ($this->tipo) {
            $logs = array_filter($logs, fn ($item) => strtolower($item['level']) === strtolower($this->tipo));
        }

        if ($this->data) {
            $logs = array_filter($logs, fn ($item) => Str::startsWith($item['datetime'], $this->data));
        }

        $this->result = array_values($logs);
    }

    public function getData(): array
    {
        $logFile = storage_path('logs/laravel.log');

        if (! File::exists($logFile)) {
            return [
                [
                    'datetime' => now()->toDateTimeString(),
                    'level' => 'INFO',
                    'message' => 'Arquivo de log vazio ou inexistente.',
                ],
            ];
        }

        $content = File::get($logFile);
        $lines = explode(PHP_EOL, $content);
        $logs = [];

        // Regex padrão Laravel log: [2025-10-16 22:15:00] local.INFO: Mensagem
        foreach ($lines as $line) {
            if (preg_match('/\[(.*?)\] (\w+)\.(\w+): (.*)/', $line, $matches)) {
                $logs[] = [
                    'datetime' => $matches[1],
                    'env' => $matches[2],
                    'level' => strtoupper($matches[3]),
                    'message' => $matches[4],
                ];
            }
        }

        return $logs;
    }
}
