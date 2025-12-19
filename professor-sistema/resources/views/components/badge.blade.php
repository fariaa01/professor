@props(['variant' => 'default', 'size' => 'md'])

@php
$classes = match($variant) {
    'success' => 'bg-green-100 text-green-800 border-green-200',
    'warning' => 'bg-yellow-100 text-yellow-800 border-yellow-200',
    'danger' => 'bg-red-100 text-red-800 border-red-200',
    'info' => 'bg-blue-100 text-blue-800 border-blue-200',
    'secondary' => 'bg-gray-100 text-gray-800 border-gray-200',
    default => 'bg-gray-100 text-gray-800 border-gray-200',
};

$sizeClasses = match($size) {
    'sm' => 'px-2 py-0.5 text-xs',
    'md' => 'px-2.5 py-0.5 text-xs',
    'lg' => 'px-3 py-1 text-sm',
    default => 'px-2.5 py-0.5 text-xs',
};
@endphp

<span {{ $attributes->merge(['class' => 'inline-flex items-center rounded-full font-medium border ' . $classes . ' ' . $sizeClasses]) }}>
    {{ $slot }}
</span>
