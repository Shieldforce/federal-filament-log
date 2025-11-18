<x-filament::page>

    {{-- Modal para visualizar log completo --}}
    @if($modalLog)
        <x-filament::modal
            id="modal-log"
            icon="heroicon-o-eye"
            heading="Log completo"
            width="4xl"
            close-button
        >
            <div class="max-h-[70vh] overflow-y-auto bg-gray-900 text-gray-200 p-4 rounded">
                <pre class="whitespace-pre-wrap text-sm">{{ $modalLog['message'] }}</pre>
            </div>

            <x-slot name="footer">
                <x-filament::button
                    color="gray"
                    x-on:click="$dispatch('close-modal', { id: 'modal-log' })"
                >
                    Fechar
                </x-filament::button>
            </x-slot>
        </x-filament::modal>
    @endif


    <x-filament::section>
        <x-filament-panels::form wire:submit="filtrar">
            {{ $this->form }}

            <div class="flex justify-center space-x-4">
                <x-filament::button
                    color="primary"
                    icon="heroicon-o-funnel"
                    labeled-from="sm"
                    tag="button"
                    type="submit"
                    style="width: 20%; margin-right:20px;"
                >
                    Filtrar
                </x-filament::button>

                {{-- Botão Limpar Logs --}}
                <x-filament::button
                    color="danger"
                    icon="heroicon-o-trash"
                    labeled-from="sm"
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


    {{-- Tabela --}}
    <div class="overflow-x-auto bg-white rounded-lg shadow">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
            <tr>
                <th class="px-4 py-2 text-xs font-medium text-gray-500">Data/Hora</th>
                <th class="px-4 py-2 text-xs font-medium text-gray-500">Ambiente</th>
                <th class="px-4 py-2 text-xs font-medium text-gray-500">Tipo</th>
                <th class="px-4 py-2 text-xs font-medium text-gray-500">Mensagem</th>
            </tr>
            </thead>

            <tbody class="bg-white divide-y divide-gray-200">
            @forelse($this->paginatedLogs as $log)

                @php
                    $linhas = explode("\n", $log['message']);
                    $primeira = $linhas[0] ?? '';
                    $temMais = count($linhas) > 1;
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
                        @endswitch">
                        {{ strtoupper($log['level']) }}
                    </td>

                    <td class="px-4 py-2 align-top">

                        @php
                            $linhas = explode("\n", $log['message']);
                            $primeira = $linhas[0] ?? '';
                            $temMais = count($linhas) > 1;
                        @endphp

                        {{-- wrapper para isolar do Filament --}}
                        <div class="bg-transparent !bg-transparent">

                            {{-- Caixa escura com limite de altura --}}
                            <div
                                class="rounded p-2 overflow-y-auto whitespace-pre-wrap"
                                style="
                                    max-height:150px;
                                    background:#111 !important;
                                    color:#f1f1f1 !important;
                                    border:1px solid #333 !important;
                                "
                            >
                                {{ $primeira }}

                                @if($temMais)
                                    <div style="margin-top:6px; text-align:center; font-size:11px; color:#ccc;">
                                        (... mais linhas)
                                    </div>
                                @endif
                            </div>

                            @if($temMais)
                                <x-filament::button
                                    color="primary"
                                    size="xs"
                                    class="mt-2"
                                    wire:click="abrirModal({{ json_encode($log) }})"
                                >
                                    Ver mais
                                </x-filament::button>
                            @endif

                        </div>

                    </td>
                </tr>

            @empty
                <tr>
                    <td colspan="4" class="px-4 py-4 text-center text-gray-500">
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
