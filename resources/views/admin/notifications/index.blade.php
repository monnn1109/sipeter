@extends('layouts.admin', [
    'title' => 'Notifikasi',
    'subtitle' => 'Semua notifikasi sistem'
])

@section('content')
<div class="max-w-5xl mx-auto space-y-6">
    <div class="bg-white rounded-2xl shadow-lg p-6">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-3xl font-black text-gray-900">📬 Notifikasi</h2>
                <p class="text-gray-600 mt-2">
                    @if($unreadCount > 0)
                        <span class="inline-flex items-center gap-2 px-3 py-1 bg-red-100 text-red-700 rounded-full text-sm font-semibold">
                            <span class="w-2 h-2 bg-red-500 rounded-full animate-pulse"></span>
                            {{ $unreadCount }} notifikasi belum dibaca
                        </span>
                    @else
                        <span class="text-green-600 font-semibold">✅ Semua notifikasi sudah dibaca</span>
                    @endif
                </p>
            </div>
            @if($notifications->count() > 0 && $unreadCount > 0)
                <form action="{{ route('admin.notifications.mark-all-read') }}" method="POST">
                    @csrf
                    <button
                        type="submit"
                        class="inline-flex items-center gap-2 px-6 py-3 bg-gradient-to-r from-indigo-600 to-indigo-700 hover:from-indigo-700 hover:to-indigo-800 text-white font-bold rounded-xl shadow-lg hover:shadow-xl transform hover:-translate-y-0.5 transition-all">
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                        </svg>
                        Tandai Semua Dibaca
                    </button>
                </form>
            @endif
        </div>
    </div>

    @if(session('success'))
        <div class="bg-gradient-to-r from-green-50 to-emerald-50 border-l-4 border-green-500 p-5 rounded-xl shadow-md">
            <div class="flex items-center gap-3">
                <svg class="w-6 h-6 text-green-600" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                </svg>
                <p class="text-green-800 font-semibold">{{ session('success') }}</p>
            </div>
        </div>
    @endif

    @if($notifications && $notifications->count() > 0)
        <div class="space-y-4">
            @foreach($notifications as $notif)
                @php
                    $notifType = is_string($notif->type) ? $notif->type : $notif->type->value;

                    $configs = [
                        'new_request' => [
                            'bg' => 'bg-blue-500',
                            'icon_bg' => 'bg-blue-100',
                            'icon_color' => 'text-blue-600',
                            'border' => 'border-blue-200',
                            'title_color' => 'text-blue-900'
                        ],
                        'status_update' => [
                            'bg' => 'bg-green-500',
                            'icon_bg' => 'bg-green-100',
                            'icon_color' => 'text-green-600',
                            'border' => 'border-green-200',
                            'title_color' => 'text-green-900'
                        ],
                        'rejection' => [
                            'bg' => 'bg-red-500',
                            'icon_bg' => 'bg-red-100',
                            'icon_color' => 'text-red-600',
                            'border' => 'border-red-200',
                            'title_color' => 'text-red-900'
                        ],
                        'ready_pickup' => [
                            'bg' => 'bg-purple-500',
                            'icon_bg' => 'bg-purple-100',
                            'icon_color' => 'text-purple-600',
                            'border' => 'border-purple-200',
                            'title_color' => 'text-purple-900'
                        ],
                        'completed' => [
                            'bg' => 'bg-gray-500',
                            'icon_bg' => 'bg-gray-100',
                            'icon_color' => 'text-gray-600',
                            'border' => 'border-gray-200',
                            'title_color' => 'text-gray-900'
                        ],
                    ];

                    $config = $configs[$notifType] ?? $configs['completed'];
                @endphp

                <div class="group relative bg-white rounded-2xl shadow-md hover:shadow-xl transition-all duration-300 overflow-hidden {{ $notif->read_at ? 'opacity-60 hover:opacity-100' : 'ring-2 ring-indigo-200' }}">
                    @if(!$notif->read_at)
                        <div class="absolute top-0 left-0 right-0 h-1 {{ $config['bg'] }}"></div>
                    @endif

                    <div class="p-5">
                        <div class="flex gap-4">
                            <div class="flex-shrink-0">
                                <div class="w-14 h-14 {{ $config['icon_bg'] }} rounded-2xl flex items-center justify-center shadow-sm group-hover:scale-110 transition-transform duration-300">
                                    @if($notifType === 'new_request')
                                        <svg class="w-7 h-7 {{ $config['icon_color'] }}" fill="currentColor" viewBox="0 0 20 20">
                                            <path d="M9 2a1 1 0 000 2h2a1 1 0 100-2H9z"/>
                                            <path d="M4 5a2 2 0 012-2 3 3 0 003 3h2a3 3 0 003-3 2 2 0 012 2v11a2 2 0 01-2 2H6a2 2 0 01-2-2V5zm3 4a1 1 0 000 2h.01a1 1 0 100-2H7zm3 0a1 1 0 000 2h3a1 1 0 100-2h-3zm-3 4a1 1 0 100 2h.01a1 1 0 100-2H7zm3 0a1 1 0 100 2h3a1 1 0 100-2h-3z"/>
                                        </svg>
                                    @elseif($notifType === 'status_update')
                                        <svg class="w-7 h-7 {{ $config['icon_color'] }}" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                        </svg>
                                    @elseif($notifType === 'rejection')
                                        <svg class="w-7 h-7 {{ $config['icon_color'] }}" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                                        </svg>
                                    @elseif($notifType === 'ready_pickup')
                                        <svg class="w-7 h-7 {{ $config['icon_color'] }}" fill="currentColor" viewBox="0 0 20 20">
                                            <path d="M9 2a1 1 0 000 2h2a1 1 0 100-2H9z"/>
                                            <path fill-rule="evenodd" d="M4 5a2 2 0 012-2 3 3 0 003 3h2a3 3 0 003-3 2 2 0 012 2v11a2 2 0 01-2 2H6a2 2 0 01-2-2V5zm9.707 5.707a1 1 0 00-1.414-1.414L9 12.586l-1.293-1.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                        </svg>
                                    @else
                                        <svg class="w-7 h-7 {{ $config['icon_color'] }}" fill="currentColor" viewBox="0 0 20 20">
                                            <path d="M10 2a6 6 0 00-6 6v3.586l-.707.707A1 1 0 004 14h12a1 1 0 00.707-1.707L16 11.586V8a6 6 0 00-6-6zM10 18a3 3 0 01-3-3h6a3 3 0 01-3 3z"/>
                                        </svg>
                                    @endif
                                </div>
                            </div>

                            <div class="flex-1 min-w-0">
                                <div class="flex items-start justify-between gap-4 mb-3">
                                    <div class="flex-1">
                                        <h3 class="font-bold {{ $config['title_color'] }} text-lg leading-tight mb-1">
                                            {{ $notif->title }}
                                        </h3>
                                        <p class="text-gray-700 leading-relaxed">
                                            {{ $notif->message }}
                                        </p>
                                    </div>
                                    @if(!$notif->read_at)
                                        <span class="flex-shrink-0 px-3 py-1.5 bg-gradient-to-r from-blue-500 to-indigo-600 text-white text-xs font-bold rounded-full shadow-md">
                                            BARU
                                        </span>
                                    @endif
                                </div>

                                @if(isset($notif->document_request_id) && $notif->document_request_id)
                                    <div class="mb-4 p-4 bg-gradient-to-r from-gray-50 to-gray-100 rounded-xl border {{ $config['border'] }}">
                                        <div class="flex items-center gap-2">
                                            <svg class="w-5 h-5 text-gray-500" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4z" clip-rule="evenodd"/>
                                            </svg>
                                            <span class="text-sm text-gray-600 font-medium">Kode Dokumen:</span>
                                            <code class="px-3 py-1 bg-white text-indigo-700 font-bold text-sm rounded-lg border border-gray-300">
                                                {{ $notif->documentRequest->request_code ?? 'N/A' }}
                                            </code>
                                        </div>
                                    </div>
                                @endif

                                <div class="flex items-center justify-between pt-3 border-t border-gray-100">
                                    <div class="flex items-center gap-2 text-sm text-gray-500">
                                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"/>
                                        </svg>
                                        <span class="font-medium">{{ $notif->created_at->diffForHumans() }}</span>
                                    </div>

                                    <div class="flex items-center gap-2">
                                        @if(isset($notif->document_request_id) && $notif->document_request_id)
                                            <a
                                                href="{{ route('admin.documents.show', $notif->document_request_id) }}"
                                                class="inline-flex items-center gap-2 px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white font-semibold rounded-lg shadow-md hover:shadow-lg transition-all text-sm">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                                </svg>
                                                Lihat Detail
                                            </a>
                                        @endif

                                        @if(!$notif->read_at)
                                            <form action="{{ route('admin.notifications.mark-read', $notif->id) }}" method="POST">
                                                @csrf
                                                <button
                                                    type="submit"
                                                    class="inline-flex items-center gap-2 px-4 py-2 bg-green-600 hover:bg-green-700 text-white font-semibold rounded-lg shadow-md hover:shadow-lg transition-all text-sm">
                                                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                                    </svg>
                                                    Tandai Dibaca
                                                </button>
                                            </form>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        @if($notifications->hasPages())
            <div class="bg-white rounded-2xl shadow-lg p-6">
                {{ $notifications->links() }}
            </div>
        @endif
    @else
        <div class="bg-gradient-to-br from-green-50 to-emerald-50 rounded-3xl shadow-xl p-16 text-center">
            <div class="w-32 h-32 bg-gradient-to-br from-green-400 to-emerald-500 rounded-full flex items-center justify-center mx-auto mb-8 shadow-2xl">
                <svg class="w-16 h-16 text-white" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                </svg>
            </div>
            <h3 class="text-3xl font-black text-gray-900 mb-4">✨ Semua Bersih!</h3>
            <p class="text-gray-600 text-lg mb-8 max-w-md mx-auto leading-relaxed">
                Tidak ada notifikasi saat ini. Notifikasi baru akan muncul ketika ada aktivitas sistem.
            </p>
            <a
                href="{{ route('admin.dashboard') }}"
                class="inline-flex items-center gap-3 px-8 py-4 bg-gradient-to-r from-indigo-600 to-indigo-700 hover:from-indigo-700 hover:to-indigo-800 text-white font-bold rounded-2xl shadow-xl hover:shadow-2xl transform hover:-translate-y-1 transition-all">
                <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20">
                    <path d="M10.707 2.293a1 1 0 00-1.414 0l-7 7a1 1 0 001.414 1.414L4 10.414V17a1 1 0 001 1h2a1 1 0 001-1v-2a1 1 0 011-1h2a1 1 0 011 1v2a1 1 0 001 1h2a1 1 0 001-1v-6.586l.293.293a1 1 0 001.414-1.414l-7-7z"/>
                </svg>
                Kembali ke Dashboard
            </a>
        </div>
    @endif
</div>
@endsection
