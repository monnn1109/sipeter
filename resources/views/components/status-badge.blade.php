@props(['status', 'size' => 'md'])

@php
    $statusValue = is_string($status) ? $status : (is_object($status) ? $status->value : 'pending');

    $statusConfig = [
        'submitted' => [
            'color' => 'bg-blue-100 text-blue-800 border-blue-200',
            'icon' => '📝',
            'label' => 'Baru Diajukan'
        ],
        'pending' => [
            'color' => 'bg-yellow-100 text-yellow-800 border-yellow-200',
            'icon' => '⏳',
            'label' => 'Menunggu Approval'
        ],
        'approved' => [
            'color' => 'bg-green-100 text-green-800 border-green-200',
            'icon' => '✅',
            'label' => 'Disetujui'
        ],
        'rejected' => [
            'color' => 'bg-red-100 text-red-800 border-red-200',
            'icon' => '❌',
            'label' => 'Ditolak'
        ],
        'processing' => [
            'color' => 'bg-indigo-100 text-indigo-800 border-indigo-200',
            'icon' => '⚙️',
            'label' => 'Sedang Diproses'
        ],
        'ready_for_pickup' => [
            'color' => 'bg-purple-100 text-purple-800 border-purple-200',
            'icon' => '📦',
            'label' => 'Siap Diambil'
        ],
        'picked_up' => [
            'color' => 'bg-teal-100 text-teal-800 border-teal-200',
            'icon' => '🎁',
            'label' => 'Sudah Diambil'
        ],
        'completed' => [
            'color' => 'bg-gray-100 text-gray-800 border-gray-200',
            'icon' => '✔️',
            'label' => 'Selesai'
        ],
        'ready' => [
            'color' => 'bg-purple-100 text-purple-800 border-purple-200',
            'icon' => '📦',
            'label' => 'Siap Diambil'
        ],

        'verification_requested' => [
            'color' => 'bg-blue-100 text-blue-800 border-blue-200',
            'icon' => '🔍',
            'label' => 'Menunggu Verifikasi'
        ],
        'verification_approved' => [
            'color' => 'bg-green-100 text-green-800 border-green-200',
            'icon' => '✅',
            'label' => 'Terverifikasi'
        ],
        'verification_rejected' => [
            'color' => 'bg-red-100 text-red-800 border-red-200',
            'icon' => '❌',
            'label' => 'Verifikasi Ditolak'
        ],

        'signature_requested' => [
            'color' => 'bg-orange-100 text-orange-800 border-orange-200',
            'icon' => '✍️',
            'label' => 'Menunggu TTD'
        ],
        'signature_uploaded' => [
            'color' => 'bg-cyan-100 text-cyan-800 border-cyan-200',
            'icon' => '📝',
            'label' => 'TTD Diupload'
        ],
        'signature_verified' => [
            'color' => 'bg-emerald-100 text-emerald-800 border-emerald-200',
            'icon' => '✅',
            'label' => 'TTD Terverifikasi'
        ],
    ];

    $sizeClasses = [
        'xs' => 'px-2 py-0.5 text-xs',
        'sm' => 'px-2 py-1 text-xs',
        'md' => 'px-3 py-1.5 text-sm',
        'lg' => 'px-4 py-2 text-base',
    ];

    $config = $statusConfig[$statusValue] ?? $statusConfig['pending'];
    $sizeClass = $sizeClasses[$size] ?? $sizeClasses['md'];
@endphp

<span {{ $attributes->merge(['class' => "inline-flex items-center gap-1 rounded-full font-bold border-2 {$config['color']} {$sizeClass} whitespace-nowrap"]) }}>
    <span class="text-base leading-none">{{ $config['icon'] }}</span>
    <span>{{ $config['label'] }}</span>
</span>
