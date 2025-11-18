<x-filament::page>

    {{-- ======================= MODAL DO TERMINAL ======================= --}}
    <x-filament::modal
        id="modal-log"
        width="7xl"
        heading="Terminal de Logs"
        icon="heroicon-o-command-line"
        close-button
    >
        <div class="p-2">

            {{-- Barra de ações --}}
            <div class="flex justify-between mb-3 px-2">
                <div class="flex gap-2">
                    <button
                        onclick="copyTerminalText()"
                        class="px-3 py-1 bg-gray-700 hover:bg-gray-600 text-white rounded text-sm"
                    >
                        Copiar
                    </button>

                    <button
                        onclick="downloadTerminalText()"
                        class="px-3 py-1 bg-gray-700 hover:bg-gray-600 text-white rounded text-sm"
                    >
                        Baixar .txt
                    </button>
                </div>

                <div class="flex gap-2">
                    <button
                        onclick="scrollTopTerminal()"
                        class="px-3 py-1 bg-gray-700 hover:bg-gray-600 text-white rounded text-sm"
                    >
                        Topo
                    </button>

                    <button
                        onclick="scrollBottomTerminal()"
                        class="px-3 py-1 bg-gray-700 hover:bg-gray-600 text-white rounded text-sm"
                    >
                        Fundo
                    </button>
                </div>
            </div>

            {{-- Terminal --}}
            <div id="terminal-container"
                 class="bg-black text-green-400 p-4 rounded-lg shadow-2xl max-h-[75vh] overflow-y-auto font-mono text-sm border border-green-500/40"
                 style="box-shadow: 0 0 12px rgba(0,255,100,0.35), inset 0 0 15px rgba(0,255,100,0.15);">

                {{-- Cabeçalho estilizado --}}
                <div class="flex items-center space-x-2 mb-4 select-none">
                    <div class="w-3 h-3 rounded-full bg-red-500"></div>
                    <div class="w-3 h-3 rounded-full bg-yellow-500"></div>
                    <div class="w-3 h-3 rounded-full bg-green-500"></div>

                    <span class="ml-3 text-green-400 tracking-widest font-bold">
                        ~/logs/system.log
                    </span>
                </div>

                {{-- Conteúdo real --}}
                <div id="terminal-content" class="whitespace-pre-wrap break-words leading-relaxed text-green-400"></div>

            </div>
        </div>

        {{-- ======================= SCRIPTS ======================= --}}
        <script>
            const rawColored = @json($modalContentColored ?? '');

            function renderTerminal() {
                const container = document.getElementById('terminal-content')

                if (!rawColored) {
                    container.innerHTML = "<span class='text-gray-500'>Nenhum conteúdo encontrado.</span>"
                    return
                }

                const lines = rawColored.split('\n').map((line, i) => `
                    <div class="flex items-start">
                        <span class="text-gray-600 select-none w-12 text-right pr-3">${i + 1}</span>
                        <span class="flex-1">${line}</span>
                    </div>
                `).join('')

                container.innerHTML = lines + `<span class="blinking-cursor">█</span>`
            }

            function copyTerminalText() {
                navigator.clipboard.writeText(rawColored.replace(/<[^>]+>/g, ''))
            }

            function downloadTerminalText() {
                const blob = new Blob([rawColored.replace(/<[^>]+>/g, '')], { type: 'text/plain' })
                const url = URL.createObjectURL(blob)
                const a = document.createElement('a')
                a.href = url
                a.download = 'log.txt'
                a.click()
            }

            function scrollTopTerminal() {
                document.getElementById('terminal-container').scrollTop = 0
            }

            function scrollBottomTerminal() {
                const el = document.getElementById('terminal-container')
                el.scrollTop = el.scrollHeight
            }

            document.addEventListener('DOMContentLoaded', renderTerminal)
            document.addEventListener('livewire:navigated', renderTerminal)
        </script>

        <style>
            .blinking-cursor {
                animation: blink 0.9s step-end infinite;
                display: inline-block;
            }
            @keyframes blink {
                50% { opacity: 0; }
            }
        </style>

    </x-filament::modal>

    {{-- ======================= FILTROS ======================= --}}
    <x-filament::section>
        <x-filament-panels::form wire:submit="filtrar">
            {{ $this->form }}

            <div class="flex justify-center space-x-4 mt-4">
                <x-filament::button
                    color="primary"
                    icon="heroicon-o-funnel"
                    type="submit"
                    class="w-40"
                >
                    Filtrar
                </x-filament::button>

                <x-filament::button
                    color="danger"
                    icon="heroicon-o-trash"
                    wire:click="limparLogs"
                    type="button"
                    class="w-40"
                >
                    Limpar Logs
                </x-filament::button>
            </div>
        </x-filament-panels::form>
    </x-filament::section>


    {{-- ======================= PAGINAÇÃO SUPERIOR ======================= --}}
    <div class="flex justify-between items-center mt-6 mb-3">
        <div class="text-sm text-gray-500">
            {{ $this->paginatedLogs->firstItem() }} até {{ $this->paginatedLogs->lastItem() }}
            de {{ $this->paginatedLogs->total() }} resultados
        </div>
        <div>{{ $this->paginatedLogs->links() }}</div>
    </div>


    {{-- ======================= TABELA ======================= --}}
    <div class="overflow-x-auto bg-white rounded-lg shadow">

        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
            <tr>
                <th class="px-4 py-2 text-left text-xs font-medium">Data/Hora</th>
                <th class="px-4 py-2 text-left text-xs font-medium">Ambiente</th>
                <th class="px-4 py-2 text-left text-xs font-medium">Tipo</th>
                <th class="px-4 py-2 text-left text-xs font-medium">Mensagem</th>
            </tr>
            </thead>

            <tbody class="bg-white divide-y divide-gray-200">
            @forelse($this->paginatedLogs as $log)

                @php
                    $msg = $log['message'];
                    $isMultiline = strlen($msg) > 200 || str_contains($msg, "\n");
                @endphp

                <tr>
                    <td class="px-4 py-2 text-sm">{{ $log['datetime'] }}</td>
                    <td class="px-4 py-2 text-sm">{{ $log['env'] }}</td>
                    <td class="px-4 py-2 text-sm font-semibold
                        @class([
                            'text-red-600' => strtolower($log['level']) === 'error',
                            'text-red-700' => strtolower($log['level']) === 'critical',
                            'text-yellow-600' => strtolower($log['level']) === 'warning',
                            'text-green-600' => strtolower($log['level']) === 'info',
                            'text-blue-600' => strtolower($log['level']) === 'debug',
                        ])
                    ">
                        {{ strtoupper($log['level']) }}
                    </td>

                    <td class="px-4 py-2 text-sm text-gray-700">
                        @if ($isMultiline)
                            <div class="max-w-[450px]">
                                <div class="text-gray-700 whitespace-pre-wrap break-words line-clamp-5">
                                    {{ Str::limit($msg, 240) }}
                                </div>

                                <button
                                    class="text-primary-600 hover:text-primary-800 text-xs mt-1 underline"
                                    wire:click="abrirLogCompleto('{{ base64_encode($msg) }}')"
                                >
                                    Ver completo →
                                </button>
                            </div>
                        @else
                            {{ $msg }}
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


    {{-- ======================= PAGINAÇÃO INFERIOR ======================= --}}
    <div class="flex justify-between items-center mt-6 mb-2">
        <div class="text-sm text-gray-500">
            {{ $this->paginatedLogs->firstItem() }} até {{ $this->paginatedLogs->lastItem() }}
            de {{ $this->paginatedLogs->total() }} resultados
        </div>
        <div>{{ $this->paginatedLogs->links() }}</div>
    </div>

</x-filament::page>
