<x-filament::page>

    {{-- ===================== FILTROS ===================== --}}
    <x-filament::section>
        <x-filament-panels::form wire:submit="filtrar">
            {{ $this->form }}

            <div class="flex justify-center space-x-4 mt-4">
                <x-filament::button
                    color="primary"
                    icon="heroicon-o-funnel"
                    type="submit"
                    class="w-1/5"
                >
                    Filtrar
                </x-filament::button>

                <x-filament::button
                    color="danger"
                    icon="heroicon-o-trash"
                    wire:click="limparLogs"
                    type="button"
                    class="w-1/5"
                >
                    Limpar Logs
                </x-filament::button>
            </div>
        </x-filament-panels::form>
    </x-filament::section>

    {{-- ===================== PAGINAÇÃO SUPERIOR ===================== --}}
    <div class="flex justify-between items-center mt-6 mb-3">
        <div class="text-sm text-gray-500">
            {{ $this->paginatedLogs->firstItem() }} até {{ $this->paginatedLogs->lastItem() }}
            de {{ $this->paginatedLogs->total() }} resultados
        </div>
        <div>
            {{ $this->paginatedLogs->links() }}
        </div>
    </div>

    {{-- ===================== TABELA ===================== --}}
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

            @forelse($this->paginatedLogs as $log)
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
                        @endswitch">
                        <pre class="whitespace-pre-wrap">{{ strtoupper($log['level']) }}</pre>
                    </td>

                    {{-- ===================== MENSAGEM TRUNCADA ===================== --}}
                    <td class="px-4 py-2 text-sm text-gray-700">
                        <div class="flex flex-col max-w-[450px]">
                            <span class="whitespace-pre-wrap break-words text-gray-700">
                                {!! nl2br(e(Str::limit($log['message'], 240))) !!}
                            </span>

                            @if(strlen($log['message']) > 240)
                                <button
                                    class="text-primary-600 hover:text-primary-800 text-xs mt-1 underline"
                                    wire:click="abrirLogCompleto('{{ base64_encode($log['message']) }}')"
                                >
                                    Ver completo →
                                </button>
                            @endif
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="4" class="px-4 py-2 text-center text-gray-500">
                        Nenhum log encontrado.
                    </td>
                </tr>
            @endforelse

            </tbody>
        </table>
    </div>

    {{-- ===================== PAGINAÇÃO INFERIOR ===================== --}}
    <div class="flex justify-between items-center mt-6 mb-2">
        <div class="text-sm text-gray-500">
            {{ $this->paginatedLogs->firstItem() }} até {{ $this->paginatedLogs->lastItem() }}
            de {{ $this->paginatedLogs->total() }} resultados
        </div>
        <div>
            {{ $this->paginatedLogs->links() }}
        </div>
    </div>

    {{-- ===================== MODAL DO LOG COMPLETO ===================== --}}
    <x-filament::modal id="modal-log" width="4xl" icon="heroicon-o-eye" heading="Log completo">
        <div class="bg-gray-900 text-gray-100 p-4 rounded-lg max-h-[70vh] overflow-y-auto text-sm leading-relaxed">
            <pre class="whitespace-pre-wrap break-words font-mono text-gray-200">
                {!! $modalContentColored !!}
            </pre>
        </div>
    </x-filament::modal>

</x-filament::page>
