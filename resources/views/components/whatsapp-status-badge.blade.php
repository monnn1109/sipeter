@props(['status'])

@php
$statusConfig = [
    'pending' => [
        'color' => 'yellow',
        'icon' => '⏳',
        'label' => 'Pending'
    ],
    'sent' => [
        'color' => 'blue',
        'icon' => '📤',
        'label' => 'Terkirim'
    ],
    'delivered' => [
        'color' => 'green',
        'icon' => '✅',
        'label' => 'Tersampaikan'
    ],
    'read' => [
        'color' => 'green',
        'icon' => '✓✓',
        'label' => 'Dibaca'
    ],
    'failed' => [
        'color' => 'red',
        'icon' => '❌',
        'label' => 'Gagal'
    ],
];

$config = $statusConfig[$status] ?? [
    'color' => 'gray',
    'icon' => '❓',
    'label' => ucfirst($status)
];

$colorClasses = [
    'yellow' => 'bg-yellow-100 text-yellow-800 border-yellow-200',
    'blue' => 'bg-blue-100 text-blue-800 border-blue-200',
    'green' => 'bg-green-100 text-green-800 border-green-200',
    'red' => 'bg-red-100 text-red-800 border-red-200',
    'gray' => 'bg-gray-100 text-gray-800 border-gray-200',
];

$classes = $colorClasses[$config['color']] ?? $colorClasses['gray'];
@endphp

<span {{ $attributes->merge(['class' => "inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold border {$classes}"]) }}>
    <span class="text-sm">{{ $config['icon'] }}</span>
    <span>{{ $config['label'] }}</span>
</span>
