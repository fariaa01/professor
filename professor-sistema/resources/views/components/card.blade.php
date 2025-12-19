@props(['padding' => true])

<div {{ $attributes->merge(['class' => 'bg-white border border-gray-200 rounded-lg shadow-sm ' . ($padding ? 'p-6' : '')]) }}>
    {{ $slot }}
</div>
