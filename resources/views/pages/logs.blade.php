<x-filament::page>

    <x-filament::modal
        id="modal-log"
        width="7xl"
        heading="Terminal de Logs"
        icon="heroicon-o-command-line"
        close-button
    >
        <div class="p-0">

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

            <div id="terminal-container"
                 class="bg-black text-green-400 p-4 rounded-lg shadow-2xl max-h-[75vh] overflow-y-auto font-mono text-sm border border-green-500/40"
                 style="box-shadow: 0 0 10px rgba(0,255,100,0.4), inset 0 0 20px rgba(0,255,100,0.1);">

                {{-- Cabeçalho do terminal --}}
                <div class="flex items-center space-x-2 mb-4">
                    <div class="w-3 h-3 rounded-full bg-red-500"></div>
                    <div class="w-3 h-3 rounded-full bg-yellow-500"></div>
                    <div class="w-3 h-3 rounded-full bg-green-500"></div>

                    <span class="ml-3 text-green-400 tracking-widest font-bold">
                    ~/logs/system.log
                </span>
                </div>

                {{-- Conteúdo com linhas numeradas --}}
                <pre id="terminal-content"
                     class="whitespace-pre-wrap break-words leading-relaxed text-green-400">
            </pre>

            </div>
        </div>

        <script>
            const rawColored = @json($modalContentColored ?? '');

            function renderTerminal() {
                const container = document.getElementById('terminal-content')

                const lines = rawColored.split('\n').map((line, index) => {
                    return `
                <div class="flex">
                    <span class="text-gray-600 select-none w-12 text-right pr-3">${index + 1}</span>
                    <span class="flex-1">${line}</span>
                </div>`
                }).join('')

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
            document.addEventListener('open-modal', renderTerminal)
        </script>

        <style>
            .blinking-cursor {
                animation: blink 0.9s step-end infinite;
                display: inline-block;
                margin-left: 4px;
            }

            @keyframes blink {
                from, to {
                    opacity: 1;
                }
                50% {
                    opacity: 0;
                }
            }
        </style>

    </x-filament::modal>


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
        <div>{{ $this->paginatedLogs->links() }}</div>
    </div>


    {{-- Tabela --}}
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
                    $message = $log['message'];
                    $isMultiline = str_contains($message, "\n")
                        || strlen($message) > 200
                        || Str::startsWith(trim($message), ['[', '{']);
                @endphp

                <tr>
                    <td class="px-4 py-2 text-sm">{{ $log['datetime'] }}</td>
                    <td class="px-4 py-2 text-sm">{{ $log['env'] }}</td>

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

                    <td class="px-4 py-2 text-sm text-gray-700">

                        @if ($isMultiline)
                            <div class="max-w-[450px]">
                                <div class="text-gray-700 whitespace-pre-wrap break-words line-clamp-5">
                                    {{ Str::limit($message, 240) }}
                                </div>

                                <button
                                    class="text-primary-600 hover:text-primary-800 text-xs mt-1 underline"
                                    wire:click="abrirLogCompleto('{{ base64_encode($message) }}')"
                                >
                                    Ver completo →
                                </button>
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
        <div>{{ $this->paginatedLogs->links() }}</div>
    </div>

</x-filament::page>
