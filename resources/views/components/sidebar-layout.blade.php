<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'PetitChef') }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Inter', sans-serif; background-color: #f5f5f5; }
        h1, h2, h3, h4, h5, h6 { font-family: 'Plus Jakarta Sans', sans-serif; }
        
        .main-container { display: flex; height: 100vh; }
        .sidebar { width: 180px; background-color: #f9f9f9; border-right: 1px solid #e0e0e0; display: flex; flex-direction: column; }
        .sidebar-logo { padding: 20px; text-align: center; border-bottom: 1px solid #e0e0e0; }
        .sidebar-logo-text { font-size: 18px; font-weight: 700; color: #ff6b35; font-family: 'Plus Jakarta Sans', sans-serif; }
        .sidebar-nav { flex: 1; padding: 20px 0; }
        .sidebar-nav a { display: block; padding: 12px 20px; color: #ff6b35; text-decoration: none; font-size: 13px; font-weight: 600; border-radius: 8px; margin: 0 8px 8px 8px; transition: all 0.3s; }
        .sidebar-nav a:hover { background-color: #fff3ed; }
        .sidebar-nav a.active { background-color: #ffe8d6; }
        .sidebar-bottom { padding: 20px; border-top: 1px solid #e0e0e0; }
        .sidebar-bottom p { font-size: 11px; color: #999; font-weight: 600; margin-bottom: 12px; }
        .logout-btn { display: block; width: 100%; padding: 8px; background-color: #ff6b35; color: white; border: none; border-radius: 20px; font-size: 12px; font-weight: 700; cursor: pointer; text-decoration: none; text-align: center; }
        
        .main-content { flex: 1; display: flex; flex-direction: column; }
        .header { background-color: white; border-bottom: 1px solid #e0e0e0; padding: 12px 30px; display: flex; justify-content: space-between; align-items: center; }
        .header-left { display: flex; gap: 15px; align-items: center; font-size: 13px; color: #666; }
        .header-right { display: flex; gap: 15px; align-items: center; }
        .user-info { display: flex; gap: 10px; align-items: center; }
        .user-name { font-weight: 600; color: #333; font-family: 'Plus Jakarta Sans', sans-serif; }
        .user-role { font-size: 12px; color: #999; }
        
        .content { flex: 1; overflow-y: auto; padding: 30px; }
        .page-title { font-size: 28px; font-weight: 700; color: #333; margin-bottom: 20px; font-family: 'Plus Jakarta Sans', sans-serif; }
    </style>
</head>
<body>
<div class="main-container">
    <!-- Sidebar -->
    <div class="sidebar">
        <div class="sidebar-logo">
            <div class="sidebar-logo-text">🍽️ PETIT CHEF</div>
        </div>
        <nav class="sidebar-nav">
            <a href="{{ route('dashboard') }}" class="{{ request()->routeIs('dashboard') ? 'active' : '' }}">📊 Tableau de bord</a>
            <a href="#" class="">📋 Commandes</a>
            <a href="#" class="">👥 Clients</a>
            <a href="#" class="">👨‍🍳 Cuisiniers</a>
            <a href="#" class="">👤 Utilisateurs</a>
        </nav>
        <div class="sidebar-bottom">
            <p>MON COMPTE</p>
            <a href="{{ route('profile.edit') }}" style="display: block; padding: 8px 12px; color: #ff6b35; text-decoration: none; font-size: 13px; margin-bottom: 8px;">👤 Profil</a>
            <form method="POST" action="{{ route('logout') }}" style="margin: 0;">
                @csrf
                <button type="submit" class="logout-btn">🚪 DECONNEXION</button>
            </form>
        </div>
    </div>

    <!-- Main Content -->
    <div class="main-content">
        <!-- Header -->
        <div class="header">
            <div class="header-left">
                <span id="current-date"></span>
                <span id="current-time"></span>
            </div>
            <div class="header-right">
                <div class="user-info">
                    <div>
                        <div class="user-name">{{ auth()->user()->name }}</div>
                        <div class="user-role">
                            @if(auth()->user()->isAdmin())
                                Administrateur
                            @elseif(auth()->user()->isCook())
                                Cuisinier
                            @else
                                Client
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Content -->
        <div class="content">
            {{ $slot }}
        </div>
    </div>
</div>

<script>
    // Update date and time
    function updateDateTime() {
        const now = new Date();
        const options = { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' };
        const dateStr = now.toLocaleDateString('fr-FR', options);
        const timeStr = now.toLocaleTimeString('fr-FR', { hour: '2-digit', minute: '2-digit' });
        
        document.getElementById('current-date').textContent = dateStr;
        document.getElementById('current-time').textContent = timeStr;
    }
    
    updateDateTime();
    setInterval(updateDateTime, 1000);
</script>
</body>
</html>
