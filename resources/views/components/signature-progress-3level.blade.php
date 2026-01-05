@php
    // 🔥 FIX PROGRESS TTD 0% - DETECT STATUS CORRECTLY
    $signatures = $document->signatures ?? collect();

    // Get each level signature by signature_level column
    $sig1 = $signatures->where('signature_level', 1)->first();
    $sig2 = $signatures->where('signature_level', 2)->first();
    $sig3 = $signatures->where('signature_level', 3)->first();

    // 🔥 FIX: Handle both string & enum status
    $sig1Status = 'locked';
    if ($sig1) {
        $status1 = is_string($sig1->status) ? $sig1->status : $sig1->status->value;
        $sig1Status = ($status1 === 'verified') ? 'verified' : (($status1 === 'rejected') ? 'rejected' : 'uploaded');
    }

    $sig2Status = 'locked';
    if ($sig2) {
        $status2 = is_string($sig2->status) ? $sig2->status : $sig2->status->value;
        $sig2Status = ($status2 === 'verified') ? 'verified' : (($status2 === 'rejected') ? 'rejected' : 'uploaded');
    } elseif ($sig1Status === 'verified') {
        $sig2Status = 'pending';
    }

    $sig3Status = 'locked';
    if ($sig3) {
        $status3 = is_string($sig3->status) ? $sig3->status : $sig3->status->value;
        $sig3Status = ($status3 === 'verified') ? 'verified' : (($status3 === 'rejected') ? 'rejected' : 'uploaded');
    } elseif ($sig2Status === 'verified') {
        $sig3Status = 'pending';
    }

    // 🔥 FIX: CALCULATE PROGRESS
    $progress = 0;
    if ($sig1Status === 'verified') $progress += 33.33;
    if ($sig2Status === 'verified') $progress += 33.33;
    if ($sig3Status === 'verified') $progress += 33.34;

    $compact = $compact ?? false;
    $showDetails = $showDetails ?? true;
@endphp

<div class="signature-progress-container {{ $compact ? 'compact-mode' : '' }}">

    <!-- Progress Bar -->
    <div class="mb-4">
        <div class="flex justify-between items-center mb-2">
            <h3 class="text-sm font-semibold text-gray-700">
                ✍️ Progress Tanda Tangan Digital 3-Level
            </h3>
            <span class="text-sm font-bold {{ $progress >= 100 ? 'text-green-600' : 'text-indigo-600' }}">
                {{ number_format($progress, 0) }}%
            </span>
        </div>

        <div class="w-full bg-gray-200 rounded-full h-2.5 overflow-hidden">
            <div class="bg-gradient-to-r from-indigo-500 to-purple-500 h-2.5 rounded-full transition-all duration-500 ease-out"
                 style="width: {{ $progress }}%"></div>
        </div>
    </div>

    <!-- Visual Progress -->
    <div class="relative">
        <div class="absolute top-6 left-6 right-6 h-0.5 bg-gray-200 {{ $compact ? 'hidden' : '' }}"></div>

        <div class="grid grid-cols-3 gap-4 relative">

            <!-- Level 1 -->
            <div class="flex flex-col items-center">
                <div class="relative z-10 mb-3">
                    @if($sig1Status === 'verified')
                        <div class="w-12 h-12 bg-indigo-500 rounded-full flex items-center justify-center shadow-lg ring-4 ring-indigo-100">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                        </div>
                    @elseif($sig1Status === 'rejected')
                        <div class="w-12 h-12 bg-red-500 rounded-full flex items-center justify-center shadow-lg ring-4 ring-red-100">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </div>
                    @elseif($sig1Status === 'uploaded')
                        <div class="w-12 h-12 bg-blue-500 rounded-full flex items-center justify-center shadow-lg ring-4 ring-blue-100 animate-pulse">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path>
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
                    <div class="text-xs font-semibold text-gray-500">TTD LEVEL 1</div>
                    <div class="text-sm font-bold text-gray-800">Ketua Akademik</div>

                    @if($showDetails && !$compact && $sig1)
                        <div class="text-xs text-gray-600 mt-1">
                            {{ $sig1->signatureAuthority->name ?? $sig1->authority->name ?? 'N/A' }}
                        </div>
                        @if($sig1->uploaded_at)
                            <div class="text-xs text-gray-500">
                                Upload: {{ $sig1->uploaded_at->format('d/m/Y H:i') }}
                            </div>
                        @endif
                        @if($sig1->verified_at)
                            <div class="text-xs text-green-600 font-medium">
                                ✅ Verified: {{ $sig1->verified_at->format('d/m/Y H:i') }}
                            </div>
                        @endif
                    @endif

                    <div class="mt-2">
                        @if($sig1Status === 'verified')
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-indigo-100 text-indigo-800">
                                ✓ Terverifikasi
                            </span>
                        @elseif($sig1Status === 'rejected')
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                ✗ Ditolak
                            </span>
                        @elseif($sig1Status === 'uploaded')
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                ⏳ Pending Review
                            </span>
                        @else
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-600">
                                🔒 Terkunci
                            </span>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Level 2 -->
            <div class="flex flex-col items-center">
                <div class="relative z-10 mb-3">
                    @if($sig2Status === 'verified')
                        <div class="w-12 h-12 bg-indigo-500 rounded-full flex items-center justify-center shadow-lg ring-4 ring-indigo-100">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                        </div>
                    @elseif($sig2Status === 'rejected')
                        <div class="w-12 h-12 bg-red-500 rounded-full flex items-center justify-center shadow-lg ring-4 ring-red-100">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </div>
                    @elseif($sig2Status === 'uploaded')
                        <div class="w-12 h-12 bg-blue-500 rounded-full flex items-center justify-center shadow-lg ring-4 ring-blue-100 animate-pulse">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path>
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
                    <div class="text-xs font-semibold text-gray-500">TTD LEVEL 2</div>
                    <div class="text-sm font-bold text-gray-800">Wakil Ketua 3</div>

                    @if($showDetails && !$compact && $sig2)
                        <div class="text-xs text-gray-600 mt-1">
                            {{ $sig2->signatureAuthority->name ?? $sig2->authority->name ?? 'N/A' }}
                        </div>
                        @if($sig2->uploaded_at)
                            <div class="text-xs text-gray-500">
                                Upload: {{ $sig2->uploaded_at->format('d/m/Y H:i') }}
                            </div>
                        @endif
                        @if($sig2->verified_at)
                            <div class="text-xs text-green-600 font-medium">
                                ✅ Verified: {{ $sig2->verified_at->format('d/m/Y H:i') }}
                            </div>
                        @endif
                    @endif

                    <div class="mt-2">
                        @if($sig2Status === 'verified')
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-indigo-100 text-indigo-800">
                                ✓ Terverifikasi
                            </span>
                        @elseif($sig2Status === 'rejected')
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                ✗ Ditolak
                            </span>
                        @elseif($sig2Status === 'uploaded')
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                ⏳ Pending Review
                            </span>
                        @else
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-600">
                                🔒 Terkunci
                            </span>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Level 3 -->
            <div class="flex flex-col items-center">
                <div class="relative z-10 mb-3">
                    @if($sig3Status === 'verified')
                        <div class="w-12 h-12 bg-indigo-500 rounded-full flex items-center justify-center shadow-lg ring-4 ring-indigo-100">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                        </div>
                    @elseif($sig3Status === 'rejected')
                        <div class="w-12 h-12 bg-red-500 rounded-full flex items-center justify-center shadow-lg ring-4 ring-red-100">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </div>
                    @elseif($sig3Status === 'uploaded')
                        <div class="w-12 h-12 bg-blue-500 rounded-full flex items-center justify-center shadow-lg ring-4 ring-blue-100 animate-pulse">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path>
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
                    <div class="text-xs font-semibold text-gray-500">TTD LEVEL 3</div>
                    <div class="text-sm font-bold text-gray-800">Direktur</div>

                    @if($showDetails && !$compact && $sig3)
                        <div class="text-xs text-gray-600 mt-1">
                            {{ $sig3->signatureAuthority->name ?? $sig3->authority->name ?? 'N/A' }}
                        </div>
                        @if($sig3->uploaded_at)
                            <div class="text-xs text-gray-500">
                                Upload: {{ $sig3->uploaded_at->format('d/m/Y H:i') }}
                            </div>
                        @endif
                        @if($sig3->verified_at)
                            <div class="text-xs text-green-600 font-medium">
                                ✅ Verified: {{ $sig3->verified_at->format('d/m/Y H:i') }}
                            </div>
                        @endif
                    @endif

                    <div class="mt-2">
                        @if($sig3Status === 'verified')
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-indigo-100 text-indigo-800">
                                ✓ Terverifikasi
                            </span>
                        @elseif($sig3Status === 'rejected')
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                ✗ Ditolak
                            </span>
                        @elseif($sig3Status === 'uploaded')
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                ⏳ Pending Review
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

    @if($progress >= 100)
        <div class="mt-6 p-4 bg-indigo-50 border-l-4 border-indigo-500 rounded-r-lg">
            <div class="flex items-center">
                <svg class="w-5 h-5 text-indigo-500 mr-2" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                </svg>
                <div>
                    <p class="text-sm font-semibold text-indigo-800">
                        🎉 Semua TTD Terverifikasi!
                    </p>
                    <p class="text-xs text-indigo-700 mt-1">
                        Siap untuk embed TTD manual ke PDF dan upload dokumen final.
                    </p>
                </div>
            </div>
        </div>
    @endif

</div>
