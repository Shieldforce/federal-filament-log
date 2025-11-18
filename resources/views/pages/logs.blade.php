<x-filament::page>

    {{-- Modal para visualizar log completo --}}
    @if($modalLog)
        <x-filament::modal
            id="modal-log"
            width="4xl"
            heading="Log completo"
            icon="heroicon-o-eye"
            wire:model="modalLog"
            close-button
        >
            <div class="bg-gray-900 text-green-400 p-4 rounded-lg max-h-[70vh] overflow-y-auto text-sm font-mono">
                <pre class="whitespace-pre-wrap">
{!! $modalContent !!}
                </pre>
            </div>
        </x-filament::modal>
    @endif


    {{-- Filtros --}}
    <x-filament::section>
        <x-filament-panels::form wire:submit="filtrar">
            {{ $this->form }}

            <div class="flex justify-center space-x-4 mt-4">
                <x-filament::button
                    color="primary"
                    icon="heroicon-o-funnel"
                    type="submit"
                    style="width: 20%; margin-right:20px;"
                >
                    Filtrar
                </x-filament::button>

                <x-filament::button
                    color="danger"
                    icon="heroicon-o-trash"
                    wire:click="limparLogs"
                    type="button"
                    style="width: 20%;"
                >
                    Limpar Logs
                </x-filament::button>
            </div>
        </x-filament-panels::form>
    </x-filament::section>

    {{-- Paginação superior --}}
    <div class="flex justify-between items-center mt-6 mb-3">
        <div class="text-sm text-gray-500">
            {{ $this->paginatedLogs->firstItem() }} até {{ $this->paginatedLogs->lastItem() }}
            de {{ $this->paginatedLogs->total() }} resultados
        </div>
        <div>
            {{ $this->paginatedLogs->links() }}
        </div>
    </div>


    {{-- Tabela de Logs --}}
    <div class="overflow-x-auto bg-white rounded-lg shadow">

        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
            <tr>
                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                    Data/Hora
                </th>
                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                    Ambiente
                </th>
                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                    Tipo
                </th>
                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                    Mensagem
                </th>
            </tr>
            </thead>

            <tbody class="bg-white divide-y divide-gray-200">
            @forelse($this->paginatedLogs as $log)

                @php
                    $message = $log['message'];
                    $isMultiline = str_contains($message, "\n")
                                    || strlen($message) > 150
                                    || Str::startsWith(trim($message), ['[', '{']);
                @endphp

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
                        <pre class="whitespace-pre-wrap">{{ strtoupper($log['level']) }}</pre>
                    </td>

                    <td class="px-4 py-2 text-sm text-gray-700">
                        @if ($isMultiline)
                            <div class="flex items-center space-x-2">
                                <span class="truncate max-w-[350px] block text-gray-600">
                                    {!! nl2br(e(Str::limit($message, 150))) !!}
                                </span>

                                <x-filament::button
                                    color="primary"
                                    icon="heroicon-o-eye"
                                    size="xs"
                                    wire:click="abrirLogCompleto('{{ base64_encode($message) }}')"
                                >
                                    Ver
                                </x-filament::button>
                            </div>
                        @else
                            {{ $message }}
                        @endif
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


    {{-- Paginação inferior --}}
    <div class="flex justify-between items-center mt-6 mb-2">
        <div class="text-sm text-gray-500">
            {{ $this->paginatedLogs->firstItem() }} até {{ $this->paginatedLogs->lastItem() }}
            de {{ $this->paginatedLogs->total() }} resultados
        </div>
        <div>
            {{ $this->paginatedLogs->links() }}
        </div>
    </div>

</x-filament::page>
