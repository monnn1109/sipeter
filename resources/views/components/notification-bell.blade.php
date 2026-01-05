@props(['notifications' => [], 'unreadCount' => 0])

<div class="relative" x-data="{ open: false }" @click.away="open = false">
    <button
        @click="open = !open"
        class="relative p-2 rounded-xl hover:bg-gray-100 transition-colors">
        <svg class="w-6 h-6 text-gray-600" fill="currentColor" viewBox="0 0 20 20">
            <path d="M10 2a6 6 0 00-6 6v3.586l-.707.707A1 1 0 004 14h12a1 1 0 00.707-1.707L16 11.586V8a6 6 0 00-6-6zM10 18a3 3 0 01-3-3h6a3 3 0 01-3 3z"/>
        </svg>
        @if($unreadCount > 0)
            <span class="absolute top-1 right-1 w-5 h-5 bg-red-500 rounded-full text-white text-xs flex items-center justify-center font-bold">
                {{ $unreadCount > 9 ? '9+' : $unreadCount }}
            </span>
        @endif
    </button>

    <div
        x-show="open"
        x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0 scale-95"
        x-transition:enter-end="opacity-100 scale-100"
        x-transition:leave="transition ease-in duration-150"
        x-transition:leave-start="opacity-100 scale-100"
        x-transition:leave-end="opacity-0 scale-95"
        class="absolute right-0 mt-2 w-96 bg-white rounded-2xl shadow-2xl border border-gray-200 z-50"
        style="display: none;">

        <div class="px-6 py-4 border-b border-gray-200 flex items-center justify-between">
            <h3 class="font-bold text-gray-900">Notifikasi</h3>
            @if($unreadCount > 0)
                <span class="px-3 py-1 bg-red-100 text-red-800 text-xs font-bold rounded-full">
                    {{ $unreadCount }} baru
                </span>
            @endif
        </div>

        <div class="max-h-96 overflow-y-auto">
            @if(count($notifications) > 0)
                @foreach($notifications as $notif)
                    <a
                        href="{{ $notif->document ? route('admin.documents.show', $notif->document->id) : route('admin.notifications.index') }}"
                        class="block px-6 py-4 hover:bg-gray-50 transition-colors border-b border-gray-100 {{ !$notif->read_at ? 'bg-blue-50' : '' }}">
                        <div class="flex items-start gap-3">
                            <div class="flex-shrink-0">
                                @php
                                    $iconColors = [
                                        'new_request' => 'bg-blue-100 text-blue-600',
                                        'status_update' => 'bg-green-100 text-green-600',
                                        'rejection' => 'bg-red-100 text-red-600',
                                        'ready_pickup' => 'bg-purple-100 text-purple-600',
                                        'completed' => 'bg-gray-100 text-gray-600',
                                    ];
                                    $iconColor = $iconColors[$notif->type->value] ?? 'bg-gray-100 text-gray-600';
                                @endphp
                                <div class="w-10 h-10 {{ $iconColor }} rounded-lg flex items-center justify-center">
                                    @if($notif->type->value === 'new_request')
                                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                            <path d="M8 3a1 1 0 011-1h2a1 1 0 110 2H9a1 1 0 01-1-1z"/>
                                            <path d="M6 3a2 2 0 00-2 2v11a2 2 0 002 2h8a2 2 0 002-2V5a2 2 0 00-2-2 3 3 0 01-3 3H9a3 3 0 01-3-3z"/>
                                        </svg>
                                    @else
                                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                            <path d="M10 2a6 6 0 00-6 6v3.586l-.707.707A1 1 0 004 14h12a1 1 0 00.707-1.707L16 11.586V8a6 6 0 00-6-6zM10 18a3 3 0 01-3-3h6a3 3 0 01-3 3z"/>
                                        </svg>
                                    @endif
                                </div>
                            </div>
                            <div class="flex-1 min-w-0">
                                <h4 class="font-semibold text-gray-900 text-sm mb-1">{{ $notif->title }}</h4>
                                <p class="text-xs text-gray-600 line-clamp-2">{{ $notif->message }}</p>
                                <p class="text-xs text-gray-500 mt-2">{{ $notif->created_at->diffForHumans() }}</p>
                            </div>
                            @if(!$notif->read_at)
                                <div class="w-2 h-2 bg-blue-600 rounded-full flex-shrink-0 mt-2"></div>
                            @endif
                        </div>
                    </a>
                @endforeach
            @else
                <div class="px-6 py-12 text-center text-gray-500">
                    <svg class="w-12 h-12 mx-auto mb-3 text-gray-300" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M10 2a6 6 0 00-6 6v3.586l-.707.707A1 1 0 004 14h12a1 1 0 00.707-1.707L16 11.586V8a6 6 0 00-6-6zM10 18a3 3 0 01-3-3h6a3 3 0 01-3 3z"/>
                    </svg>
                    <p class="text-sm font-semibold">Tidak ada notifikasi</p>
                </div>
            @endif
        </div>

        @if(count($notifications) > 0)
            <div class="px-6 py-3 bg-gray-50 border-t border-gray-200">
                <a
                    href="{{ route('admin.notifications.index') }}"
                    class="block text-center text-sm font-semibold text-indigo-600 hover:text-indigo-700">
                    Lihat Semua Notifikasi →
                </a>
            </div>
        @endif
    </div>
</div>
