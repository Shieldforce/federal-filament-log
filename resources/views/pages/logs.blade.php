<x-filament::page>
    <div class="space-y-6">
        {{-- Formulário de filtros --}}
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700">Palavra-chave</label>
                <input type="text" wire:model="search" placeholder="Buscar mensagem..."
                       class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring focus:ring-primary-200 focus:ring-opacity-50">
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700">Tipo/Nível</label>
                <select wire:model="tipo"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring focus:ring-primary-200 focus:ring-opacity-50">
                    <option value="">Todos</option>
                    <option value="emergency">EMERGENCY</option>
                    <option value="alert">ALERT</option>
                    <option value="critical">CRITICAL</option>
                    <option value="error">ERROR</option>
                    <option value="warning">WARNING</option>
                    <option value="notice">NOTICE</option>
                    <option value="info">INFO</option>
                    <option value="debug">DEBUG</option>
                </select>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700">Data</label>
                <input type="text" wire:model="data" placeholder="YYYY-MM-DD"
                       class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring focus:ring-primary-200 focus:ring-opacity-50">
            </div>
        </div>

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
    </div>
</x-filament::page>
