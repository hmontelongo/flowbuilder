<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ $title ?? 'Flow Builder' }}</title>

    {{-- Inter font for Figma design match --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @fluxAppearance

    <style>
        .flow-builder-layout {
            display: grid;
            grid-template-columns: var(--fb-sidebar-width) 1fr;
            grid-template-rows: var(--fb-topnav-height) 1fr;
            height: 100vh;
            width: 100vw;
        }

        .flow-builder-topnav {
            grid-column: 1 / -1;
            grid-row: 1;
        }

        .flow-builder-sidebar {
            grid-column: 1;
            grid-row: 2;
        }

        .flow-builder-main {
            grid-column: 2;
            grid-row: 2;
            position: relative;
            overflow: hidden;
            background-color: #f4f6f7;
            border: 1px solid #d1d5dc;
            border-top-left-radius: var(--fb-border-radius);
        }
    </style>
</head>
<body class="antialiased overflow-hidden bg-white">
    {{ $slot }}

    @fluxScripts
</body>
</html>
