@props(['selected' => 'pickup', 'name' => 'delivery_method'])

<div class="space-y-3">
    <label class="block text-sm font-medium text-gray-700">
        Metode Pengambilan Dokumen <span class="text-red-500">*</span>
    </label>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <label class="relative flex cursor-pointer rounded-lg border bg-white p-4 shadow-sm focus:outline-none transition-all hover:border-blue-500 {{ $selected === 'pickup' ? 'border-blue-600 ring-2 ring-blue-600' : 'border-gray-300' }}">
            <input
                type="radio"
                name="{{ $name }}"
                value="pickup"
                class="sr-only"
                {{ $selected === 'pickup' ? 'checked' : '' }}
                required
                onchange="updateDeliveryMethod(this)"
            />
            <span class="flex flex-1">
                <span class="flex flex-col">
                    <span class="flex items-center gap-2">
                        <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                        </svg>
                        <span class="block text-sm font-medium text-gray-900">Ambil di Kampus</span>
                    </span>
                    <span class="mt-1 flex items-center text-sm text-gray-500">
                        Dokumen fisik akan diambil langsung di kampus
                    </span>
                </span>
            </span>
            <svg class="h-5 w-5 text-blue-600 {{ $selected === 'pickup' ? '' : 'invisible' }}" viewBox="0 0 20 20" fill="currentColor">
                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
            </svg>
        </label>

        <label class="relative flex cursor-pointer rounded-lg border bg-white p-4 shadow-sm focus:outline-none transition-all hover:border-green-500 {{ $selected === 'download' ? 'border-green-600 ring-2 ring-green-600' : 'border-gray-300' }}">
            <input
                type="radio"
                name="{{ $name }}"
                value="download"
                class="sr-only"
                {{ $selected === 'download' ? 'checked' : '' }}
                required
                onchange="updateDeliveryMethod(this)"
            />
            <span class="flex flex-1">
                <span class="flex flex-col">
                    <span class="flex items-center gap-2">
                        <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M9 19l3 3m0 0l3-3m-3 3V10"></path>
                        </svg>
                        <span class="block text-sm font-medium text-gray-900">Unduh Digital</span>
                    </span>
                    <span class="mt-1 flex items-center text-sm text-gray-500">
                        Dokumen digital akan tersedia untuk diunduh
                    </span>
                </span>
            </span>
            <svg class="h-5 w-5 text-green-600 {{ $selected === 'download' ? '' : 'invisible' }}" viewBox="0 0 20 20" fill="currentColor">
                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
            </svg>
        </label>
    </div>

    @error('delivery_method')
        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
    @enderror

    <p class="text-xs text-gray-500">
        <svg class="inline w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
        </svg>
        Anda akan menerima notifikasi WhatsApp ketika dokumen sudah siap
    </p>
</div>

@once
@push('scripts')
<script>
function updateDeliveryMethod(input) {
    const labels = document.querySelectorAll('label[class*="ring-"]');

    labels.forEach(label => {
        label.classList.remove('ring-2', 'border-blue-600', 'border-green-600', 'ring-blue-600', 'ring-green-600');
        label.classList.add('border-gray-300');
        const checkIcon = label.querySelector('svg:last-child');
        if (checkIcon) {
            checkIcon.classList.add('invisible');
        }
    });

    const selectedLabel = input.closest('label');
    selectedLabel.classList.remove('border-gray-300');

    const checkIcon = selectedLabel.querySelector('svg:last-child');
    if (checkIcon) {
        checkIcon.classList.remove('invisible');
    }

    if (input.value === 'pickup') {
        selectedLabel.classList.add('border-blue-600', 'ring-2', 'ring-blue-600');
    } else {
        selectedLabel.classList.add('border-green-600', 'ring-2', 'ring-green-600');
    }
}

document.addEventListener('DOMContentLoaded', function() {
    const checkedInput = document.querySelector('input[name="delivery_method"]:checked');
    if (checkedInput) {
        updateDeliveryMethod(checkedInput);
    }
});
</script>
@endpush
@endonce
