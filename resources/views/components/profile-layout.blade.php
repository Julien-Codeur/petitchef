@php
    $user = auth()->user();
    $isClient = $user->isClient();
    $isCook = $user->isCook();
@endphp

@if($isClient)
    <x-client-sidebar-layout>
        {{ $slot }}
    </x-client-sidebar-layout>
@elseif($isCook)
    <x-cook-sidebar-layout>
        {{ $slot }}
    </x-cook-sidebar-layout>
@else
    <x-admin-sidebar-layout>
        {{ $slot }}
    </x-admin-sidebar-layout>
@endif
