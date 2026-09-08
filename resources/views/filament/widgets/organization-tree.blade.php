@props(['tree'])

<div class="filament-widgets-organization-tree">
    @if($tree)
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 dark:border-gray-700">
            <!-- Header -->
            <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white">
                    Struktur Organisasi
                </h3>
            </div>

            <!-- Tree Content -->
            <div class="p-6">
                @include('filament.widgets.partials.organization-tree-node', ['node' => $tree, 'level' => 0])
            </div>
        </div>
    @else
        <div class="bg-gray-50 rounded-xl border border-gray-200 dark:bg-gray-800 dark:border-gray-700 p-6 text-center">
            <p class="text-gray-500 dark:text-gray-400">
                Tidak ada struktur organisasi yang ditemukan.
            </p>
        </div>
    @endif
</div>
