@extends('layouts.guest')

@section('title', 'Verifikasi Dokumen')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-blue-50 to-indigo-100 py-12 px-4">
    <div class="max-w-3xl mx-auto">
        @php
            $level = $verification->verification_level ?? 1;

            $levelConfig = match($level) {
                1 => [
                    'color' => 'blue',
                    'name' => 'Ketua Akademik',
                    'progress' => 33,
                    'iconColor' => 'text-blue-600',
                    'headerClass' => 'bg-gradient-to-r from-blue-600 to-blue-700',
                    'progressClass' => 'bg-gradient-to-r from-blue-500 to-blue-600',
                    'borderColor' => 'border-blue-500',
                    'bgColor' => 'bg-blue-50',
                    'textColor' => 'text-blue-800',
                    'buttonClass' => 'bg-blue-600 hover:bg-blue-700',
                ],
                2 => [
                    'color' => 'purple',
                    'name' => 'Wakil Ketua 3',
                    'progress' => 66,
                    'iconColor' => 'text-purple-600',
                    'headerClass' => 'bg-gradient-to-r from-purple-600 to-purple-700',
                    'progressClass' => 'bg-gradient-to-r from-purple-500 to-purple-600',
                    'borderColor' => 'border-purple-500',
                    'bgColor' => 'bg-purple-50',
                    'textColor' => 'text-purple-800',
                    'buttonClass' => 'bg-purple-600 hover:bg-purple-700',
                ],
                3 => [
                    'color' => 'green',
                    'name' => 'Direktur (Final)',
                    'progress' => 100,
                    'iconColor' => 'text-green-600',
                    'headerClass' => 'bg-gradient-to-r from-green-600 to-green-700',
                    'progressClass' => 'bg-gradient-to-r from-green-500 to-green-600',
                    'borderColor' => 'border-green-500',
                    'bgColor' => 'bg-green-50',
                    'textColor' => 'text-green-800',
                    'buttonClass' => 'bg-green-600 hover:bg-green-700',
                ],
                default => [
                    'color' => 'gray',
                    'name' => 'Unknown',
                    'progress' => 0,
                    'iconColor' => 'text-gray-600',
                    'headerClass' => 'bg-gradient-to-r from-gray-600 to-gray-700',
                    'progressClass' => 'bg-gradient-to-r from-gray-500 to-gray-600',
                    'borderColor' => 'border-gray-500',
                    'bgColor' => 'bg-gray-50',
                    'textColor' => 'text-gray-800',
                    'buttonClass' => 'bg-gray-600 hover:bg-gray-700',
                ],
            };
        @endphp

        {{-- ✅ FIX: Display Error Messages (Global) --}}
        @if($errors->any())
            <div class="bg-red-50 border-2 border-red-500 rounded-lg p-4 mb-6 shadow-lg">
                <div class="flex items-start">
                    <svg class="w-6 h-6 text-red-600 mr-3 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path>
                    </svg>
                    <div class="flex-1">
                        <h3 class="text-lg font-bold text-red-800 mb-2">❌ Validasi Gagal!</h3>
                        <ul class="list-disc list-inside space-y-1 text-red-700">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>
        @endif

        <div class="text-center mb-8">
            <div class="inline-block p-4 bg-white rounded-full shadow-lg mb-4">
                <svg class="w-16 h-16 {{ $levelConfig['iconColor'] }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
            </div>
            <h1 class="text-3xl font-bold text-gray-800 mb-2">📋 Verifikasi Dokumen</h1>
            <p class="text-gray-600">Level {{ $level }} dari 3 - {{ $levelConfig['name'] }}</p>

            <div class="max-w-md mx-auto mt-4">
                <div class="flex justify-between text-xs font-medium text-gray-600 mb-2">
                    <span>Progress Verifikasi</span>
                    <span>{{ $levelConfig['progress'] }}%</span>
                </div>
                <div class="w-full bg-gray-200 rounded-full h-3">
                    <div class="{{ $levelConfig['progressClass'] }} h-3 rounded-full transition-all duration-500"
                         style="width: {{ $levelConfig['progress'] }}%"></div>
                </div>
            </div>
        </div>

        @if($level > 1)
            <div class="bg-green-50 border border-green-200 rounded-lg p-4 mb-6">
                <div class="flex items-start">
                    <svg class="w-5 h-5 text-green-600 mr-2 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                    </svg>
                    <div class="flex-1">
                        <p class="text-green-800 font-medium mb-2">✅ Verifikasi Sebelumnya Sudah Disetujui:</p>
                        <div class="space-y-1 text-sm text-green-700">
                            @if($level >= 2)
                                @php
                                    $level1 = $verification->documentRequest->verifications->where('verification_level', 1)->where('status', 'approved')->first();
                                @endphp
                                @if($level1)
                                    <p>• Level 1: <strong>{{ $level1->authority->name }}</strong> ({{ $level1->verified_at->format('d M Y, H:i') }})</p>
                                @endif
                            @endif
                            @if($level >= 3)
                                @php
                                    $level2 = $verification->documentRequest->verifications->where('verification_level', 2)->where('status', 'approved')->first();
                                @endphp
                                @if($level2)
                                    <p>• Level 2: <strong>{{ $level2->authority->name }}</strong> ({{ $level2->verified_at->format('d M Y, H:i') }})</p>
                                @endif
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        @endif

        <div class="bg-green-50 border border-green-200 rounded-lg p-4 mb-6">
            <div class="flex items-center">
                <svg class="w-5 h-5 text-green-600 mr-2" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                </svg>
                <div>
                    <p class="text-green-800 font-medium">✅ Token Valid</p>
                    <p class="text-green-600 text-sm">⏰ Berlaku sampai: {{ $verification->expires_at->format('d F Y, H:i') }} WIB</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-2xl shadow-xl overflow-hidden">
            <div class="{{ $levelConfig['headerClass'] }} p-6 text-white">
                <h2 class="text-xl font-semibold mb-4">📄 Detail Dokumen</h2>
                <div class="grid grid-cols-2 gap-4 text-sm">
                    <div>
                        <p class="text-{{ $levelConfig['color'] }}-100">No. Dokumen</p>
                        <p class="font-semibold text-lg">{{ $verification->documentRequest->request_code }}</p>
                    </div>
                    <div>
                        <p class="text-{{ $levelConfig['color'] }}-100">Tanggal Request</p>
                        <p class="font-semibold">{{ $verification->documentRequest->created_at->format('d F Y') }}</p>
                    </div>
                </div>
            </div>

            <form action="{{ route('verification.submit', $verification->token) }}" method="POST" class="p-6" id="verificationForm">
                @csrf

                <div class="mb-6">
                    <label class="block text-sm font-medium text-gray-700 mb-2">📋 Jenis Dokumen</label>
                    <div class="bg-gray-50 rounded-lg p-4 border border-gray-200">
                        <p class="text-lg font-semibold text-gray-800">{{ $verification->documentRequest->documentType->name }}</p>
                    </div>
                </div>

                <div class="mb-6">
                    <label class="block text-sm font-medium text-gray-700 mb-3">👤 Data Pemohon</label>
                    <div class="bg-gray-50 rounded-lg p-4 border border-gray-200 space-y-3">
                        <div class="flex justify-between">
                            <span class="text-gray-600">Nama:</span>
                            <span class="font-semibold text-gray-800">{{ $verification->documentRequest->applicant_name }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">NIM/NIP:</span>
                            <span class="font-semibold text-gray-800">{{ $verification->documentRequest->applicant_identifier }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">Email:</span>
                            <span class="font-semibold text-gray-800">{{ $verification->documentRequest->applicant_email }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">No. HP:</span>
                            <span class="font-semibold text-gray-800">{{ $verification->documentRequest->applicant_phone }}</span>
                        </div>
                        @if($verification->documentRequest->applicant_unit)
                        <div class="flex justify-between">
                            <span class="text-gray-600">Program Studi/Unit:</span>
                            <span class="font-semibold text-gray-800">{{ $verification->documentRequest->applicant_unit }}</span>
                        </div>
                        @endif
                    </div>
                </div>

                @if($verification->documentRequest->purpose)
                <div class="mb-6">
                    <label class="block text-sm font-medium text-gray-700 mb-2">📝 Keperluan</label>
                    <div class="bg-gray-50 rounded-lg p-4 border border-gray-200">
                        <p class="text-gray-800">{{ $verification->documentRequest->purpose }}</p>
                    </div>
                </div>
                @endif

                <div class="mb-6 {{ $levelConfig['bgColor'] }} rounded-lg p-4 border {{ $levelConfig['borderColor'] }}">
                    <label class="block text-sm font-medium {{ $levelConfig['textColor'] }} mb-2">👤 Anda (Level {{ $level }}):</label>
                    <p class="text-lg font-semibold text-gray-900">{{ $verification->authority->name }}</p>
                    <p class="text-gray-700">{{ $verification->authority->position }}</p>
                    @if($level === 3)
                        <p class="text-sm text-red-600 mt-2 font-medium">⚠️ Ini adalah FINAL APPROVAL. Setelah Anda setujui, lanjut ke proses TTD.</p>
                    @endif
                </div>

                <hr class="my-6">

                <div class="mb-6">
                    <label class="block text-lg font-semibold text-gray-800 mb-4">ℹ️ Verifikasi Kelayakan Level {{ $level }}:</label>
                    <p class="text-gray-600 mb-4">Apakah data pemohon di atas sudah benar dan layak untuk diterbitkan dokumen?</p>
                </div>

                <div class="mb-6">
                    <label class="block text-sm font-medium text-gray-700 mb-3">Keputusan Verifikasi: <span class="text-red-500">*</span></label>

                    <div class="space-y-3">
                        <label class="flex items-center p-4 border-2 border-gray-200 rounded-lg cursor-pointer hover:bg-green-50 hover:border-green-500 transition" for="approve">
                            <input type="radio" name="decision" value="approved" id="approve" class="w-5 h-5 text-green-600" {{ old('decision') === 'approved' ? 'checked' : '' }} required>
                            <div class="ml-3">
                                <span class="font-semibold text-gray-800">✅ Disetujui (Level {{ $level }})</span>
                                <p class="text-sm text-gray-600">
                                    @if($level === 3)
                                        Dokumen layak diterbitkan (FINAL APPROVAL - 100%)
                                    @elseif($level === 2)
                                        Lanjut ke Level 3 (Direktur) - Progress 66%
                                    @else
                                        Lanjut ke Level 2 (Wakil Ketua 3) - Progress 33%
                                    @endif
                                </p>
                            </div>
                        </label>

                        <label class="flex items-center p-4 border-2 border-gray-200 rounded-lg cursor-pointer hover:bg-red-50 hover:border-red-500 transition" for="reject">
                            <input type="radio" name="decision" value="rejected" id="reject" class="w-5 h-5 text-red-600" {{ old('decision') === 'rejected' ? 'checked' : '' }} required>
                            <div class="ml-3">
                                <span class="font-semibold text-gray-800">❌ Ditolak (Level {{ $level }})</span>
                                <p class="text-sm text-gray-600">Dokumen tidak layak - Proses BERHENTI</p>
                            </div>
                        </label>
                    </div>

                    @error('decision')
                        <p class="text-red-600 text-sm mt-2 font-semibold flex items-center gap-1">
                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                            </svg>
                            {{ $message }}
                        </p>
                    @enderror
                </div>

                {{-- ✅ FIX: SINGLE DYNAMIC TEXTAREA --}}
                <div class="mb-6">
                    <label class="block text-sm font-medium text-gray-700 mb-2" id="notesLabel">
                        Catatan (opsional):
                    </label>
                    <textarea
                        name="notes"
                        id="notesTextarea"
                        rows="4"
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition"
                        placeholder="Tambahkan catatan jika diperlukan..."
                    >{{ old('notes') }}</textarea>

                    @error('notes')
                        <p class="text-red-600 text-sm mt-2 font-semibold flex items-center gap-1">
                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                            </svg>
                            {{ $message }}
                        </p>
                    @enderror

                    <p class="text-sm text-gray-500 mt-2" id="notesHint">Maksimal 1000 karakter</p>
                </div>

                <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4 mb-6">
                    <div class="flex items-start">
                        <svg class="w-5 h-5 text-yellow-600 mr-2 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                        </svg>
                        <div>
                            <p class="font-medium text-yellow-800">⚠️ Perhatian</p>
                            <p class="text-sm text-yellow-700">
                                Keputusan Anda tidak dapat diubah setelah submit.
                                @if($level === 3)
                                    Ini adalah <strong>FINAL APPROVAL</strong>.
                                @else
                                    Jika disetujui, otomatis lanjut ke Level {{ $level + 1 }}.
                                @endif
                            </p>
                        </div>
                    </div>
                </div>

                <div class="flex gap-4">
                    <button type="button" onclick="window.history.back()" class="flex-1 px-6 py-3 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 font-semibold transition">
                        ❌ Batal
                    </button>
                    <button type="submit" class="flex-1 px-6 py-3 {{ $levelConfig['buttonClass'] }} text-white rounded-lg font-semibold transition shadow-lg">
                        ✅ Submit Verifikasi Level {{ $level }}
                    </button>
                </div>
            </form>
        </div>

        <div class="text-center mt-6 text-gray-600 text-sm">
            <p>🔒 Link ini bersifat rahasia dan hanya untuk Anda</p>
            <p class="mt-1">SIPETER - Sistem Persuratan Terpadu</p>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const approveRadio = document.getElementById('approve');
    const rejectRadio = document.getElementById('reject');
    const notesTextarea = document.getElementById('notesTextarea');
    const notesLabel = document.getElementById('notesLabel');
    const notesHint = document.getElementById('notesHint');

    // ✅ FIX: Dynamic textarea behavior
    function updateNotesField() {
        if (rejectRadio.checked) {
            // Jika TOLAK dipilih
            notesLabel.innerHTML = 'Catatan / Alasan Penolakan: <span class="text-red-500">*</span>';
            notesTextarea.required = true;
            notesTextarea.placeholder = 'Jelaskan alasan penolakan secara detail...';
            notesTextarea.classList.remove('border-gray-300', 'focus:ring-blue-500');
            notesTextarea.classList.add('border-red-300', 'focus:ring-red-500');
            notesHint.classList.remove('text-gray-500');
            notesHint.classList.add('text-red-600');
            notesHint.textContent = '⚠️ WAJIB DIISI! Maksimal 1000 karakter';
        } else if (approveRadio.checked) {
            // Jika SETUJU dipilih
            notesLabel.innerHTML = 'Catatan (opsional):';
            notesTextarea.required = false;
            notesTextarea.placeholder = 'Tambahkan catatan jika diperlukan...';
            notesTextarea.classList.remove('border-red-300', 'focus:ring-red-500');
            notesTextarea.classList.add('border-gray-300', 'focus:ring-blue-500');
            notesHint.classList.remove('text-red-600');
            notesHint.classList.add('text-gray-500');
            notesHint.textContent = 'Maksimal 1000 karakter';
        }
    }

    // Event listeners
    approveRadio.addEventListener('change', updateNotesField);
    rejectRadio.addEventListener('change', updateNotesField);

    // ✅ Initialize on page load (handle old() values)
    updateNotesField();
});
</script>
@endsection
