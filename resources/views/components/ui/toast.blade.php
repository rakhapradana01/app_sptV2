@props([
    'type' => 'success',
    'message' => '',
])

@php
    $colors = [
        'success' => 'bg-green-500',
        'error' => 'bg-red-500',
        'warning' => 'bg-yellow-500',
        'info' => 'bg-blue-500',
    ];
@endphp

<div 
    x-data="{ show: true }"
    x-init="setTimeout(() => show = false, 4000)"
    x-show="show"
    x-transition
    class="fixed top-22 right-5 z-50 px-4 py-3 rounded-lg shadow-lg text-white {{ $colors[$type] }}"
>
    {{ $message }}
</div>