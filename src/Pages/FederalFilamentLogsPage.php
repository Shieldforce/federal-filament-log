<?php

namespace Shieldforce\FederalFilamentLog\Pages;

use Carbon\Carbon;
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

    public ?string $search = null;
    public ?array  $result;

    public function mount(): void
    {
        $this->filtrar();
    }

    protected function getFiltroSchema(): array
    {
        return [

        ];
    }

    protected function getForms(): array
    {
        return [
            'filtroForm' => $this->makeForm()
                ->schema($this->getFiltroSchema())
                ->columns(8),
        ];
    }

    public function updated()
    {
        $this->filtrar();
    }

    public function filtrar()
    {
        $getData  = $this->getData();
        $newItens = [];

        foreach ($getData as $item) {
            $newItens[] = $item;
        }

        $this->result = array_values($newItens);
    }

    public function getData()
    {
        return [
            [
                "numero" => "Um",
                "letra"  => "Dois",
            ],
            [
                "numero" => "Tres",
                "letra"  => "Quatro",
            ]
        ];
    }
}
