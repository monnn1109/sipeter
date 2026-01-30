@extends('layouts.admin')

@section('title', 'Verifikasi Pending')

@section('content')

<div class="container mx-auto px-4 py-8">
    <div class="mb-6 flex items-center justify-between">
        <div>
            <h1 class="text-3xl font-bold text-gray-800 mb-2">📄 Verifikasi Pending (3-Level)</h1>
            <p class="text-gray-600">Menunggu verifikasi dari pejabat</p>
        </div>
        <div class="flex items-center gap-3">
            <div class="bg-yellow-100 px-4 py-2 rounded-lg">
                <p class="text-yellow-800 font-semibold">{{ $verifications->total() ?? 0 }} Pending</p>
            </div>
            <a href="{{ route('admin.verifications.index') }}"
               class="flex items-center gap-2 px-4 py-2 bg-gray-200 hover:bg-gray-300 text-gray-700 font-medium rounded-lg transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
                Kembali
            </a>
        </div>
    </div>

    {{-- ✅ NEW: Level Filter Tabs --}}
    <div class="bg-white rounded-lg shadow mb-6">
        <div class="border-b border-gray-200">
            <nav class="flex -mb-px">
                <a href="{{ route('admin.verifications.pending', ['level' => 'all']) }}"
                   class="px-6 py-3 border-b-2 font-medium text-sm {{ request('level', 'all') === 'all' ? 'border-yellow-500 text-yellow-600' : 'border-transparent text-gray-500 hover:text-gray-700' }}">
                    Semua Level
                </a>
                <a href="{{ route('admin.verifications.pending', ['level' => '1']) }}"
                   class="px-6 py-3 border-b-2 font-medium text-sm {{ request('level') === '1' ? 'border-blue-500 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700' }}">
                    📘 Level 1
                </a>
                <a href="{{ route('admin.verifications.pending', ['level' => '2']) }}"
                   class="px-6 py-3 border-b-2 font-medium text-sm {{ request('level') === '2' ? 'border-purple-500 text-purple-600' : 'border-transparent text-gray-500 hover:text-gray-700' }}">
                    📕 Level 2
                </a>
                <a href="{{ route('admin.verifications.pending', ['level' => '3']) }}"
                   class="px-6 py-3 border-b-2 font-medium text-sm {{ request('level') === '3' ? 'border-green-500 text-green-600' : 'border-transparent text-gray-500 hover:text-gray-700' }}">
                    📗 Level 3
                </a>
            </nav>
        </div>
    </div>

    <div class="bg-white rounded-xl shadow-lg overflow-hidden">
        @if($verifications->isEmpty())
            <div class="p-12 text-center">
                <svg class="w-24 h-24 mx-auto text-gray-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <h3 class="text-xl font-semibold text-gray-700 mb-2">Tidak Ada Verifikasi Pending</h3>
                <p class="text-gray-500">
                    @if(request('level'))
                        Tidak ada verifikasi pending untuk Level {{ request('level') }}
                    @else
                        Semua verifikasi sudah diproses
                    @endif
                </p>
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-50 border-b border-gray-200">
                        <tr>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase">Level</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase">No. Dokumen</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase">Pemohon</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase">Jenis</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase">Pejabat</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase">Dikirim</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase">Expires</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @foreach($verifications as $verification)
                        <tr class="hover:bg-gray-50 transition">
                            {{-- ✅ FIXED: Level Badge --}}
                            <td class="px-6 py-4">
                                @php
                                    $level = $verification->verification_level ?? 1;
                                    $levelConfig = [
                                        1 => ['bg' => 'bg-blue-100', 'text' => 'text-blue-800', 'label' => 'L1', 'name' => 'Ketua Akd'],
                                        2 => ['bg' => 'bg-purple-100', 'text' => 'text-purple-800', 'label' => 'L2', 'name' => 'Wakil Ketua 3'],
                                        3 => ['bg' => 'bg-green-100', 'text' => 'text-green-800', 'label' => 'L3', 'name' => 'Direktur'],
                                    ];
                                    $config = $levelConfig[$level] ?? ['bg' => 'bg-gray-100', 'text' => 'text-gray-800', 'label' => 'L?', 'name' => 'Unknown'];
                                @endphp
                                <div class="flex flex-col items-center">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold {{ $config['bg'] }} {{ $config['text'] }}">
                                        {{ $config['label'] }}
                                    </span>
                                    <span class="text-xs text-gray-500 mt-1">{{ $config['name'] }}</span>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <a href="{{ route('admin.documents.show', $verification->documentRequest->id) }}"
                                   class="font-mono font-semibold text-blue-600 hover:text-blue-800">
                                    {{ $verification->documentRequest->request_code }}
                                </a>
                            </td>
                            <td class="px-6 py-4">
                                <div>
                                    <p class="font-medium text-gray-900">{{ $verification->documentRequest->applicant_name }}</p>
                                    <p class="text-sm text-gray-500">{{ $verification->documentRequest->applicant_identifier }}</p>
                                </div>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-700">
                                {{ $verification->documentRequest->documentType->name }}
                            </td>
                            <td class="px-6 py-4">
                                <div>
                                    <p class="font-medium text-gray-900">{{ $verification->authority->name }}</p>
                                    <p class="text-xs text-gray-500">{{ $verification->authority->authority_type->label() }}</p>
                                </div>
                            </td>

                            {{-- ✅ FIXED: requested_at dengan null check --}}
                            <td class="px-6 py-4 text-sm text-gray-700">
                                @if($verification->sent_at)
                                    {{ $verification->sent_at->format('d M Y') }}
                                    <p class="text-xs text-gray-500">{{ $verification->sent_at->format('H:i') }}</p>
                                @else
                                    <span class="text-gray-400">-</span>
                                @endif
                            </td>

                            {{-- ✅ FIXED: expires_at dengan null check --}}
                            <td class="px-6 py-4 text-sm">
                                @if($verification->expires_at)
                                    @php
                                        $hoursLeft = now()->diffInHours($verification->expires_at, false);
                                    @endphp
                                    @if($hoursLeft > 24)
                                        <span class="text-green-600 font-medium">{{ now()->diffInDays($verification->expires_at) }} hari lagi</span>
                                    @elseif($hoursLeft > 0)
                                        <span class="text-yellow-600 font-medium">{{ $hoursLeft }} jam lagi</span>
                                    @else
                                        <span class="text-red-600 font-medium">Expired</span>
                                    @endif
                                    <p class="text-xs text-gray-500">{{ $verification->expires_at->format('d M Y H:i') }}</p>
                                @else
                                    <span class="text-gray-400">-</span>
                                @endif
                            </td>

                            <td class="px-6 py-4">
                                <span class="px-3 py-1 bg-yellow-100 text-yellow-800 text-xs font-semibold rounded-full">
                                    ⏳ Pending
                                </span>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            @if($verifications->hasPages())
            <div class="px-6 py-4 border-t border-gray-200">
                {{ $verifications->appends(request()->query())->links() }}
            </div>
            @endif
        @endif
    </div>

    <div class="mt-6 bg-blue-50 border border-blue-200 rounded-lg p-4">
        <div class="flex items-start">
            <svg class="w-5 h-5 text-blue-600 mr-3 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path>
            </svg>
            <div class="text-sm text-blue-800">
                <p class="font-medium mb-1">ℹ️ Informasi Verifikasi 3-Level:</p>
                <ul class="space-y-1">
                    <li>• Link verifikasi berlaku <strong>3 hari</strong> sejak dikirim per level</li>
                    <li>• Pejabat akan menerima <strong>WhatsApp otomatis</strong> dari sistem</li>
                    <li>• Setelah Level 1 approved, <strong>otomatis lanjut</strong> ke Level 2</li>
                    <li>• Setelah Level 2 approved, <strong>otomatis lanjut</strong> ke Level 3 (Final)</li>
                    <li>• Jika ditolak di level manapun, proses <strong>BERHENTI</strong></li>
                </ul>
            </div>
        </div>
    </div>
</div>
@endsection
