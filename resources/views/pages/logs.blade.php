<x-filament::page>
    <x-filament::section>
        <x-filament-panels::form wire:submit="filtrar">
            {{ $this->form }}
            <div class="flex justify-center space-x-4">
                <x-filament::button
                    color="primary"
                    icon="heroicon-o-funnel"
                    icon-alias="panels::widgets.account.logout-button"
                    labeled-from="sm"
                    tag="button"
                    type="submit"
                    class=""
                    style="width: 20%; margin-right:20px;"
                >
                    Filtrar
                </x-filament::button>
            </div>
        </x-filament-panels::form>
    </x-filament::section>

    {{-- Tabela de logs --}}
    <div class="overflow-x-auto bg-white rounded-lg shadow">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
            <tr>
                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Data/Hora</th>
                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Ambiente</th>
                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tipo</th>
                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Mensagem</th>
            </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
            @forelse($result as $log)
                <tr>
                    <td class="px-4 py-2 text-sm text-gray-700">{{ $log['datetime'] }}</td>
                    <td class="px-4 py-2 text-sm text-gray-700">{{ $log['env'] }}</td>
                    <td class="px-4 py-2 text-sm font-semibold
                            @switch(strtolower($log['level']))
                                @case('error') text-red-600 @break
                                @case('critical') text-red-700 @break
                                @case('warning') text-yellow-600 @break
                                @case('info') text-green-600 @break
                                @case('debug') text-blue-600 @break
                                @default text-gray-700
                            @endswitch
                        ">
                        {{ strtoupper($log['level']) }}
                    </td>
                    <td class="px-4 py-2 text-sm text-gray-700">{{ $log['message'] }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="4" class="px-4 py-2 text-center text-gray-500">Nenhum log encontrado.</td>
                </tr>
            @endforelse
            </tbody>
        </table>
    </div>
</x-filament::page>
