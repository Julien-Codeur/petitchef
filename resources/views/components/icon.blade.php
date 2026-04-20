@props(['icon' => 'check', 'size' => '24', 'color' => '#333', 'class' => ''])

<svg 
    xmlns="http://www.w3.org/2000/svg" 
    viewBox="0 0 24 24" 
    fill="none" 
    stroke="{{ $color }}" 
    stroke-width="2" 
    stroke-linecap="round" 
    stroke-linejoin="round"
    width="{{ $size }}"
    height="{{ $size }}"
    class="icon-svg {{ $class }}"
    style="display: inline-block; vertical-align: middle;"
>
    @include('components.icons.' . $icon)
</svg>