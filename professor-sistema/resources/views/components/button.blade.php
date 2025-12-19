@props(['variant' => 'primary', 'size' => 'md', 'href' => null])

@php
$variantClasses = match($variant) {
    'primary' => 'bg-blue-600 hover:bg-blue-700 text-white border-transparent',
    'secondary' => 'bg-gray-100 hover:bg-gray-200 text-gray-900 border-gray-300',
    'outline' => 'bg-transparent hover:bg-gray-50 text-gray-700 border-gray-300',
    'ghost' => 'bg-transparent hover:bg-gray-100 text-gray-700 border-transparent',
    'danger' => 'bg-red-600 hover:bg-red-700 text-white border-transparent',
    default => 'bg-blue-600 hover:bg-blue-700 text-white border-transparent',
};

$sizeClasses = match($size) {
    'sm' => 'px-3 py-1.5 text-sm',
    'md' => 'px-4 py-2 text-sm',
    'lg' => 'px-6 py-3 text-base',
    default => 'px-4 py-2 text-sm',
};

$baseClasses = 'inline-flex items-center justify-center font-medium rounded-md border transition-colors focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 disabled:opacity-50 disabled:pointer-events-none';
@endphp

@if($href)
    <a href="{{ $href }}" {{ $attributes->merge(['class' => $baseClasses . ' ' . $variantClasses . ' ' . $sizeClasses]) }}>
        {{ $slot }}
    </a>
@else
    <button {{ $attributes->merge(['type' => 'button', 'class' => $baseClasses . ' ' . $variantClasses . ' ' . $sizeClasses]) }}>
        {{ $slot }}
    </button>
@endif
