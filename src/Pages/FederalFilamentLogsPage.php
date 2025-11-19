<?php

namespace Shieldforce\FederalFilamentLog\Pages;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Livewire\WithPagination;
use Shieldforce\FederalFilamentLog\Services\Permissions\CanPageTrait;

class FederalFilamentLogsPage extends Page implements HasForms
{
    use CanPageTrait;
    use InteractsWithForms;
    use WithPagination;

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

    public array $result = [];

    protected int $perPage = 20;

    public string $modalContent = '';

    public string $modalContentColored = '';

    public function abrirLogCompleto($mensagemBase64)
    {
        $raw = base64_decode($mensagemBase64);

        if ($this->pareceJson($raw)) {
            $raw = json_encode(
                json_decode($raw, true),
                JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE
            );
        }

        $this->modalContent = $raw;
        $this->modalContentColored = $this->colorir($raw);

        $this->dispatch('open-modal', id: 'modal-log');
    }

    private function pareceJson(string $texto): bool
    {
        $trim = trim($texto);

        return Str::startsWith($trim, '{') || Str::startsWith($trim, '[');
    }

    private function colorir(string $raw): string
    {
        // Opcional: escapar apenas < e > reais do log
        $raw = str_replace(['<', '>'], ['&lt;', '&gt;'], $raw);

        $patterns = [
            // Níveis de log
            '/\bEMERGENCY\b/i' => '<span class="text-red-600 font-bold">EMERGENCY</span>',
            '/\bALERT\b/i' => '<span class="text-red-500 font-bold">ALERT</span>',
            '/\bCRITICAL\b/i' => '<span class="text-red-500 font-bold">CRITICAL</span>',
            '/\bERROR\b/i' => '<span class="text-red-400 font-bold">ERROR</span>',
            '/\bWARNING\b/i' => '<span class="text-yellow-400 font-bold">WARNING</span>',
            '/\bNOTICE\b/i' => '<span class="text-cyan-400 font-bold">NOTICE</span>',
            '/\bINFO\b/i' => '<span class="text-blue-400 font-bold">INFO</span>',
            '/\bDEBUG\b/i' => '<span class="text-gray-400 font-bold">DEBUG</span>',

            // Estruturas e valores
            '/array \(/i' => '<span class="text-purple-400 font-bold">array(</span>',
            '/null/i' => '<span class="text-gray-500 font-bold">null</span>',
            '/true/i' => '<span class="text-green-400 font-bold">true</span>',
            '/false/i' => '<span class="text-red-400 font-bold">false</span>',

            // Strings entre aspas
            '/"([^"]*)"/' => '<span class="text-green-300">"$1"</span>',
            "/'([^']*)'/" => '<span class="text-green-300">\'$1\'</span>',
        ];

        foreach ($patterns as $pattern => $replace) {
            $raw = preg_replace($pattern, $replace, $raw);
        }

        // Mantém quebras de linha
        return nl2br($raw);
    }

    protected function getFormSchema(): array
    {
        return [
            Grid::make(3)->schema([
                TextInput::make('search')->label('Palavra-chave'),
                Select::make('tipo')
                    ->label('Tipo/Nível')
                    ->options([
                        '' => 'Todos',
                        'emergency' => 'EMERGENCY',
                        'alert' => 'ALERT',
                        'critical' => 'CRITICAL',
                        'error' => 'ERROR',
                        'warning' => 'WARNING',
                        'notice' => 'NOTICE',
                        'info' => 'INFO',
                        'debug' => 'DEBUG',
                    ]),
                DatePicker::make('data')->label('Data')->format('Y-m-d'),
            ]),
        ];
    }

    public function mount(): void
    {
        $this->filtrar();
    }

    public function updated()
    {
        $this->resetPage();
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

    public function getPaginatedLogsProperty()
    {
        $page = $this->getPage();
        $offset = ($page - 1) * $this->perPage;
        $items = array_slice($this->result, $offset, $this->perPage);

        return new LengthAwarePaginator(
            $items,
            count($this->result),
            $this->perPage,
            $page,
            ['path' => request()->url()]
        );
    }

    protected function getData(): array
    {
        $logFile = storage_path('logs/laravel.log');
        if (! File::exists($logFile)) {
            return [];
        }

        $content = File::get($logFile);
        $lines = explode(PHP_EOL, $content);
        $logs = [];
        $entry = null;

        foreach ($lines as $line) {
            if (preg_match('/^\[(.*?)\] (\w+)\.(\w+): (.*)$/', $line, $m)) {
                if ($entry) {
                    $logs[] = $entry;
                }

                $entry = [
                    'datetime' => $m[1],
                    'env' => $m[2],
                    'level' => $m[3],
                    'message' => $m[4],
                ];
            } elseif ($entry) {
                $entry['message'] .= "\n" . $line;
            }
        }

        if ($entry) {
            $logs[] = $entry;
        }

        return array_reverse($logs);
    }

    public function limparLogs(): void
    {
        foreach (glob(storage_path('logs/*.log')) as $file) {
            file_put_contents($file, '');
        }

        $this->filtrar();

        Notification::make()
            ->success()
            ->title('Logs limpos com sucesso!')
            ->send();
    }
}
