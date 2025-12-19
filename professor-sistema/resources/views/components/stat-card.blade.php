@props(['title', 'value', 'icon' => null, 'trend' => null, 'trendValue' => null])

<x-card>
    <div class="flex items-center justify-between">
        <div class="flex-1">
            <p class="text-sm font-medium text-gray-600">{{ $title }}</p>
            <p class="mt-2 text-3xl font-bold text-gray-900">{{ $value }}</p>
            
            @if($trend && $trendValue)
                <p class="mt-2 flex items-center text-sm">
                    @if($trend === 'up')
                        <span class="text-green-600">↑ {{ $trendValue }}</span>
                    @elseif($trend === 'down')
                        <span class="text-red-600">↓ {{ $trendValue }}</span>
                    @endif
                </p>
            @endif
        </div>
        
        @if($icon)
            <div class="flex-shrink-0">
                <div class="p-3 bg-blue-50 rounded-lg">
                    {!! $icon !!}
                </div>
            </div>
        @endif
    </div>
</x-card>
