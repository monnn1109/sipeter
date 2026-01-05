@php
    $currentStep = $document->current_verification_step ?? 0;
    $verifications = $document->verifications ?? collect();

    $level1 = $verifications->where('verification_level', 1)->first();
    $level2 = $verifications->where('verification_level', 2)->first();
    $level3 = $verifications->where('verification_level', 3)->first();

    $level1Status = $level1
        ? ($level1->decision === 'approved' ? 'approved' : ($level1->decision === 'rejected' ? 'rejected' : 'pending'))
        : ($currentStep >= 1 ? 'pending' : 'locked');

    $level2Status = $level2
        ? ($level2->decision === 'approved' ? 'approved' : ($level2->decision === 'rejected' ? 'rejected' : 'pending'))
        : ($currentStep >= 2 ? 'pending' : 'locked');

    $level3Status = $level3
        ? ($level3->decision === 'approved' ? 'approved' : ($level3->decision === 'rejected' ? 'rejected' : 'pending'))
        : ($currentStep >= 3 ? 'pending' : 'locked');

    $progress = 0;
    if ($level1Status === 'approved') $progress += 33.33;
    if ($level2Status === 'approved') $progress += 33.33;
    if ($level3Status === 'approved') $progress += 33.34;

    $compact = $compact ?? false;
    $showDetails = $showDetails ?? true;
@endphp

<div class="verification-progress-container {{ $compact ? 'compact-mode' : '' }}">

    <div class="mb-4">
        <div class="flex justify-between items-center mb-2">
            <h3 class="text-sm font-semibold text-gray-700">
                Progress Verifikasi 3-Level
            </h3>
            <span class="text-sm font-bold {{ $progress === 100 ? 'text-green-600' : 'text-blue-600' }}">
                {{ number_format($progress, 0) }}%
            </span>
        </div>

        <div class="w-full bg-gray-200 rounded-full h-2.5 overflow-hidden">
            <div class="bg-gradient-to-r from-blue-500 to-green-500 h-2.5 rounded-full transition-all duration-500 ease-out"
                 style="width: {{ $progress }}%"></div>
        </div>
    </div>

    <div class="relative">

        <div class="absolute top-6 left-6 right-6 h-0.5 bg-gray-200 {{ $compact ? 'hidden' : '' }}"></div>

        <div class="grid grid-cols-3 gap-4 relative">

            <div class="flex flex-col items-center">
                <div class="relative z-10 mb-3">
                    @if($level1Status === 'approved')
                        <div class="w-12 h-12 bg-green-500 rounded-full flex items-center justify-center shadow-lg ring-4 ring-green-100 transition-all duration-300">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                        </div>
                    @elseif($level1Status === 'rejected')
                        <div class="w-12 h-12 bg-red-500 rounded-full flex items-center justify-center shadow-lg ring-4 ring-red-100">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </div>
                    @elseif($level1Status === 'pending')
                        <div class="w-12 h-12 bg-yellow-500 rounded-full flex items-center justify-center shadow-lg ring-4 ring-yellow-100 animate-pulse">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                    @else
                        <div class="w-12 h-12 bg-gray-300 rounded-full flex items-center justify-center shadow">
                            <svg class="w-6 h-6 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                            </svg>
                        </div>
                    @endif
                </div>

                <div class="text-center {{ $compact ? 'space-y-1' : 'space-y-2' }}">
                    <div class="text-xs font-semibold text-gray-500">LEVEL 1</div>
                    <div class="text-sm font-bold text-gray-800">Ketua Akademik</div>

                    @if($showDetails && !$compact)
                        @if($level1)
                            <div class="text-xs text-gray-600 mt-1">
                                {{ $level1->authority->name ?? 'N/A' }}
                            </div>
                            @if($level1->verified_at)
                                <div class="text-xs text-gray-500">
                                    {{ $level1->verified_at->format('d M Y, H:i') }}
                                </div>
                            @endif
                        @else
                            <div class="text-xs text-gray-400 italic">Belum diverifikasi</div>
                        @endif
                    @endif

                    <div class="mt-2">
                        @if($level1Status === 'approved')
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                ✓ Disetujui
                            </span>
                        @elseif($level1Status === 'rejected')
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                ✗ Ditolak
                            </span>
                        @elseif($level1Status === 'pending')
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                ⏳ Pending
                            </span>
                        @else
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-600">
                                🔒 Terkunci
                            </span>
                        @endif
                    </div>
                </div>
            </div>

            <div class="flex flex-col items-center">
                <div class="relative z-10 mb-3">
                    @if($level2Status === 'approved')
                        <div class="w-12 h-12 bg-green-500 rounded-full flex items-center justify-center shadow-lg ring-4 ring-green-100 transition-all duration-300">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                        </div>
                    @elseif($level2Status === 'rejected')
                        <div class="w-12 h-12 bg-red-500 rounded-full flex items-center justify-center shadow-lg ring-4 ring-red-100">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </div>
                    @elseif($level2Status === 'pending')
                        <div class="w-12 h-12 bg-yellow-500 rounded-full flex items-center justify-center shadow-lg ring-4 ring-yellow-100 animate-pulse">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                    @else
                        <div class="w-12 h-12 bg-gray-300 rounded-full flex items-center justify-center shadow">
                            <svg class="w-6 h-6 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                            </svg>
                        </div>
                    @endif
                </div>

                <div class="text-center {{ $compact ? 'space-y-1' : 'space-y-2' }}">
                    <div class="text-xs font-semibold text-gray-500">LEVEL 2</div>
                    <div class="text-sm font-bold text-gray-800">Wakil Ketua 3</div>

                    @if($showDetails && !$compact)
                        @if($level2)
                            <div class="text-xs text-gray-600 mt-1">
                                {{ $level2->authority->name ?? 'N/A' }}
                            </div>
                            @if($level2->verified_at)
                                <div class="text-xs text-gray-500">
                                    {{ $level2->verified_at->format('d M Y, H:i') }}
                                </div>
                            @endif
                        @else
                            <div class="text-xs text-gray-400 italic">Belum diverifikasi</div>
                        @endif
                    @endif

                    <div class="mt-2">
                        @if($level2Status === 'approved')
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                ✓ Disetujui
                            </span>
                        @elseif($level2Status === 'rejected')
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                ✗ Ditolak
                            </span>
                        @elseif($level2Status === 'pending')
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                ⏳ Pending
                            </span>
                        @else
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-600">
                                🔒 Terkunci
                            </span>
                        @endif
                    </div>
                </div>
            </div>

            <div class="flex flex-col items-center">
                <div class="relative z-10 mb-3">
                    @if($level3Status === 'approved')
                        <div class="w-12 h-12 bg-green-500 rounded-full flex items-center justify-center shadow-lg ring-4 ring-green-100 transition-all duration-300">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                        </div>
                    @elseif($level3Status === 'rejected')
                        <div class="w-12 h-12 bg-red-500 rounded-full flex items-center justify-center shadow-lg ring-4 ring-red-100">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </div>
                    @elseif($level3Status === 'pending')
                        <div class="w-12 h-12 bg-yellow-500 rounded-full flex items-center justify-center shadow-lg ring-4 ring-yellow-100 animate-pulse">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                    @else
                        <div class="w-12 h-12 bg-gray-300 rounded-full flex items-center justify-center shadow">
                            <svg class="w-6 h-6 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                            </svg>
                        </div>
                    @endif
                </div>

                <div class="text-center {{ $compact ? 'space-y-1' : 'space-y-2' }}">
                    <div class="text-xs font-semibold text-gray-500">LEVEL 3</div>
                    <div class="text-sm font-bold text-gray-800">Direktur</div>

                    @if($showDetails && !$compact)
                        @if($level3)
                            <div class="text-xs text-gray-600 mt-1">
                                {{ $level3->authority->name ?? 'N/A' }}
                            </div>
                            @if($level3->verified_at)
                                <div class="text-xs text-gray-500">
                                    {{ $level3->verified_at->format('d M Y, H:i') }}
                                </div>
                            @endif
                        @else
                            <div class="text-xs text-gray-400 italic">Belum diverifikasi</div>
                        @endif
                    @endif

                    <div class="mt-2">
                        @if($level3Status === 'approved')
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                ✓ Disetujui
                            </span>
                        @elseif($level3Status === 'rejected')
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                ✗ Ditolak
                            </span>
                        @elseif($level3Status === 'pending')
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                ⏳ Pending
                            </span>
                        @else
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-600">
                                🔒 Terkunci
                            </span>
                        @endif
                    </div>
                </div>
            </div>

        </div>
    </div>

    @if($progress === 100)
        <div class="mt-6 p-4 bg-green-50 border-l-4 border-green-500 rounded-r-lg">
            <div class="flex items-center">
                <svg class="w-5 h-5 text-green-500 mr-2" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                </svg>
                <div>
                    <p class="text-sm font-semibold text-green-800">
                        🎉 Semua Verifikasi Selesai!
                    </p>
                    <p class="text-xs text-green-700 mt-1">
                        Dokumen telah diverifikasi oleh semua pejabat. Siap untuk proses penandatanganan.
                    </p>
                </div>
            </div>
        </div>
    @endif

    @if(in_array('rejected', [$level1Status, $level2Status, $level3Status]))
        @php
            $rejectedLevel = $level1Status === 'rejected' ? $level1 : ($level2Status === 'rejected' ? $level2 : $level3);
            $rejectedLevelNum = $level1Status === 'rejected' ? 1 : ($level2Status === 'rejected' ? 2 : 3);
        @endphp
        <div class="mt-6 p-4 bg-red-50 border-l-4 border-red-500 rounded-r-lg">
            <div class="flex items-start">
                <svg class="w-5 h-5 text-red-500 mr-2 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path>
                </svg>
                <div class="flex-1">
                    <p class="text-sm font-semibold text-red-800">
                        ❌ Ditolak di Level {{ $rejectedLevelNum }}
                    </p>
                    @if($rejectedLevel && $rejectedLevel->notes)
                        <p class="text-xs text-red-700 mt-1">
                            <strong>Alasan:</strong> {{ $rejectedLevel->notes }}
                        </p>
                    @endif
                    <p class="text-xs text-red-600 mt-2 font-medium">
                        ⚠️ Proses verifikasi dihentikan.
                    </p>
                </div>
            </div>
        </div>
    @endif

</div>

<style>
    .verification-progress-container.compact-mode {
        padding: 1rem;
    }

    .verification-progress-container.compact-mode .grid {
        gap: 0.5rem;
    }

    .verification-progress-container.compact-mode .w-12 {
        width: 2.5rem;
        height: 2.5rem;
    }

    .verification-progress-container.compact-mode .text-sm {
        font-size: 0.75rem;
    }
</style>
