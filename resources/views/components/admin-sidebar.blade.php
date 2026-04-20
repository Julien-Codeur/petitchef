<!-- Admin Sidebar -->
<div style="width: 250px; background-color: #f8f9fa; border-right: 1px solid #e9ecef; min-height: 100vh; padding: 20px 0; position: fixed; left: 0; top: 0; overflow-y: auto;">
    <!-- Logo -->
    <div style="padding: 20px; text-align: center; margin-bottom: 30px;">
        <div style="font-size: 36px; margin-bottom: 10px;">🍽️</div>
        <h2 style="font-family: 'Plus Jakarta Sans', sans-serif; font-size: 18px; font-weight: 700; color: #333; margin: 0;">Admin Panel</h2>
    </div>

    <!-- Menu Items -->
    <nav style="padding: 0;">
        <!-- Tableau de bord -->
        <a href="{{ route('admin.dashboard') }}" style="display: flex; align-items: center; gap: 12px; padding: 15px 20px; color: {{ request()->routeIs('admin.dashboard') ? '#ff6b35' : '#666' }}; text-decoration: none; border-left: 4px solid {{ request()->routeIs('admin.dashboard') ? '#ff6b35' : 'transparent' }}; background-color: {{ request()->routeIs('admin.dashboard') ? '#fff3ed' : 'transparent' }}; transition: all 0.3s;">
            <span style="font-size: 20px;">📊</span>
            <span style="font-weight: 600;">Tableau de bord</span>
        </a>

        <!-- Commandes -->
        <a href="{{ route('admin.orders.index') }}" style="display: flex; align-items: center; gap: 12px; padding: 15px 20px; color: {{ request()->routeIs('admin.orders.*') ? '#ff6b35' : '#666' }}; text-decoration: none; border-left: 4px solid {{ request()->routeIs('admin.orders.*') ? '#ff6b35' : 'transparent' }}; background-color: {{ request()->routeIs('admin.orders.*') ? '#fff3ed' : 'transparent' }}; transition: all 0.3s;">
            <span style="font-size: 20px;">📦</span>
            <span style="font-weight: 600;">Commandes</span>
        </a>

        <!-- Clients -->
        <a href="{{ route('admin.users.index') }}" style="display: flex; align-items: center; gap: 12px; padding: 15px 20px; color: {{ request()->routeIs('admin.users.*') ? '#ff6b35' : '#666' }}; text-decoration: none; border-left: 4px solid {{ request()->routeIs('admin.users.*') ? '#ff6b35' : 'transparent' }}; background-color: {{ request()->routeIs('admin.users.*') ? '#fff3ed' : 'transparent' }}; transition: all 0.3s;">
            <span style="font-size: 20px;">👥</span>
            <span style="font-weight: 600;">Clients</span>
        </a>

        <!-- Cuisiniers -->
        <a href="{{ route('admin.cooks.index') }}" style="display: flex; align-items: center; gap: 12px; padding: 15px 20px; color: {{ request()->routeIs('admin.cooks.*') ? '#ff6b35' : '#666' }}; text-decoration: none; border-left: 4px solid {{ request()->routeIs('admin.cooks.*') ? '#ff6b35' : 'transparent' }}; background-color: {{ request()->routeIs('admin.cooks.*') ? '#fff3ed' : 'transparent' }}; transition: all 0.3s;">
            <span style="font-size: 20px;">👨‍🍳</span>
            <span style="font-weight: 600;">Cuisiniers</span>
        </a>

        <!-- Utilisateurs (All) -->
        <a href="{{ route('admin.dishes.index') }}" style="display: flex; align-items: center; gap: 12px; padding: 15px 20px; color: {{ request()->routeIs('admin.dishes.*') ? '#ff6b35' : '#666' }}; text-decoration: none; border-left: 4px solid {{ request()->routeIs('admin.dishes.*') ? '#ff6b35' : 'transparent' }}; background-color: {{ request()->routeIs('admin.dishes.*') ? '#fff3ed' : 'transparent' }}; transition: all 0.3s;">
            <span style="font-size: 20px;">🍲</span>
            <span style="font-weight: 600;">Plats</span>
        </a>

        <!-- Rapports -->
        <a href="{{ route('admin.reports.index') }}" style="display: flex; align-items: center; gap: 12px; padding: 15px 20px; color: {{ request()->routeIs('admin.reports.*') ? '#ff6b35' : '#666' }}; text-decoration: none; border-left: 4px solid {{ request()->routeIs('admin.reports.*') ? '#ff6b35' : 'transparent' }}; background-color: {{ request()->routeIs('admin.reports.*') ? '#fff3ed' : 'transparent' }}; transition: all 0.3s;">
            <span style="font-size: 20px;">📋</span>
            <span style="font-weight: 600;">Rapports</span>
        </a>
    </nav>

    <!-- Bottom Section -->
    <div style="border-top: 1px solid #e9ecef; margin-top: 40px; padding-top: 20px; padding-bottom: 20px;">
        <div style="padding: 0 20px; margin-bottom: 20px;">
            <p style="font-size: 12px; font-weight: 600; color: #999; text-transform: uppercase; margin: 0 0 15px 0;">MON COMPTE</p>

            <!-- Profil Link -->
            <a href="{{ route('profile.edit') }}" style="display: flex; align-items: center; gap: 12px; padding: 12px; color: #666; text-decoration: none; border-radius: 8px; transition: all 0.3s; margin-bottom: 10px;">
                <span style="font-size: 18px;">👤</span>
                <span style="font-weight: 600;">Profil</span>
            </a>

            <!-- Logout Button -->
            <form method="POST" action="{{ route('logout') }}" style="margin: 0;">
                @csrf
                <button type="submit" style="width: 100%; background-color: #ff6b35; color: white; border: none; padding: 12px; border-radius: 20px; font-size: 14px; font-weight: 700; cursor: pointer; display: flex; align-items: center; justify-content: center; gap: 8px; transition: all 0.3s;" onmouseover="this.style.backgroundColor='#ff5a1f'" onmouseout="this.style.backgroundColor='#ff6b35'">
                    <span>🔐</span>
                    <span>DECONNEXION</span>
                </button>
            </form>
        </div>
    </div>
</div>
