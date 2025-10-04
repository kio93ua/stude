<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>{{ config('app.name', 'Study Buddy') }}</title>
  <meta name="description" content="Study Buddy — сучасна школа англійської. Засновниця: Іветта Тимканич. Розмовна практика, граматика, підготовка до іспитів.">
  <meta name="theme-color" content="#118C8C">
  <style>[x-cloak]{display:none!important}</style>
  <link rel="preload" as="image" href="{{ asset('images/logo.png') }}" imagesizes="(min-width:1024px) 200px, 140px">
  @vite(['resources/css/app.css','resources/js/app.js'])
</head>
<body class="min-h-screen font-sans text-secondary bg-gradient-to-b from-brand-mint/50 via-white to-brand-mint/20">

    @include('partials.header')

  <main id="main-content">
    @yield('content')
  </main>

    @include('partials.footer')

  @stack('scripts')
</body>
</html>