@props(['title' => '', 'description' => ''])

<div {{ $attributes->merge(['class' => 'mb-6']) }}>
    @if($title)
        <h3 class="text-xl font-semibold text-gray-900">{{ $title }}</h3>
    @else
        <h3 class="text-xl font-semibold text-gray-900">{{ $slot }}</h3>
    @endif
    
    @if($description)
        <p class="text-sm text-gray-500 mt-1">{{ $description }}</p>
    @endif
</div>
