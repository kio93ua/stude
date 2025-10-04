<footer class="bg-secondary text-white">
  <div class="mx-auto max-w-7xl px-6 py-10 md:py-12">
    <div class="grid grid-cols-1 gap-6 md:grid-cols-2 md:gap-8 lg:grid-cols-4">
      <!-- 1) Бренд + опис -->
      <section aria-labelledby="ft-brand">
        <!-- БЕЙДЖ ДЛЯ ЛОГО (glass + ring-offset під фон) -->
       <!-- Лого 3: без підкладки, більше -->
<a href="{{ url('/') }}"
   aria-label="{{ config('app.name', 'Study Buddy') }}"
   class="inline-flex items-center">
  <img
    src="{{ asset('images/logo6.png') }}"
    alt="{{ config('app.name', 'Study Buddy') }}"
    width="284" height="192"
    loading="lazy" decoding="async"
    class="block h-10 sm:h-20 md:h-28 lg:h-32 xl:h-38 w-auto select-none" />
</a>


        <p id="ft-brand" class="mt-4 text-base leading-7 text-white/90">
          Сучасна школа англійської. Засновниця — <span class="font-semibold text-white">Іветта Тимканич</span>.
          Розмовна практика, граматика, підготовка до іспитів.
        </p>
      </section>

      <!-- 2) Навігація: Сторінки -->
      <nav aria-label="Сторінки">
        <h3 class="text-sm font-semibold text-white">Сторінки</h3>
        <ul class="mt-4 space-y-2">
          @foreach ([['#services','Послуги'],['#approach','Підхід'],['#about','Про школу'],['#testimonials','Відгуки'],['#contact','Запис']] as [$href,$label])
            <li>
              <a href="{{ $href }}"
                 class="inline-block text-white/90 hover:text-white transition
                        focus:outline-none focus-visible:ring-2 focus-visible:ring-white/80
                        focus-visible:ring-offset-2 focus-visible:ring-offset-[rgb(6_117_124)]">
                {{ $label }}
              </a>
            </li>
          @endforeach
        </ul>
      </nav>

      <!-- 3) Навігація: Ресурси -->
      <nav aria-label="Ресурси">
        <h3 class="text-sm font-semibold text-white">Ресурси</h3>
        <ul class="mt-4 space-y-2">
          @foreach ([[url('/blog'),'Блог'],[url('/faq'),'FAQ'],[url('/support'),'Підтримка'],[url('/contacts'),'Контакти']] as [$href,$label])
            <li>
              <a href="{{ $href }}"
                 class="inline-block text-white/90 hover:text-white transition
                        focus:outline-none focus-visible:ring-2 focus-visible:ring-white/80
                        focus-visible:ring-offset-2 focus-visible:ring-offset-[rgb(6_117_124)]">
                {{ $label }}
              </a>
            </li>
          @endforeach
        </ul>
      </nav>

      <!-- 4) Контакти + соцмережі -->
      <section aria-labelledby="ft-contacts">
        <h3 id="ft-contacts" class="text-sm font-semibold text-white">Контакти</h3>
        <ul class="mt-4 space-y-2 text-white/90">
          <li>
            <a href="tel:+380671234567"
               class="hover:text-white transition
                      focus:outline-none focus-visible:ring-2 focus-visible:ring-white/80
                      focus-visible:ring-offset-2 focus-visible:ring-offset-[rgb(6_117_124)]">
              +38 (067) 123 45 67
            </a>
          </li>
          <li>
            <a href="mailto:hello@studybuddy.school"
               class="hover:text-white transition
                      focus:outline-none focus-visible:ring-2 focus-visible:ring-white/80
                      focus-visible:ring-offset-2 focus-visible:ring-offset-[rgb(6_117_124)]">
              hello@studybuddy.school
            </a>
          </li>
          <li>м. Ужгород, вул. Прикладна, 1</li>
        </ul>

        <div class="mt-5 flex gap-3">
          @php
            $icons = [
              ['https://www.tiktok.com','TikTok','M21 8.5a6.7 6.7 0 0 1-5.3-2.6V17a5.5 5.5 0 1 1-5.5-5.5c.2 0 .5 0 .7.1V14a2.5 2.5 0 1 0 1.8 2.4V3h2.7a6.6 6.6 0 0 0 5.6 3v2.5z'],
              ['https://instagram.com','Instagram','M7 2h10a5 5 0 0 1 5 5v10a5 5 0 0 1-5 5H7a5 5 0 0 1-5-5V7a5 5 0 0 1 5-5m0 2a3 3 0 0 0-3 3v10a3 3 0 0 0 3 3h10a3 3 0 0 0 3-3V7a3 3 0 0 0-3-3zm11 2a1 1 0 1 1 0 2 1 1 0 0 1 0-2M12 7a5 5 0 1 1 0 10 5 5 0 0 1 0-10'],
              ['https://facebook.com','Facebook','M13 10h3l-.4 3H13v9h-3v-9H8v-3h2V8.5C10 6.6 11 5 13.8 5H16v3h-1.6c-.8 0-1.4.3-1.4 1.1z'],
              ['https://youtube.com','YouTube','M23.5 6.2a3 3 0 0 0-2.1-2.1C19.4 3.5 12 3.5 12 3.5s-7.4 0-9.4.6A3 3 0 0 0 .5 6.2 31.3 31.3 0 0 0 0 12a31.3 31.3 0 0 0 .6 5.8 3 3 0 0 0 2.1 2.1c2 .6 9.3 .6 9.3 .6s7.4 0 9.4-.6a3 3 0 0 0 2.1-2.1c.4-1.9 .6-3.8 .6-5.8s-.2-3.9 -.6-5.8zM9.8 15.5V8.5l6.2 3.5z'],
            ];
          @endphp
          @foreach ($icons as [$href,$label,$d])
            <a href="{{ $href }}" aria-label="{{ $label }}"
               class="inline-flex h-9 w-9 items-center justify-center rounded-full border border-white/25 text-white/90 hover:text-white transition
                      focus:outline-none focus-visible:ring-2 focus-visible:ring-white/80
                      focus-visible:ring-offset-2 focus-visible:ring-offset-[rgb(6_117_124)]">
              <svg viewBox="0 0 24 24" class="h-5 w-5" aria-hidden="true"><path fill="currentColor" d="{{ $d }}"/></svg>
            </a>
          @endforeach
        </div>
      </section>
    </div>

    <!-- Низ -->
    <div class="mt-6 border-t border-white/20 pt-3">
      <div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between text-sm text-white/90">
        <p>© {{ date('Y') }} {{ config('app.name', 'Study Buddy') }}. Усі права захищено.</p>
        <div class="flex items-center gap-6">
          <a href="{{ url('/terms') }}"
             class="hover:text-white transition focus:outline-none focus-visible:ring-2 focus-visible:ring-white/80 focus-visible:ring-offset-2 focus-visible:ring-offset-[rgb(6_117_124)]">Умови</a>
          <a href="{{ url('/privacy') }}"
             class="hover:text-white transition focus:outline-none focus-visible:ring-2 focus-visible:ring-white/80 focus-visible:ring-offset-2 focus-visible:ring-offset-[rgb(6_117_124)]">Політика</a>
          <a href="#top"
             class="hover:text-white transition focus:outline-none focus-visible:ring-2 focus-visible:ring-white/80 focus-visible:ring-offset-2 focus-visible:ring-offset-[rgb(6_117_124)]">Вгору ↑</a>
        </div>
      </div>
    </div>
  </div>
</footer>
