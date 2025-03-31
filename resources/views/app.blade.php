<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title inertia>{{ config('app.name', 'Laravel') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Favicon -->
        <link rel="icon" href="{{ asset('favicon.ico') }}">

        <!-- Custom Styles -->
        <link href="{{ asset('css/custom.css') }}" rel="stylesheet">

        <!-- Scripts -->
        @routes
        @vite(['resources/js/app.js', "resources/js/Pages/{$page['component']}.vue"])
        @inertiaHead
    </head>
    <body class="font-sans antialiased">
        <div id="app-loading" style="display: flex; align-items: center; justify-content: center; height: 100vh; flex-direction: column;">
            <img src="{{ asset('images/logo.png') }}" alt="Study Platform Logo" style="width: auto; height: 120px; margin-bottom: 20px;">
            <div style="background-color: #f3f4f6; border-radius: 0.5rem; padding: 1rem; text-align: center; box-shadow: 0 4px 6px rgba(0,0,0,0.1);">
                <p style="margin-bottom: 10px;">Loading application...</p>
                <div style="width: 100%; background-color: #e5e7eb; height: 4px; border-radius: 2px; overflow: hidden;">
                    <div style="width: 30%; height: 100%; background-color: #f97316; animation: loading 2s infinite ease-in-out;"></div>
                </div>
            </div>
        </div>
        
        <script>
            // Hide loading screen when app is loaded
            document.addEventListener('DOMContentLoaded', function() {
                setTimeout(function() {
                    const loading = document.getElementById('app-loading');
                    if (loading) {
                        loading.style.display = 'none';
                    }
                }, 500);
            });
        </script>
        
        <style>
            @keyframes loading {
                0% { width: 10%; margin-left: 0%; }
                50% { width: 30%; margin-left: 70%; }
                100% { width: 10%; margin-left: 0%; }
            }
        </style>
        
        @inertia
    </body>
</html>
