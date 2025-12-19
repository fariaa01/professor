@props(['type' => 'textarea'])

<textarea {{ $attributes->merge(['class' => 'border-gray-300 focus:border-blue-500 focus:ring-blue-500 rounded-md shadow-sm w-full', 'rows' => '3']) }}>{{ $slot }}</textarea>
