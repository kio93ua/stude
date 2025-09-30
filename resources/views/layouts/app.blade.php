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

  <header class="sticky top-0 z-50 border-b border-brand-mint/50 bg-white/85 backdrop-blur supports-[backdrop-filter]:backdrop-blur">
    <div class="mx-auto flex max-w-7xl items-center justify-between px-6 py-2.5 md:py-3">
      <a href="{{ url('/') }}" class="inline-flex items-center flex-shrink-0" aria-label="{{ config('app.name', 'Study Buddy') }}">
        <img
          src="{{ asset('images/logo.png') }}"
          alt="{{ config('app.name', 'Study Buddy') }}"
          width="263" height="106"
          class="block h-10 md:h-15 w-auto object-contain select-none"
          loading="eager" decoding="async" fetchpriority="high"
          sizes="(min-width:1024px) 200px, 140px"
        />
      </a>

      <nav class="hidden items-center gap-6 text-sm font-medium text-secondary/80 md:flex" aria-label="Головна навігація">
        <a href="#services" class="transition hover:text-primary">Послуги</a>
        <a href="#approach" class="transition hover:text-primary">Підхід</a>
        <a href="#about" class="transition hover:text-primary">Про школа</a>
        <a href="#testimonials" class="transition hover:text-primary">Відгуки</a>
        <a href="#contact" class="transition hover:text-primary">Запис</a>
      </nav>

      <div class="flex items-center gap-3">
        <a href="{{ route('login') }}"
           class="rounded-full border border-primary/30 px-4 py-2 text-sm font-semibold text-primary transition hover:border-primary hover:text-secondary">
          Кабінет
        </a>
        <a href="#contact"
           class="rounded-full bg-accent px-4 py-2 text-sm font-semibold text-slate-900 shadow-md shadow-accent/30 transition hover:bg-warn hover:shadow-warn/30">
          Записатися
        </a>
      </div>
    </div>
  </header>

  <main id="main-content">
    @yield('content')
  </main>

  <footer class="mt-16 border-t border-brand-mint/50 bg-white/90">
    <div class="mx-auto grid max-w-7xl gap-8 px-6 py-10 md:grid-cols-3">
      <div>
        <p class="text-lg font-semibold text-secondary">{{ config('app.name', 'Study Buddy') }}</p>
        <p class="mt-2 text-sm text-secondary/70">
          Сучасна школа англійської. Засновниця — <span class="font-medium text-secondary">Іветта Тимканич</span>.
        </p>
      </div>
      <div class="space-y-2 text-sm text-secondary/80">
        <p><a href="mailto:hello@studybuddy.school" class="hover:text-primary">hello@studybuddy.school</a></p>
        <p><a href="tel:+380671234567" class="hover:text-primary">+38 (067) 123 45 67</a></p>
      </div>
      <div class="flex items-center md:justify-end">
        <a href="#contact"
           class="rounded-full bg-primary px-5 py-2 text-sm font-semibold text-white shadow-md shadow-primary/30 transition hover:bg-secondary">
          Безкоштовна консультація
        </a>
      </div>
    </div>
    <div class="border-t border-brand-mint/40">
      <div class="mx-auto max-w-7xl px-6 py-4 text-xs text-secondary/60">
        &copy; {{ now()->year }} {{ config('app.name', 'Study Buddy') }}. Усі права захищено.
      </div>
    </div>
  </footer>

  @stack('scripts')
</body>
</html>