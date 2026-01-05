@props(['activities' => null, 'documentRequest' => null])

@php
    $activityList = null;

    if ($activities !== null) {
        $activityList = $activities;
    } elseif ($documentRequest !== null) {
        $activityList = $documentRequest->activities ?? collect();
    }

    if (is_array($activityList)) {
        $activityList = collect($activityList);
    }

    $activityList = $activityList ?? collect();
@endphp

<div class="flow-root">
    <ul role="list" class="-mb-8">
        @forelse($activityList as $index => $activity)
            <li>
                <div class="relative pb-8">
                    @if(!$loop->last)
                        <span class="absolute top-4 left-4 -ml-px h-full w-0.5 bg-gray-200" aria-hidden="true"></span>
                    @endif

                    <div class="relative flex space-x-3">
                        <div>
                            @php
                                $activityType = is_object($activity->activity_type)
                                    ? $activity->activity_type
                                    : null;

                                $colorClass = $activityType ? $activityType->color() : 'blue';
                                $iconType = $activityType ? $activityType->icon() : 'info';
                            @endphp

                            <span class="h-8 w-8 rounded-full flex items-center justify-center ring-8 ring-white bg-{{ $colorClass }}-500">
                                <svg class="h-5 w-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    @switch($iconType)
                                        @case('send')
                                        @case('file-text')
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                            @break
                                        @case('check-circle')
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                            @break
                                        @case('clock')
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                            @break
                                        @case('check')
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                            @break
                                        @case('x-circle')
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2 2m2-2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                            @break
                                        @case('upload')
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                            @break
                                        @case('download')
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path>
                                            @break
                                        @case('package')
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                                            @break
                                        @case('refresh-cw')
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                                            @break
                                        @default
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    @endswitch
                                </svg>
                            </span>
                        </div>
                        <div class="min-w-0 flex-1 md:flex justify-between items-start md:items-center">
                            <div>
                                <p class="text-sm font-medium text-gray-900">
                                    {{ $activity->description ?? 'Aktivitas tidak diketahui' }}
                                </p>

                                @if($activity->user_id && $activity->user)
                                    <p class="mt-1 text-xs text-gray-500">
                                        oleh <span class="font-medium">{{ $activity->user->name }}</span>
                                    </p>
                                @elseif($activity->actor_name)
                                    <p class="mt-1 text-xs text-gray-500">
                                        oleh <span class="font-medium">{{ $activity->actor_name }}</span>
                                    </p>
                                @endif

                                @if(isset($activity->notes) && $activity->notes)
                                    <p class="mt-1 text-xs text-gray-600 italic">
                                        "{{ $activity->notes }}"
                                    </p>
                                @endif
                            </div>

                            {{-- ✅ FIXED: Timezone WIB otomatis dari config & AppServiceProvider --}}
                            <div class="whitespace-nowrap text-right text-sm text-gray-500 mt-2 md:mt-0">
                                @if(isset($activity->created_at))
                                    <time datetime="{{ $activity->created_at->toIso8601String() }}">
                                        {{ $activity->created_at->translatedFormat('d M Y') }}
                                    </time>
                                    <p class="text-xs text-gray-400">
                                        {{ $activity->created_at->format('H:i') }} WIB
                                    </p>
                                @else
                                    <span class="text-xs text-gray-400">-</span>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </li>
        @empty
            <li class="text-center py-12">
                <div class="flex flex-col items-center">
                    <div class="h-16 w-16 bg-gray-100 rounded-full flex items-center justify-center mb-4">
                        <svg class="h-8 w-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                    </div>
                    <h3 class="text-sm font-semibold text-gray-900 mb-1">Belum Ada Riwayat Aktivitas</h3>
                    <p class="text-sm text-gray-500">Riwayat perubahan status akan ditampilkan di sini</p>
                </div>
            </li>
        @endforelse
    </ul>
</div>
