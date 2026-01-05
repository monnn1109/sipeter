@extends('layouts.internal')

@section('title', 'Ajukan Permohonan Dokumen')

@section('content')
<div class="max-w-3xl mx-auto">
    <div class="mb-6">
        <h2 class="text-2xl font-bold text-gray-900">Ajukan Permohonan Dokumen</h2>
        <p class="mt-1 text-gray-600">Lengkapi formulir di bawah untuk mengajukan permohonan dokumen</p>
    </div>

    @if($errors->any())
        <div class="mb-6 bg-red-50 border border-red-200 text-red-600 px-4 py-3 rounded-lg">
            <ul class="list-disc list-inside">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="bg-white rounded-lg shadow p-6">
        <form action="{{ route('internal.my-documents.review') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
            @csrf

            <div>
                <label for="document_type_id" class="block text-sm font-medium text-gray-700">
                    Jenis Dokumen <span class="text-red-500">*</span>
                </label>
                <select id="document_type_id" name="document_type_id" required
                        class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                    <option value="">Pilih Jenis Dokumen</option>
                    @foreach($documentTypes as $type)
                        <option value="{{ $type->id }}" {{ old('document_type_id') == $type->id ? 'selected' : '' }}>
                            {{ $type->name }} ({{ $type->code_prefix }})
                        </option>
                    @endforeach
                </select>
                @error('document_type_id')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="purpose" class="block text-sm font-medium text-gray-700">
                    Keperluan <span class="text-red-500">*</span>
                </label>
                <textarea id="purpose" name="purpose" rows="4" required
                          class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                          placeholder="Jelaskan keperluan dokumen secara detail">{{ old('purpose') }}</textarea>
                @error('purpose')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="quantity" class="block text-sm font-medium text-gray-700">
                    Jumlah Eksemplar <span class="text-red-500">*</span>
                </label>
                <input type="number" id="quantity" name="quantity" min="1" max="10" value="{{ old('quantity', 1) }}" required
                       class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                @error('quantity')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            {{-- Tanggal Dibutuhkan --}}
            <div>
                <label for="needed_date" class="block text-sm font-medium text-gray-700">
                    Tanggal Dibutuhkan <span class="text-red-500">*</span>
                </label>
                <input type="date" id="needed_date" name="needed_date" value="{{ old('needed_date') }}" required
                       min="{{ date('Y-m-d', strtotime('+1 day')) }}"
                       class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                @error('needed_date')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            {{-- Alamat --}}
            <div>
                <label for="applicant_address" class="block text-sm font-medium text-gray-700">
                    Alamat <span class="text-red-500">*</span>
                </label>
                <textarea id="applicant_address" name="applicant_address" rows="3" required
                          class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                          placeholder="Masukkan alamat lengkap Anda">{{ old('applicant_address') }}</textarea>
                @error('applicant_address')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            {{-- Metode Pengambilan --}}
            <x-delivery-method-selector :selected="old('delivery_method', 'pickup')" />

            {{-- Lampiran (Optional) --}}
            <div>
                <label for="attachment" class="block text-sm font-medium text-gray-700">
                    Lampiran Pendukung (Opsional)
                </label>
                <input type="file" id="attachment" name="attachment" accept=".pdf,.jpg,.jpeg,.png"
                       class="mt-1 block w-full text-sm text-gray-500
                              file:mr-4 file:py-2 file:px-4
                              file:rounded-lg file:border-0
                              file:text-sm file:font-semibold
                              file:bg-blue-50 file:text-blue-700
                              hover:file:bg-blue-100">
                <p class="mt-1 text-xs text-gray-500">Format: PDF, JPG, JPEG, PNG. Maksimal 2MB</p>
                @error('attachment')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            {{-- Catatan Tambahan --}}
            <div>
                <label for="notes" class="block text-sm font-medium text-gray-700">
                    Catatan Tambahan
                </label>
                <textarea id="notes" name="notes" rows="2"
                          class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                          placeholder="Catatan tambahan (opsional)">{{ old('notes') }}</textarea>
                @error('notes')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            {{-- Submit Button --}}
            <div class="flex items-center justify-between pt-4 border-t">
                <a href="{{ route('internal.dashboard') }}" class="text-gray-600 hover:text-gray-900">
                    ← Kembali
                </a>
                <button type="submit"
                        class="px-6 py-3 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-lg shadow-sm transition-colors focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                    Ajukan Permohonan
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
