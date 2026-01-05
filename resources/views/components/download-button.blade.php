@props([
    'documentRequest',
    'route',
    'size' => 'md',
    'fullWidth' => false
])

@php
    $sizeClasses = [
        'sm' => 'px-3 py-1.5 text-sm',
        'md' => 'px-4 py-2 text-base',
        'lg' => 'px-6 py-3 text-lg'
    ];

    $buttonClass = $sizeClasses[$size] ?? $sizeClasses['md'];
    $widthClass = $fullWidth ? 'w-full' : '';

    $canDownload = $documentRequest->canBeDownloaded();
    $hasFile = $documentRequest->hasFile();
    $isDownloaded = $documentRequest->isDownloaded();
    $downloadedAt = $documentRequest->getDownloadedAt();
@endphp

@if($canDownload)
    <a href="{{ $route }}"
       class="inline-flex items-center justify-center gap-2 {{ $buttonClass }} {{ $widthClass }} bg-green-600 hover:bg-green-700 text-white font-medium rounded-lg shadow-sm transition-colors focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M9 19l3 3m0 0l3-3m-3 3V10"></path>
        </svg>
        <span>Unduh Dokumen</span>
        @if($isDownloaded)
            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-green-800 text-white">
                Sudah Diunduh
            </span>
        @endif
    </a>
@elseif($hasFile && !$documentRequest->isReady())
    <button type="button"
            disabled
            class="inline-flex items-center justify-center gap-2 {{ $buttonClass }} {{ $widthClass }} bg-gray-300 text-gray-500 font-medium rounded-lg shadow-sm cursor-not-allowed">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
        </svg>
        <span>Dokumen Belum Siap</span>
    </button>
@else
    <div class="inline-flex items-center gap-2 {{ $buttonClass }} {{ $widthClass }} bg-yellow-50 text-yellow-800 border border-yellow-200 rounded-lg">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
        </svg>
        <span class="text-sm">Menunggu Admin Upload File</span>
    </div>
@endif

@if($hasFile && $documentRequest->file_uploaded_at)
    <div class="mt-2 text-xs text-gray-500">
        <div class="flex items-center gap-1">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
            </svg>
            <span>{{ $documentRequest->getFileName() }}</span>
        </div>
        <div class="flex items-center gap-1 mt-1">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
            <span>Diupload: {{ $documentRequest->file_uploaded_at->format('d M Y H:i') }}</span>
        </div>
        @if($isDownloaded && $downloadedAt)
            <div class="flex items-center gap-1 mt-1 text-green-600">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <span>Diunduh: {{ $downloadedAt->format('d M Y H:i') }}</span>
            </div>
        @endif
    </div>
@endif
