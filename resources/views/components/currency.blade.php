@props([
    'amount' => 0,
    'format' => 'default', // default, indian, noDecimal, custom
    'decimals' => 2,
    'showSymbol' => true,
    'class' => '',
])

@php
    $formatted = match($format) {
        'indian' => currencyIndian($amount, $showSymbol),
        'noDecimal' => currencyNoDecimal($amount, $showSymbol),
        'custom' => currencyCustom($amount, $decimals, $showSymbol),
        default => currency($amount, $showSymbol),
    };
@endphp

<span {{ $attributes->merge(['class' => $class]) }}>
    {{ $formatted }}
</span>

