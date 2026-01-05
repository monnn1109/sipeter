@props(['status'])

@php
    use App\Enums\SignatureStatus;

    if ($status instanceof SignatureStatus) {
        $statusValue = $status->value;
        $label = $status->label();
    } else {
        $statusValue = $status;
        $label = match($status) {
            'requested' => 'Menunggu TTD',
            'uploaded' => 'TTD Sudah Diupload',
            'verified' => 'TTD Terverifikasi',
            'rejected' => 'TTD Ditolak',
            default => ucfirst($status)
        };
    }

    $config = match($statusValue) {
        'requested' => [
            'color' => 'yellow',
            'icon' => '⏳',
            'class' => 'bg-yellow-100 text-yellow-800 border-yellow-200'
        ],
        'uploaded' => [
            'color' => 'blue',
            'icon' => '📤',
            'class' => 'bg-blue-100 text-blue-800 border-blue-200'
        ],
        'verified' => [
            'color' => 'green',
            'icon' => '✅',
            'class' => 'bg-green-100 text-green-800 border-green-200'
        ],
        'rejected' => [
            'color' => 'red',
            'icon' => '❌',
            'class' => 'bg-red-100 text-red-800 border-red-200'
        ],
        default => [
            'color' => 'gray',
            'icon' => '●',
            'class' => 'bg-gray-100 text-gray-800 border-gray-200'
        ]
    };
@endphp

<span
    {{ $attributes->merge([
        'class' => 'inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium border ' . $config['class']
    ]) }}
>
    <span class="mr-1">{{ $config['icon'] }}</span>
    {{ $label }}
</span>
