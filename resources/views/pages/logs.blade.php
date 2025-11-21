<x-filament::page>

    {{-- FILTROS --}}
    <x-filament::section>
        <x-filament-panels::form wire:submit="filtrar">
            {{ $this->form }}

            <div class="flex justify-between mt-4 gap-4">
                <x-filament::button color="primary" icon="heroicon-o-funnel" type="submit" class="w-1/5">
                    Filtrar
                </x-filament::button>

                <x-filament::button color="danger" icon="heroicon-o-trash"
                                    wire:click="limparLogs"
                                    type="button" class="w-1/5">
                    Limpar Logs
                </x-filament::button>
            </div>
        </x-filament-panels::form>
    </x-filament::section>

    {{-- PAGINAÇÃO SUPERIOR --}}
    <div class="flex justify-between items-center mt-6 mb-3">
        <div class="text-sm text-gray-600 dark:text-gray-300">
            {{ $this->paginatedLogs->firstItem() }} até {{ $this->paginatedLogs->lastItem() }}
            de {{ $this->paginatedLogs->total() }} resultados
        </div>
        <div>
            {{ $this->paginatedLogs->links() }}
        </div>
    </div>

    {{-- TABELA --}}
    <div class="overflow-x-auto bg-white dark:bg-gray-900 rounded-lg shadow border border-gray-200 dark:border-gray-700">
        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700 w-full">

            <thead class="bg-gray-50 dark:bg-gray-800">
            <tr>
                <th class="px-4 py-2 text-left text-xs font-medium text-gray-600 dark:text-gray-300">Data/Hora</th>
                <th class="px-4 py-2 text-left text-xs font-medium text-gray-600 dark:text-gray-300">Ambiente</th>
                <th class="px-4 py-2 text-left text-xs font-medium text-gray-600 dark:text-gray-300">Tipo</th>
                <th class="px-4 py-2 text-left text-xs font-medium text-gray-600 dark:text-gray-300">Mensagem</th>
            </tr>
            </thead>

            <tbody class="bg-white dark:bg-gray-900 divide-y divide-gray-200 dark:divide-gray-700">
            @forelse($this->paginatedLogs as $log)
                <tr>
                    <td class="px-4 py-2 text-sm text-gray-700 dark:text-gray-300">{{ $log['datetime'] }}</td>
                    <td class="px-4 py-2 text-sm text-gray-700 dark:text-gray-300">{{ $log['env'] }}</td>
                    <td class="px-4 py-2 text-sm font-semibold text-gray-800 dark:text-gray-200">
                        {{ strtoupper($log['level']) }}
                    </td>
                    <td class="px-4 py-2 text-sm text-gray-700 dark:text-gray-300 break-words">
                        <div class="flex flex-col">
                            <span class="whitespace-pre-wrap break-words">
                                {!! nl2br(e(Str::limit($log['message'], 260))) !!}
                            </span>

                            @if(strlen($log['message']) > 260)
                                <button
                                    class="text-primary-600 dark:text-primary-400 hover:underline text-xs mt-1"
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
                    <td colspan="4" class="px-4 py-3 text-center text-gray-500 dark:text-gray-400">
                        Nenhum log encontrado.
                    </td>
                </tr>
            @endforelse
            </tbody>
        </table>
    </div>

    {{-- PAGINAÇÃO INFERIOR --}}
    <div class="flex justify-between items-center mt-6 mb-2">
        <div class="text-sm text-gray-600 dark:text-gray-300">
            {{ $this->paginatedLogs->firstItem() }} até {{ $this->paginatedLogs->lastItem() }}
            de {{ $this->paginatedLogs->total() }} resultados
        </div>
        <div>
            {{ $this->paginatedLogs->links() }}
        </div>
    </div>

    {{-- MODAL --}}
    <x-filament::modal
        id="modal-log"
        width="6xl"
        icon="heroicon-o-eye"
        heading="Log completo"
    >
        <div class="p-4 rounded-lg bg-gray-100 dark:bg-gray-900 max-h-[80vh] overflow-y-auto text-sm text-gray-800 dark:text-gray-200">
            <pre class="whitespace-pre-wrap break-words font-mono leading-5">
{!! $this->modalContentColored !!}
            </pre>
        </div>
    </x-filament::modal>

</x-filament::page>
