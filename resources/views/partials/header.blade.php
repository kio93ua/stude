{{-- Skip link для клавіатури/скрінрідерів --}}
<a href="#main-content"
   class="sr-only focus:not-sr-only focus:absolute focus:left-4 focus:top-4 focus:z-[60] focus:rounded-lg focus:bg-white focus:px-4 focus:py-2 focus:text-slate-900 focus:shadow">
  Перейти до основного контенту
</a>

<header class="sticky top-0 z-50 border-b border-brand-mint/50 bg-white/85 backdrop-blur supports-[backdrop-filter]:backdrop-blur">
  <div class="mx-auto flex max-w-7xl items-center justify-between px-6 py-2.5 md:py-3">

    {{-- ЛОГО: вище, без спотворень (48 / 56 / 64 px) --}}
    <a href="{{ url('/') }}" class="inline-flex items-center shrink-0" aria-label="{{ config('app.name', 'Study Buddy') }}">
      <img
        src="{{ asset('images/logo.png') }}"
        alt="{{ config('app.name', 'Study Buddy') }}"
        width="263" height="106"
        class="block h-11 md:h-14 lg:h-16 w-auto object-contain select-none"
        loading="eager" decoding="async" fetchpriority="high"
        sizes="(min-width:1024px) 200px, 140px"
      />
      {{-- якщо лого “губиться” на фоні — додай до img:
           drop-shadow-[0_1px_0_rgba(255,255,255,.35)] md:drop-shadow-[0_6px_18px_rgba(0,0,0,.35)] --}}
    </a>

    {{-- Головна навігація (desktop). Якщо буде кілька <nav>, даємо aria-label --}}
    <nav class="hidden items-center gap-6 text-sm font-medium text-secondary/80 md:flex"
         aria-label="Головна навігація">
      <a href="#services" class="transition hover:text-primary">Послуги</a>
      <a href="#approach" class="transition hover:text-primary">Підхід</a>
      <a href="#about" class="transition hover:text-primary">Про школа</a>
      <a href="#testimonials" class="transition hover:text-primary">Відгуки</a>
      <a href="#contact" class="transition hover:text-primary">Запис</a>
    </nav>

    <div class="hidden md:flex items-center gap-3">
      <a href="{{ route('login') }}"
         class="rounded-full border border-primary/30 px-4 py-2 text-sm font-semibold text-primary transition hover:border-primary hover:text-secondary">
        Кабінет
      </a>
      <a href="#contact"
         class="rounded-full bg-accent px-4 py-2 text-sm font-semibold text-slate-900 shadow-md shadow-accent/30 transition hover:bg-warn hover:shadow-warn/30">
        Записатися
      </a>
    </div>

    {{-- КНОПКА-БУРГЕР (Disclosure). 44–48px, aria-expanded/controls --}}
    <button
      type="button"
      class="md:hidden inline-flex h-11 w-11 items-center justify-center rounded-lg
             focus:outline-none focus-visible:ring-2 focus-visible:ring-primary/60
             focus-visible:ring-offset-2 focus-visible:ring-offset-white"
      aria-label="Відкрити меню"
      aria-controls="mobile-nav"
      aria-expanded="false"
      id="menu-toggle"
      {{-- якщо використовуєш Vue-острівець — залиш і ці атрибути: --}}
      data-vue="MobileMenuButton"
      data-props='@json(["targetId" => "mobile-nav"])'
    >
      <svg aria-hidden="true" viewBox="0 0 24 24" class="h-6 w-6 text-secondary">
        <path fill="currentColor" d="M4 6h16v2H4zM4 11h16v2H4zM4 16h16v2H4z"/>
      </svg>
    </button>
  </div>

  {{-- Мобільна навігація під disclosure-кнопкою --}}
  <div id="mobile-nav" class="md:hidden hidden border-t border-brand-mint/50 bg-white/95 backdrop-blur">
    <nav class="px-6 py-3" aria-label="Головна навігація (мобільна)">
      <ul class="flex flex-col gap-2">
        <li><a href="#services" class="block rounded-lg px-3 py-2 text-secondary/90 hover:bg-brand-mint/15 hover:text-primary">Послуги</a></li>
        <li><a href="#approach" class="block rounded-lg px-3 py-2 text-secondary/90 hover:bg-brand-mint/15 hover:text-primary">Підхід</a></li>
        <li><a href="#about" class="block rounded-lg px-3 py-2 text-secondary/90 hover:bg-brand-mint/15 hover:text-primary">Про школа</a></li>
        <li><a href="#testimonials" class="block rounded-lg px-3 py-2 text-secondary/90 hover:bg-brand-mint/15 hover:text-primary">Відгуки</a></li>
        <li><a href="#contact" class="block rounded-lg px-3 py-2 text-secondary/90 hover:bg-brand-mint/15 hover:text-primary">Запис</a></li>
      </ul>

      <div class="mt-3 flex gap-2" aria-label="Дії">
        <a href="{{ route('login') }}" class="w-full rounded-full border border-primary/30 px-4 py-2 text-sm font-semibold text-primary text-center">Кабінет</a>
        <a href="#contact" class="w-full rounded-full bg-accent px-4 py-2 text-sm font-semibold text-slate-900 text-center">Записатися</a>
      </div>
    </nav>
  </div>
</header>

@push('scripts')
<script>
  // Легкий прогресивний тоглер, якщо Vue-острівець не підключився
  (function () {
    const btn = document.getElementById('menu-toggle');
    const panel = document.getElementById('mobile-nav');
    if (!btn || !panel) return;

    const setState = (open) => {
      btn.setAttribute('aria-expanded', String(open));
      btn.setAttribute('aria-label', open ? 'Закрити меню' : 'Відкрити меню');
      panel.classList.toggle('hidden', !open);
    };

    btn.addEventListener('click', () => {
      const open = btn.getAttribute('aria-expanded') !== 'true';
      setState(open);
      if (open) panel.querySelector('a,button')?.focus();
    });

    // Закриття по Esc, повертаємо фокус на кнопку (APG)
    panel.addEventListener('keydown', (e) => {
      if (e.key === 'Escape') {
        setState(false);
        btn.focus();
      }
    });
  })();
</script>
@endpush
