@extends('layouts.app')

@section('content')

{{-- КАСТОМНІ СТИЛІ — не залежать від Tailwind --}}
<style>
  .sb-cover{position:relative; overflow:hidden; border-radius:1.5rem; background:rgba(255,255,255,.6); box-shadow:0 1px 2px rgba(0,0,0,.06); border:1px solid rgba(61,193,170,.4)}
  .sb-cover__box{width:100%; max-height:400px; aspect-ratio:16/9;}
  .sb-cover__img{width:100%; height:100%; object-fit:contain; object-position:center; display:block; background:#eef9f6;} /* ВАЖЛИВО: contain */
  @supports not (aspect-ratio: 16/9) {
    .sb-cover__box{position:relative;}
    .sb-cover__box::before{content:""; display:block; padding-top:56.25%;}
    .sb-cover__img{position:absolute; inset:0;}
  }
  /* ====== VIDEO (одна секція) ====== */
  .sb-video{margin-top:3rem; overflow:hidden; border-radius:1rem; background:#000; box-shadow:0 10px 15px -3px rgba(0,0,0,.1);}
  .sb-video__box{width:100%; aspect-ratio:16/9; min-height:400px;}
  @media (min-width:640px){ .sb-video__box{min-height:420px;} }
  @media (min-width:768px){ .sb-video__box{min-height:460px;} }
  @media (min-width:1024px){ .sb-video__box{min-height:500px;} }
  .sb-video__frame{width:100%; height:100%; border:0; display:block;}

  /* ====== GALLERY ====== */
  {{-- ===== GALLERY: contain + lightbox ===== --}}
@push('styles')
<style>

  .sb-card { border-radius: 1rem; overflow: hidden; border: 1px solid rgba(61,193,170,.4); background: rgba(255,255,255,.7); }
  .sb-gal-box { width:100%; aspect-ratio: 4/3; background: #f6fbf9; position: relative; }
  .sb-gal-img { width:100%; height:100%; object-fit: contain; object-position: center; display:block; }
  .sb-gal-img:focus-visible { outline: 2px solid rgba(61,193,170,.8); outline-offset: 2px; }

  /* Фолбек для старих браузерів без aspect-ratio */
  @supports not (aspect-ratio: 4/3) {
    .sb-gal-box { position:relative; }
    .sb-gal-box::before { content:""; display:block; padding-top:75%; }
    .sb-gal-img { position:absolute; inset:0; }
  }

  /* LIGHTBOX (native <dialog>) */
  .sb-lightbox::backdrop { background: rgba(0,0,0,.6); }
  .sb-lightbox { border: 0; padding: 0; border-radius: 1rem; overflow: hidden; max-width: 92vw; max-height: 92vh; background: #0b0b0b; }
  .sb-lightbox__wrap { position: relative; width: min(92vw, 1280px); height: min(92vh, 820px); }
  .sb-lightbox__img { width:100%; height:100%; object-fit: contain; background:#0b0b0b; }
  .sb-lightbox__close { position:absolute; top:.5rem; right:.5rem; background: rgba(255,255,255,.08); color:#fff; border:1px solid rgba(255,255,255,.18); border-radius:.75rem; padding:.5rem .75rem; font-weight:600; }
  .sb-lightbox__close:focus-visible { outline:2px solid #3dc1aa; outline-offset: 2px; }
  

  /* Картки/блоки — напівпрозорі поверх градієнта */
  .surface-card{
    background: rgba(255,255,255,.72);
    backdrop-filter: saturate(130%) blur(2px);
    -webkit-backdrop-filter: saturate(130%) blur(2px);
  }

  /* Переконайся, що секції не “збивають” фон на білий */
  section.page-section{ background: transparent; }

  /* ====== ФОЛБЕК, якщо браузер не підтримує aspect-ratio ====== */
  @supports not (aspect-ratio: 16/9) {
    .sb-cover__box,
    .sb-video__box,
    .sb-gal-box {position:relative; height:auto;}
    .sb-cover__box::before,
    .sb-video__box::before{content:""; display:block; padding-top:56.25%;} /* 16:9 */
    .sb-gal-box::before{content:""; display:block; padding-top:75%;} /* 4:3 */
    .sb-cover__img,
    .sb-video__frame,
    .sb-gal-img {position:absolute; inset:0; width:100%; height:100%;}
  }
</style>

<section class="py-12 ">
  <div class="mx-auto max-w-7xl px-6 lg:px-8">
    <div class="lg:grid lg:grid-cols-12 lg:gap-10 xl:gap-14">

      {{-- MAIN --}}
      <div class="lg:col-span-8 xl:col-span-9">
        {{-- Breadcrumbs --}}
        <nav class="flex items-center gap-2 text-sm text-secondary/70" aria-label="Breadcrumb">
          <a href="{{ route('posts.index') }}"
             class="inline-flex items-center gap-1 font-semibold text-primary transition hover:text-secondary focus:outline-none focus-visible:ring-2 focus-visible:ring-primary/40 rounded">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5L8.25 12l7.5-7.5"/>
            </svg>
            До блогу
          </a>
          <span class="text-secondary/40" aria-hidden="true">/</span>
          <span>Публікація</span>
        </nav>

        {{-- Title --}}
        <h1 class="mt-4 text-4xl sm:text-5xl font-bold tracking-tight text-secondary">
          {{ $post->title }}
        </h1>

        {{-- Meta (без вставки відео тут) --}}
        <div class="mt-4 flex flex-wrap items-center gap-3 text-sm text-secondary/60">
          <time datetime="{{ optional($post->published_at)?->toIso8601String() }}">
            {{ optional($post->published_at)->format('d.m.Y H:i') }}
          </time>
          <span class="h-1 w-1 rounded-full bg-secondary/30"></span>
          <span>Автор: Study School</span>
          @if ($post->youtube_embed_url)
            <span class="rounded-full bg-brand-mint/30 px-3 py-1 text-xs font-semibold uppercase tracking-wide text-primary">Відео</span>
          @endif
        </div>

        {{-- ===== COMPACT HEADER: зліва excerpt, справа маленька обкладинка ===== --}}
        @php $coverExists = !empty($post->cover_image_path); @endphp
        @if ($coverExists || $post->excerpt)
          <section class="mt-8 grid grid-cols-1 gap-6 md:grid-cols-12 md:items-stretch">
            {{-- LEFT: short description --}}
            <div class="md:col-span-7">
              @if ($post->excerpt)
                <div class="h-full rounded-3xl border border-brand-mint/30 bg-brand-mint/15 p-6">
                  <p class="text-lg text-secondary/85 max-w-[70ch]">{{ $post->excerpt }}</p>
                </div>
              @endif
            </div>

            {{-- RIGHT: cover 16:9, max-h ~400px --}}
              @if ($coverExists)
    @php
      $basePath = ltrim($post->cover_image_path, '/');
      $disk     = \Illuminate\Support\Facades\Storage::disk('public');
      $ext      = pathinfo($basePath, PATHINFO_EXTENSION);
      $name     = substr($basePath, 0, -(strlen($ext)+1));

      $candidates = [480=>"{$name}-480.{$ext}", 768=>"{$name}-768.{$ext}", 1200=>"{$name}-1200.{$ext}", 1600=>"{$name}-1600.{$ext}"];
      $existing   = [];
      foreach ($candidates as $w=>$rel) if ($disk->exists($rel)) $existing[$w] = asset('storage/'.$rel);

      $coverSrc = asset('storage/'.$basePath);
    @endphp

    <figure class="md:col-span-5 sb-cover">
      <div class="sb-cover__box">
        <img
          class="sb-cover__img"
          alt="{{ $post->title }}"
          src="{{ $coverSrc }}"
          @if(!empty($existing))
            srcset="{{ collect($existing)->map(fn($url,$w)=>$url.' '.$w.'w')->implode(', ') }}"
            sizes="(min-width:1280px) 560px, (min-width:1024px) 460px, 100vw"
          @endif
          width="1280" height="720"
          loading="lazy" decoding="async"
        >
      </div>
    </figure>

            @endif
          </section>
        @endif
        {{-- ===== /COMPACT HEADER ===== --}}

        {{-- BODY: prose + внутрішній лімітер довжини рядка --}}
        <article class="mt-10">
          <div class="prose prose-lg prose-slate max-w-none prose-a:text-primary hover:prose-a:text-secondary prose-strong:text-secondary prose-img:rounded-2xl dark:prose-invert">
            <div class="max-w-[70ch] leading-relaxed space-y-6 md:space-y-8">
              {!! $post->body !!}
            </div>
          </div>
        </article>

        {{-- ===== YOUTUBE — одна секція, стабільна висота ===== --}}
        @if ($post->youtube_embed_url)
          <div class="sb-video border border-brand-mint/40">
            <div class="sb-video__box">
              <iframe
                class="sb-video__frame"
                src="{{ $post->youtube_embed_url }}"
                title="YouTube video player"
                allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share"
                allowfullscreen
                loading="lazy"
              ></iframe>
            </div>
          </div>
        @endif
        {{-- ===== /YOUTUBE ===== --}}

        {{-- GALLERY: 4/3, srcset/sizes якщо є --}}
        @if ($post->gallery_images)
  <section class="mt-12">
    <h2 class="text-xl font-semibold text-secondary">Галерея</h2>
    <div class="mt-4 grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
      @foreach ($post->gallery_images as $image)
        @php
          $imgPath = ltrim($image, '/');
          $ext  = pathinfo($imgPath, PATHINFO_EXTENSION);
          $name = substr($imgPath, 0, -(strlen($ext)+1));

          // реальні варіанти (якщо згенеровані):
          $cands = [480=>"{$name}-480.{$ext}", 768=>"{$name}-768.{$ext}", 1200=>"{$name}-1200.{$ext}"];
          $existing = [];
          foreach ($cands as $w=>$rel) {
            if (\Illuminate\Support\Facades\Storage::disk('public')->exists($rel)) $existing[$w] = asset('storage/'.$rel);
          }
          $imgSrc = asset('storage/'.$imgPath);
          $srcset = !empty($existing) ? collect($existing)->map(fn($url,$w)=>$url.' '.$w.'w')->implode(', ') : null;
        @endphp

        <figure class="sb-card">
          <div class="sb-gal-box">
            <img
              class="sb-gal-img cursor-zoom-in"
              alt="{{ $post->title }} — фото {{ $loop->iteration }}"
              src="{{ $imgSrc }}"
              @if($srcset) srcset="{{ $srcset }}" sizes="(min-width:1024px) 33vw, (min-width:640px) 50vw, 100vw" @endif
              width="1200" height="900"
              loading="lazy" decoding="async"
              data-full="{{ $imgSrc }}" {{-- можна підставити 1600w/оригінал якщо є --}}
              tabindex="0" {{-- доступність: можна відкрити Enter/Space --}}
            >
          </div>
        </figure>
      @endforeach
    </div>
  </section>
@endif

{{-- LIGHTBOX (один на сторінці, перевикористовується) --}}
<dialog id="sb-lightbox" class="sb-lightbox" aria-label="Перегляд зображення (повний розмір)">
  <div class="sb-lightbox__wrap">
    <button type="button" class="sb-lightbox__close" data-close>Закрити ✕</button>
    <img id="sb-lightbox-img" class="sb-lightbox__img" alt="">
  </div>
</dialog>

@push('scripts')
<script>
(function(){
  const dlg = document.getElementById('sb-lightbox');
  const img = document.getElementById('sb-lightbox-img');
  const closeBtn = dlg.querySelector('[data-close]');

  // Відкрити по кліку або Enter/Space
  function openLightbox(ev){
    ev.preventDefault();
    const target = ev.currentTarget;
    const src = target.getAttribute('data-full') || target.currentSrc || target.src;
    const alt = target.getAttribute('alt') || '';
    img.src = src;
    img.alt = alt;
    if (typeof dlg.showModal === 'function') { dlg.showModal(); }
    else { dlg.setAttribute('open',''); } // дуже старі браузери
    closeBtn.focus();
  }

  // Закриття
  function closeLightbox(){ 
    if (typeof dlg.close === 'function') dlg.close();
    else dlg.removeAttribute('open');
    img.removeAttribute('src');
    img.removeAttribute('alt');
  }

  // Клік поза зображенням — закрити
  dlg.addEventListener('click', (e) => {
    const rect = dlg.querySelector('.sb-lightbox__wrap').getBoundingClientRect();
    const inDialog = (e.clientX >= rect.left && e.clientX <= rect.right &&
                      e.clientY >= rect.top  && e.clientY <= rect.bottom);
    if (!inDialog) closeLightbox();
  });

  // Кнопка закриття + Esc
  closeBtn.addEventListener('click', closeLightbox);
  dlg.addEventListener('cancel', (e) => { e.preventDefault(); closeLightbox(); }); // Esc

  // Призначаємо обробники всім зображенням галереї
  document.querySelectorAll('.sb-gal-img').forEach(el => {
    el.addEventListener('click', openLightbox);
    el.addEventListener('keydown', (ev) => {
      if (ev.key === 'Enter' || ev.key === ' ') { openLightbox.call(ev.currentTarget, ev); }
    });
  });

  // Простий фокус-трап (мінімальний)
  dlg.addEventListener('keydown', (ev) => {
    if (ev.key !== 'Tab') return;
    const focusables = dlg.querySelectorAll('button,[href],input,select,textarea,[tabindex]:not([tabindex="-1"])');
    if (!focusables.length) return;
    const first = focusables[0], last = focusables[focusables.length - 1];
    if (ev.shiftKey && document.activeElement === first) { last.focus(); ev.preventDefault(); }
    else if (!ev.shiftKey && document.activeElement === last) { first.focus(); ev.preventDefault(); }
  });
})();
</script>
@endpush

        {{-- Інші матеріали --}}
        @if ($recentPosts->isNotEmpty())
          <section class="mt-16 border-t border-brand-mint/30 pt-8">
            <h2 class="text-xl font-semibold text-secondary">Інші матеріали</h2>
            <div class="mt-4 grid gap-4 grid-cols-1 sm:grid-cols-2 lg:grid-cols-3">
              @foreach ($recentPosts as $recent)
                <a href="{{ route('posts.show', $recent) }}"
                   class="group flex items-start justify-between rounded-xl border border-transparent bg-white/70 px-4 py-3 transition hover:border-brand-mint/40 hover:bg-white focus:outline-none focus-visible:ring-2 focus-visible:ring-primary/40">
                  <div>
                    <p class="text-sm uppercase tracking-wide text-secondary/60">{{ optional($recent->published_at)->format('d.m.Y') }}</p>
                    <p class="mt-1 text-base font-semibold text-secondary group-hover:text-primary">{{ $recent->title }}</p>
                  </div>
                  <svg xmlns="http://www.w3.org/2000/svg" class="mt-1 h-5 w-5 text-primary transition group-hover:translate-x-[2px]" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5"/>
                  </svg>
                </a>
              @endforeach
            </div>
          </section>
        @endif
      </div>

      {{-- SIDEBAR --}}
      <aside class="mt-12 lg:mt-0 lg:col-span-4 xl:col-span-3 lg:sticky lg:top-24 space-y-6" aria-label="Сайдбар">
        {{-- Інфо --}}
        <section class="rounded-2xl border border-brand-mint/30 bg-white/70 p-5">
          <h2 class="text-sm font-semibold uppercase tracking-wide text-secondary/70">Інформація</h2>
          <dl class="mt-3 space-y-1 text-sm text-secondary/80">
            <div class="flex items-center justify-between">
              <dt class="text-secondary/60">Автор</dt>
              <dd class="font-medium">Study School</dd>
            </div>
            <div class="flex items-center justify-between">
              <dt class="text-secondary/60">Опубліковано</dt>
              <dd class="font-medium">{{ optional($post->published_at)->format('d.m.Y H:i') }}</dd>
            </div>
          </dl>
        </section>

        {{-- Пошук --}}
        <section class="rounded-2xl border border-brand-mint/30 bg-white/70 p-5">
          <h2 class="text-sm font-semibold uppercase tracking-wide text-secondary/70">Пошук</h2>
          <form action="{{ route('posts.index') }}" method="GET" class="mt-3">
            <label class="sr-only" for="q">Пошук по блогу</label>
            <div class="flex">
              <input id="q" name="q" type="search" placeholder="Введіть запит…"
                     class="w-full rounded-l-xl border border-brand-mint/40 bg-white px-3 py-2 text-sm text-secondary placeholder-secondary/50 focus:border-primary focus:outline-none focus-visible:ring-2 focus-visible:ring-primary/30"
                     value="{{ request('q') }}">
              <button type="submit"
                      class="rounded-r-xl bg-primary px-4 py-2 text-sm font-semibold text-white transition hover:bg-secondary focus:outline-none focus-visible:ring-2 focus-visible:ring-primary/40">
                Знайти
              </button>
            </div>
          </form>
        </section>

        {{-- Категорії / Теги --}}
        @if (($post->categories ?? null) || ($post->tags ?? null))
          <section class="rounded-2xl border border-brand-mint/30 bg-white/70 p-5">
            <h2 class="text-sm font-semibold uppercase tracking-wide text-secondary/70">Категорії та теги</h2>

            @if (!empty($post->categories) && $post->categories->isNotEmpty())
              <div class="mt-3">
                <h3 class="text-xs font-semibold uppercase tracking-wide text-secondary/60">Категорії</h3>
                <div class="mt-2 flex flex-wrap gap-2">
                  @foreach ($post->categories as $cat)
                    <a href="{{ route('posts.index', ['category' => $cat->slug]) }}"
                       class="inline-flex items-center rounded-full border border-brand-mint/40 bg-white px-3 py-1 text-xs font-medium text-secondary transition hover:border-brand-mint/60 hover:text-primary focus:outline-none focus-visible:ring-2 focus-visible:ring-primary/30">
                      {{ $cat->name }}
                    </a>
                  @endforeach
                </div>
              </div>
            @endif

            @if (!empty($post->tags) && $post->tags->isNotEmpty())
              <div class="mt-4">
                <h3 class="text-xs font-semibold uppercase tracking-wide text-secondary/60">Теги</h3>
                <div class="mt-2 flex flex-wrap gap-2">
                  @foreach ($post->tags as $tag)
                    <a href="{{ route('posts.index', ['tag' => $tag->slug]) }}"
                       class="inline-flex items-center rounded-full border border-brand-mint/40 bg-white px-3 py-1 text-xs font-medium text-secondary transition hover:border-brand-mint/60 hover:text-primary focus:outline-none focus-visible:ring-2 focus-visible:ring-primary/30">
                      #{{ $tag->name }}
                    </a>
                  @endforeach
                </div>
              </div>
            @endif
          </section>
        @endif

        {{-- До блогу --}}
        <a href="{{ route('posts.index') }}"
           class="inline-flex w-full items-center justify-center gap-2 rounded-2xl border border-brand-mint/40 bg-white/70 px-4 py-3 font-semibold text-secondary transition hover:border-brand-mint/60 hover:bg-white hover:text-primary focus:outline-none focus-visible:ring-2 focus-visible:ring-primary/40">
          <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-primary" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5L8.25 12l7.5-7.5"/>
          </svg>
          До блогу
        </a>
      </aside>

    </div>
  </div>
</section>
@endsection
