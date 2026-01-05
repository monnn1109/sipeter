@props(['signature', 'showDetails' => false])

<div {{ $attributes->merge(['class' => 'signature-preview-container']) }}>
    @if($signature->hasSignatureFile())
        <div class="bg-gray-50 border-2 border-gray-300 rounded-lg p-6 mb-4">
            <div class="flex items-center justify-center">
                <img
                    src="{{ $signature->signature_url }}"
                    alt="Tanda Tangan {{ $signature->signatureAuthority->name }}"
                    class="max-w-full h-auto max-h-64 object-contain"
                    onerror="this.onerror=null; this.src='{{ asset('images/no-signature.png') }}';"
                >
            </div>
        </div>

        <div class="grid grid-cols-2 gap-4 text-sm mb-4">
            <div class="bg-blue-50 border border-blue-200 rounded-lg p-3">
                <p class="text-blue-600 font-medium mb-1">📁 File Info</p>
                <p class="text-gray-700">
                    <span class="font-medium">Tipe:</span> {{ strtoupper($signature->file_type ?? 'PNG') }}<br>
                    <span class="font-medium">Ukuran:</span> {{ $signature->file_size_formatted }}
                </p>
            </div>

            <div class="bg-green-50 border border-green-200 rounded-lg p-3">
                <p class="text-green-600 font-medium mb-1">⏰ Upload Time</p>
                <p class="text-gray-700">
                    {{ $signature->uploaded_at?->format('d M Y') }}<br>
                    {{ $signature->uploaded_at?->format('H:i') }} WIB
                </p>
            </div>
        </div>

        @if($showDetails)
            <div class="border-t pt-4 space-y-3 text-sm">
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <p class="font-medium text-gray-600">Pejabat:</p>
                        <p class="text-gray-900">{{ $signature->signatureAuthority->name }}</p>
                        <p class="text-gray-500 text-xs">{{ $signature->signatureAuthority->position }}</p>
                    </div>
                    <div>
                        <p class="font-medium text-gray-600">Status:</p>
                        <div class="mt-1">
                            <x-signature-status-badge :status="$signature->status" />
                        </div>
                    </div>
                </div>

                @if($signature->verification_notes)
                    <div class="bg-blue-50 border border-blue-200 rounded-lg p-3">
                        <p class="font-medium text-blue-900 mb-1">📝 Catatan Verifikasi:</p>
                        <p class="text-blue-800 text-sm">{{ $signature->verification_notes }}</p>
                    </div>
                @endif

                @if($signature->rejection_reason)
                    <div class="bg-red-50 border border-red-200 rounded-lg p-3">
                        <p class="font-medium text-red-900 mb-1">⚠️ Alasan Penolakan:</p>
                        <p class="text-red-800 text-sm">{{ $signature->rejection_reason }}</p>
                    </div>
                @endif

                <div class="flex gap-2 pt-2">
                    <a
                        href="{{ route('admin.signatures.verify-form', $signature->id) }}"
                        class="flex-1 px-4 py-2 bg-blue-600 text-white text-center rounded-lg hover:bg-blue-700 transition text-sm font-medium"
                    >
                        🔍 Detail Lengkap
                    </a>
                    @if($signature->hasSignatureFile())
                        <a
                            href="{{ route('admin.signatures.download', $signature->id) }}"
                            class="px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition text-sm font-medium"
                            download
                        >
                            📥 Download
                        </a>
                    @endif
                </div>
            </div>
        @endif

    @else
        <div class="bg-gray-50 border-2 border-dashed border-gray-300 rounded-lg p-8 text-center">
            <svg class="mx-auto h-16 w-16 text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
            </svg>
            <p class="text-gray-600 font-medium mb-1">Belum ada tanda tangan</p>
            <p class="text-gray-500 text-sm">
                @if($signature->status->value === 'requested')
                    Menunggu pejabat mengupload tanda tangan
                @else
                    File tanda tangan tidak tersedia
                @endif
            </p>
        </div>
    @endif
</div>

@push('styles')
<style>
    .signature-preview-container img {
        image-rendering: -webkit-optimize-contrast;
        image-rendering: crisp-edges;
    }
</style>
@endpush
