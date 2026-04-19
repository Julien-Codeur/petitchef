@if(auth()->user()->isAdmin())
    @include('dashboard.admin-dashboard')
@elseif(auth()->user()->isCook())
    @include('dashboard.chef-dashboard')
@else
    @include('dashboard.client-dashboard')
@endif
