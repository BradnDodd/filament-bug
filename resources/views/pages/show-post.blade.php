<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Laravel</title>
    @vite('resources/css/app.css')
    @livewireStyles
    @filamentStyles
</head>
<body class="font-sans antialiased dark:bg-black dark:text-white/50">
    @livewire('show-post', ['post' => $post])

@livewire('notifications')
@livewireScripts
@filamentScripts
@vite('resources/js/app.js')
</body>
</html>
