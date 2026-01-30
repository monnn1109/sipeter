@extends('layouts.admin')

@section('title', 'Verifikasi Disetujui - SIPETER Admin')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="mb-8 flex items-center justify-between">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">✅ Verifikasi Disetujui</h1>
            <p class="text-gray-600 mt-2">Daftar dokumen yang telah diverifikasi dan disetujui (3-Level Sequential)</p>
        </div>
        <a href="{{ route('admin.verifications.index') }}"
           class="flex items-center gap-2 px-4 py-2 bg-gray-200 hover:bg-gray-300 text-gray-700 font-medium rounded-lg transition-colors">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
            </svg>
            Kembali
        </a>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600 mb-1">Dokumen Disetujui</p>
                    <p class="text-3xl font-bold text-green-600">{{ $stats['total_approved'] }}</p>
                    <p class="text-xs text-gray-500 mt-1">Semua level lengkap</p>
                </div>
                <div class="bg-green-100 p-3 rounded-lg">
                    <span class="text-3xl">✅</span>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600 mb-1">Hari Ini</p>
                    <p class="text-3xl font-bold text-blue-600">{{ $stats['today'] }}</p>
                </div>
                <div class="bg-blue-100 p-3 rounded-lg">
                    <span class="text-3xl">📅</span>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600 mb-1">Minggu Ini</p>
                    <p class="text-3xl font-bold text-purple-600">{{ $stats['this_week'] }}</p>
                </div>
                <div class="bg-purple-100 p-3 rounded-lg">
                    <span class="text-3xl">📊</span>
                </div>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-lg shadow-lg overflow-hidden">
        @if($verifications->count() > 0)
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Dokumen
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Pemohon
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Verifikasi 3-Level
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Tanggal Selesai
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Waktu Proses
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Aksi
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @php
                            // Group verifications by document_request_id
                            $groupedVerifications = $verifications->groupBy('document_request_id');
                        @endphp

                        @foreach($groupedVerifications as $documentId => $docVerifications)
                            @php
                                // Get document info from first verification
                                $firstVerification = $docVerifications->first();
                                $document = $firstVerification->documentRequest;

                                // Get all 3 levels
                                $level1 = $docVerifications->firstWhere('verification_level', 1);
                                $level2 = $docVerifications->firstWhere('verification_level', 2);
                                $level3 = $docVerifications->firstWhere('verification_level', 3);

                                // Calculate total process time (from first request to last approval)
                                $allVerifications = $docVerifications->sortBy('created_at');
                                $firstRequest = $allVerifications->first()->created_at;
                                $lastApproval = $allVerifications->sortByDesc('verified_at')->first()->verified_at;

                                $totalSeconds = $firstRequest->diffInSeconds($lastApproval);
                                $totalMinutes = $firstRequest->diffInMinutes($lastApproval);
                                $totalHours = $firstRequest->diffInHours($lastApproval);
                                $totalDays = $firstRequest->diffInDays($lastApproval);

                                // Determine speed color
                                if ($totalHours < 2) {
                                    $speedColor = 'bg-green-100 text-green-800';
                                    $speedIcon = '⚡';
                                } elseif ($totalHours < 24) {
                                    $speedColor = 'bg-blue-100 text-blue-800';
                                    $speedIcon = '✓';
                                } elseif ($totalDays <= 3) {
                                    $speedColor = 'bg-yellow-100 text-yellow-800';
                                    $speedIcon = '○';
                                } else {
                                    $speedColor = 'bg-orange-100 text-orange-800';
                                    $speedIcon = '⊗';
                                }
                            @endphp

                            <tr class="hover:bg-gray-50">
                                {{-- Document Info --}}
                                <td class="px-6 py-4">
                                    <div>
                                        <p class="text-sm font-mono font-medium text-gray-900">
                                            {{ $document->document_code }}
                                        </p>
                                        <p class="text-sm text-gray-500">
                                            {{ $document->documentType->name }}
                                        </p>
                                    </div>
                                </td>

                                {{-- Applicant Info --}}
                                <td class="px-6 py-4">
                                    <div>
                                        <p class="text-sm font-medium text-gray-900">
                                            {{ $document->applicant_name }}
                                        </p>
                                        <p class="text-sm text-gray-500">
                                            {{ $document->applicant_identifier }}
                                        </p>
                                    </div>
                                </td>

                                {{-- 3-Level Verification Status --}}
                                <td class="px-6 py-4">
                                    <div class="space-y-2">
                                        {{-- Level 1 --}}
                                        <div class="flex items-center gap-2">
                                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-bold bg-blue-100 text-blue-800">
                                                L1
                                            </span>
                                            @if($level1)
                                                <span class="text-xs text-green-600 font-medium">✓</span>
                                                <span class="text-xs text-gray-700">{{ $level1->authority->name }}</span>
                                            @else
                                                <span class="text-xs text-gray-400">-</span>
                                            @endif
                                        </div>

                                        {{-- Level 2 --}}
                                        <div class="flex items-center gap-2">
                                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-bold bg-purple-100 text-purple-800">
                                                L2
                                            </span>
                                            @if($level2)
                                                <span class="text-xs text-green-600 font-medium">✓</span>
                                                <span class="text-xs text-gray-700">{{ $level2->authority->name }}</span>
                                            @else
                                                <span class="text-xs text-gray-400">-</span>
                                            @endif
                                        </div>

                                        {{-- Level 3 --}}
                                        <div class="flex items-center gap-2">
                                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-bold bg-green-100 text-green-800">
                                                L3
                                            </span>
                                            @if($level3)
                                                <span class="text-xs text-green-600 font-medium">✓</span>
                                                <span class="text-xs text-gray-700">{{ $level3->authority->name }}</span>
                                            @else
                                                <span class="text-xs text-gray-400">-</span>
                                            @endif
                                        </div>
                                    </div>
                                </td>

                                {{-- Completion Date --}}
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @if($lastApproval)
                                        <div class="text-sm">
                                            <p class="text-gray-900 font-medium">{{ $lastApproval->format('d/m/Y') }}</p>
                                            <p class="text-gray-500">{{ $lastApproval->format('H:i') }} WIB</p>
                                        </div>
                                    @else
                                        <span class="text-gray-400">-</span>
                                    @endif
                                </td>

                                {{-- Process Time --}}
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm">
                                        <span class="inline-flex items-center px-2.5 py-1 text-xs font-bold {{ $speedColor }} rounded-full whitespace-nowrap">
                                            {{ $speedIcon }}
                                            @if($totalSeconds < 60)
                                                {{ round($totalSeconds) }} detik
                                            @elseif($totalMinutes < 60)
                                                {{ round($totalMinutes) }} menit
                                            @elseif($totalHours < 24)
                                                @if($totalMinutes % 60 > 0)
                                                    {{ floor($totalHours) }} jam {{ round($totalMinutes % 60) }} menit
                                                @else
                                                    {{ round($totalHours) }} jam
                                                @endif
                                            @elseif($totalDays < 7)
                                                @if($totalHours % 24 > 0)
                                                    {{ $totalDays }} hari {{ floor($totalHours % 24) }} jam
                                                @else
                                                    {{ $totalDays }} hari
                                                @endif
                                            @else
                                                {{ $totalDays }} hari
                                            @endif
                                        </span>
                                        <p class="text-xs text-gray-500 mt-1">
                                            Mulai: {{ $firstRequest->format('d/m H:i') }}
                                        </p>
                                    </div>
                                </td>

                                {{-- Actions --}}
                                <td class="px-6 py-4 whitespace-nowrap text-sm">
                                    <a
                                        href="{{ route('admin.documents.show', $document->id) }}"
                                        class="text-blue-600 hover:text-blue-800 font-medium"
                                    >
                                        👁️ Detail
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="bg-gray-50 px-6 py-4">
                {{ $verifications->links() }}
            </div>
        @else
            <div class="text-center py-12">
                <svg class="mx-auto h-16 w-16 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <h3 class="mt-4 text-lg font-medium text-gray-900">Belum ada verifikasi disetujui</h3>
                <p class="mt-2 text-sm text-gray-500">
                    Verifikasi yang disetujui akan muncul di sini.
                </p>
            </div>
        @endif
    </div>

    {{-- Legend --}}
    <div class="mt-6 bg-blue-50 border border-blue-200 rounded-lg p-4">
        <div class="flex items-start">
            <svg class="w-5 h-5 text-blue-600 mr-3 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path>
            </svg>
            <div class="text-sm text-blue-800">
                <p class="font-medium mb-2">ℹ️ Keterangan Waktu Proses:</p>
                <div class="grid grid-cols-2 md:grid-cols-4 gap-2">
                    <div class="flex items-center gap-2">
                        <span class="px-2 py-1 bg-green-100 text-green-800 text-xs font-bold rounded-full">⚡</span>
                        <span class="text-xs">Sangat Cepat (&lt; 2 jam)</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <span class="px-2 py-1 bg-blue-100 text-blue-800 text-xs font-bold rounded-full">✓</span>
                        <span class="text-xs">Cepat (2-24 jam)</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <span class="px-2 py-1 bg-yellow-100 text-yellow-800 text-xs font-bold rounded-full">○</span>
                        <span class="text-xs">Normal (1-3 hari)</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <span class="px-2 py-1 bg-orange-100 text-orange-800 text-xs font-bold rounded-full">⊗</span>
                        <span class="text-xs">Lambat (&gt; 3 hari)</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
