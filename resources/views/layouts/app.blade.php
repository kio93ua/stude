<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>{{ config('app.name', 'Study Buddy') }}</title>
  <meta name="description" content="Study Buddy — сучасна школа англійської. Засновниця: Іветта Тимканич. Розмовна практика, граматика, підготовка до іспитів.">
  <meta name="theme-color" content="#118C8C">

  {{-- важливо: ЖОДНИХ Google Fonts тут. Шрифт підтягуємо з @fontsource у app.css,
       а CLS гасимо через metric overrides (size-adjust, ascent/descent/line-gap-override). --}}

  {{-- дрібний анти-флік до монтування Vue-острівців --}}
  <style>[x-cloak]{display:none!important}</style>

  @vite(['resources/css/app.css','resources/js/app.js'])
</head>
<body class="min-h-screen font-sans text-secondary bg-gradient-to-b from-brand-mint/50 via-white to-brand-mint/20">

  <!-- Header -->
  <header class="sticky top-0 z-50 border-b border-brand-mint/50 bg-white/80 backdrop-blur supports-[backdrop-filter]:backdrop-blur">
    <div class="mx-auto flex max-w-7xl items-center justify-between px-6 py-4">
      <a href="{{ url('/') }}" class="group inline-flex items-center gap-2" aria-label="{{ config('app.name', 'Study Buddy') }}">
        <span class="inline-flex h-9 w-9 items-center justify-center rounded-xl bg-accent/90 shadow-md shadow-accent/30 ring-1 ring-accent/50">
          <span class="block h-2 w-4 -rotate-12 rounded-full bg-warn"></span>
        </span>
        <span class="text-lg font-semibold tracking-tight text-secondary transition group-hover:text-primary">
          {{ config('app.name', 'Study Buddy') }}
        </span>
      </a>

      <nav class="hidden items-center gap-6 text-sm font-medium text-secondary/80 md:flex" aria-label="Головна навігація">
        <a href="#services" class="transition hover:text-primary">Послуги</a>
        <a href="#approach" class="transition hover:text-primary">Підхід</a>
        <a href="#about" class="transition hover:text-primary">Про школу</a>
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

  <!-- Footer -->
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
