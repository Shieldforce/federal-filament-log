<x-filament-panels::page>
    <x-filament::section>
        <x-filament-panels::form wire:submit="filtrar">
            {{ $this->filtroForm }}
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

    @if($result)
        <div class="overflow-x-auto">
            <table class="min-w-full w-full divide-y divide-gray-200 dark:divide-gray-700">
                <thead class="bg-gray-100 dark:bg-gray-800">
                <tr>
                    <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                        Número
                    </th>
                    <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                       Letra
                    </th>
                </tr>
                </thead>
                <tbody class="bg-white dark:bg-gray-900 divide-y divide-gray-200 dark:divide-gray-700">
                @foreach($result as $item)
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap text-center text-sm text-gray-900 dark:text-gray-300">
                            {{ $item["numero"] }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-center text-sm text-gray-900 dark:text-gray-300">
                            {{ $item["letra"] }}
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
    @endif
</x-filament-panels::page>
