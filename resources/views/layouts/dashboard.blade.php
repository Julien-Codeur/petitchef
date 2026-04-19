<!DOCTYPE html>
<html lang="fr" class="light">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'PetitChef') }} - {{ $title ?? 'Tableau de Bord' }}</title>

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Material Symbols -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap">

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    <style>
        body {
            font-family: 'Inter', sans-serif;
            background-color: #f5f5f5;
        }
        
        h1, h2, h3, h4, h5, h6 {
            font-family: 'Plus Jakarta Sans', sans-serif;
        }
        
        .material-symbols-outlined {
            font-variation-settings: 'FILL' 0, 'wght' 400, 'GRAD' 0, 'opsz' 24;
            color: inherit;
        }
        
        .sidebar {
            background-color: #f9f9f9;
            border-right: 1px solid #e0e0e0;
            min-height: 100vh;
        }
        
        .sidebar-nav a {
            color: #ff6b35;
            transition: all 0.3s ease;
            border-radius: 8px;
        }
        
        .sidebar-nav a:hover {
            background-color: #fff3ed;
        }
        
        .sidebar-nav a.active {
            background-color: #ffe8d6;
            font-weight: 600;
        }
        
        .btn-orange {
            background-color: #ff6b35;
            color: white;
            border: none;
            border-radius: 20px;
            padding: 8px 16px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
        }
        
        .btn-orange:hover {
            background-color: #ff5a1f;
            box-shadow: 0 4px 12px rgba(255, 107, 53, 0.3);
        }
        
        .btn-orange-outline {
            background-color: transparent;
            color: #ff6b35;
            border: 2px solid #ff6b35;
            border-radius: 20px;
            padding: 8px 16px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
        }
        
        .btn-orange-outline:hover {
            background-color: #fff3ed;
        }
        
        .stat-card {
            background-color: white;
            border-radius: 8px;
            padding: 20px;
            text-align: center;
            border: 1px solid #e0e0e0;
        }
        
        .stat-number {
            font-size: 32px;
            font-weight: 700;
            color: #ff6b35;
            font-family: 'Plus Jakarta Sans', sans-serif;
        }
        
        .stat-label {
            font-size: 13px;
            color: #666;
            margin-top: 8px;
            font-weight: 500;
        }
        
        .header-top {
            background-color: white;
            padding: 12px 20px;
            border-bottom: 1px solid #e0e0e0;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .user-info {
            display: flex;
            align-items: center;
            gap: 12px;
        }
        
        .user-name {
            font-weight: 600;
            color: #333;
            font-family: 'Plus Jakarta Sans', sans-serif;
        }
        
        .user-role {
            font-size: 12px;
            color: #999;
        }
    </style>
</head>
<body class="bg-gray-100">
    {{ $slot }}
</body>
</html>
