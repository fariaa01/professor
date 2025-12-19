@props(['type' => 'text', 'money' => false])

@if($money)
    <div class="relative">
        <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-gray-500">R$</span>
        <input {{ $attributes->merge(['class' => 'pl-10 border-gray-300 focus:border-blue-500 focus:ring-blue-500 rounded-md shadow-sm w-full', 'type' => 'number', 'step' => '0.01', 'min' => '0']) }}>
    </div>
@else
    <input {{ $attributes->merge(['class' => 'border-gray-300 focus:border-blue-500 focus:ring-blue-500 rounded-md shadow-sm w-full', 'type' => $type]) }}>
@endif
