@props(['node', 'level' => 0])

@php
    $colors = [
        'border-l-blue-500',
        'border-l-green-500', 
        'border-l-purple-500',
        'border-l-orange-500',
    ];
    
    $bgColors = [
        'bg-blue-50 dark:bg-blue-900/20',
        'bg-green-50 dark:bg-green-900/20',
        'bg-purple-50 dark:bg-purple-900/20',
        'bg-orange-50 dark:bg-orange-900/20',
    ];
    
    $colorIndex = $level % 4;
    $indent = 'ml-' . ($level * 4);
    $borderColor = $colors[$colorIndex] ?? 'border-l-gray-300';
    $bgColor = $bgColors[$colorIndex] ?? 'bg-gray-50 dark:bg-gray-800';
    
    $icons = [
        'faculty' => '<svg class="w-5 h-5 text-blue-600 dark:text-blue-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>',
        'study_program' => '<svg class="w-5 h-5 text-green-600 dark:text-green-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>',
        'bureau' => '<svg class="w-5 h-5 text-purple-600 dark:text-purple-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>',
        'department' => '<svg class="w-5 h-5 text-orange-600 dark:text-orange-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.653-.125-1.273-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.653.125-1.273.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/></svg>',
        'library' => '<svg class="w-5 h-5 text-indigo-600 dark:text-indigo-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 9l3 3-3 3m5 0h3M5 20h14a2 2 0 002-2V6a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>',
        'quality_unit' => '<svg class="w-5 h-5 text-rose-600 dark:text-rose-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>',
    ];
    
    $icon = $icons[$node['type'] ?? 'unit'] ?? $icons['default'];
    $typeLabel = ucwords(str_replace('_', ' ', $node['type'] ?? 'unit'));
@endphp

<div class="{{ $indent }} {{ $level > 0 ? 'pl-4 ' . $borderColor : '' }}">
    <!-- Node Container -->
    <div class="flex items-center gap-3 p-3 rounded-lg {{ $bgColor }} hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors">
        <!-- Icon -->
        <div class="flex-shrink-0">
            {!! $icon !!}
        </div>
        
        <!-- Content -->
        <div class="flex-1 min-w-0">
            <div class="font-medium text-gray-900 dark:text-white">
                {{ $node['name'] }}
            </div>
            <div class="text-sm text-gray-500 dark:text-gray-400">
                <span>{{ $node['code'] }}</span>
                <span class="mx-2 text-gray-400">|</span>
                <span class="capitalize">{{ $typeLabel }}</span>
            </div>
        </div>
        
        <!-- Root Badge -->
        @if($level === 0)
            <span class="px-2 py-1 bg-blue-100 text-blue-800 text-xs font-medium rounded-full dark:bg-blue-900/30 dark:text-blue-300">
                Root
            </span>
        @endif
    </div>
    
    <!-- Children -->
    @if(!empty($node['children']))
        <div class="mt-2 space-y-2">
            @foreach($node['children'] as $child)
                @include('filament.widgets.partials.organization-tree-node', [
                    'node' => $child,
                    'level' => $level + 1
                ])
            @endforeach
        </div>
    @endif
</div>
