<?php

namespace Shieldforce\FederalFilamentLog\Pages;

use Filament\Pages\Page;

class FederalFilamentLogsPage extends Page
{
    protected static string  $view            = 'federal-filament-log::pages.logs';
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
