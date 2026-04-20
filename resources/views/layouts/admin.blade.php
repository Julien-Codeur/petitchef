<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
        
        <!-- Chart.js Library -->
        <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.js"></script>
    </head>
    <body class="font-sans antialiased">
        <div style="display: flex; min-height: 100vh; background-color: #f8f9fa;">
            <!-- Sidebar -->
            <x-admin-sidebar />

            <!-- Main Content -->
            <div style="flex: 1; margin-left: 250px; display: flex; flex-direction: column;">
                <!-- Page Heading -->
                @isset($header)
                    <header style="background-color: white; box-shadow: 0 1px 3px rgba(0,0,0,0.1); padding: 20px 30px;">
                        <div style="max-width: 1200px;">
                            {{ $header }}
                        </div>
                    </header>
                @endisset

                <!-- Page Content -->
                <main style="flex: 1; padding: 30px;">
                    {{ $slot }}
                </main>
            </div>
        </div>
    </body>
</html>
