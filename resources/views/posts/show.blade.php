@extends('layouts.app')

@section('content')

{{-- КАСТОМНІ СТИЛІ — не залежать від Tailwind --}}
<style>
  .sb-cover{position:relative; overflow:hidden; border-radius:1.5rem; background:rgba(255,255,255,.6); box-shadow:0 1px 2px rgba(0,0,0,.06); border:1px solid rgba(61,193,170,.4)}
  .sb-cover__box{width:100%; max-height:400px; aspect-ratio:16/9;}
  .sb-cover__img{width:100%; height:100%; object-fit:contain; object-position:center; display:block; background:#eef9f6;}
  @supports not (aspect-ratio: 16/9) {
    .sb-cover__box{position:relative;}
    .sb-cover__box::before{content:""; display:block; padding-top:56.25%;}
    .sb-cover__img{position:absolute; inset:0;}
  }
  .sb-video{margin-top:3rem; overflow:hidden; border-radius:1rem; background:#000; box-shadow:0 10px 15px -3px rgba(0,0,0,.1);}
  .sb-video__box{width:100%; aspect-ratio:16/9; min-height:400px;}
  @media (min-width:640px){ .sb-video__box{min-height:420px;} }
  @media (min-width:768px){ .sb-video__box{min-height:460px;} }
  @media (min-width:1024px){ .sb-video__box{min-height:500px;} }
  .sb-video__frame{width:100%; height:100%; border:0; display:block;}

  
</style>

{{-- ===== GALLERY: contain + lightbox ===== --}}
@push('styles')
<style>
  
  .sb-card { border-radius: 1rem; overflow: hidden; border: 1px solid rgba(61,193,170,.4); background: rgba(255,255,255,.7); }
  .sb-gal-box { width:100%; aspect-ratio: 4/3; background: #f6fbf9; position: relative; }
  .sb-gal-img { width:100%; height:100%; object-fit: contain; object-position: center; display:block; }
  .sb-gal-img:focus-visible { outline: 2px solid rgba(61,193,170,.8); outline-offset: 2px; }

  @supports not (aspect-ratio: 4/3) {
    .sb-gal-box { position:relative; }
    .sb-gal-box::before { content:""; display:block; padding-top:75%; }
    .sb-gal-img { position:absolute; inset:0; }
  }
:root{ --lb-gap: clamp(12px, 4vw, 28px); }  /* відступ навколо фото */

/* <dialog> займає екран; прихований доки немає [open] */
.sb-lightbox{
  position: fixed;
  inset: 0;
  margin: 0;
  border: 0;
  padding: 0;                     
  background: transparent;
  display: block;
  overflow: hidden;               
}
.sb-lightbox:not([open]){ display: none; }


.sb-lightbox::backdrop{
  background: rgba(0,0,0,.55);
  backdrop-filter: blur(3px);
  -webkit-backdrop-filter: blur(3px);
}


.sb-lightbox__wrap{
  position: fixed;
  left: 50%;
  top: 50%;
  transform: translate(-50%, -50%);
  background: transparent;

  
  max-width:  calc(100vw  - 2*var(--lb-gap));
  max-height: calc(100dvh - 2*var(--lb-gap));   
}


.sb-lightbox__img{
  display: block;
  width: auto;
  height: auto;
  max-width: 100%;
  max-height: 100%;
  object-fit: contain;
  object-position: center;
  background: transparent;
}


.sb-lightbox:not([open]) .sb-lightbox__close{ display:none!important; }
.sb-lightbox[open] .sb-lightbox__close{
  position: absolute; top: .75rem; right: .75rem;
  z-index: 2;
  width: 44px; height: 44px;
  border-radius: 9999px;
  display: inline-flex; align-items: center; justify-content: center;
  border: 1px solid rgba(255,255,255,.3);
  background: #0FA6A0; color: #fff;
  box-shadow: 0 4px 10px rgba(0,0,0,.30);
}
.sb-lightbox[open] .sb-lightbox__close svg{
  width: 20px; height: 20px;
  stroke: #fff; stroke-width: 2.5;  
  pointer-events: none;
}


html.modal-open, body.modal-open { overflow: hidden; }
@media (max-width: 640px), (hover: none) and (pointer: coarse) {
  :root{
    
    --lb-gap: 8px;
  }

  
  .sb-lightbox__wrap{
    
    width:  calc(100svw - env(safe-area-inset-left, 0px) - env(safe-area-inset-right, 0px) - 2*var(--lb-gap));
    height: calc(100svh - env(safe-area-inset-top,  0px) - env(safe-area-inset-bottom, 0px) - 2*var(--lb-gap));
  }


  .sb-lightbox__img{
    width: 100%;
    height: 100%;
    object-fit: contain; 
  }


  .sb-lightbox[open] .sb-lightbox__close{
    top:  calc(.5rem + env(safe-area-inset-top, 0px));
    right: calc(.5rem + env(safe-area-inset-right, 0px));
  }
}


@media (max-width: 380px) {
  :root{ --lb-gap: 6px; }
}



  .surface-card{ background: rgba(255,255,255,.72); backdrop-filter: saturate(130%) blur(2px); -webkit-backdrop-filter: saturate(130%) blur(2px); }
  section.page-section{ background: transparent; }

  @supports not (aspect-ratio: 16/9) {
    .sb-cover__box,
    .sb-video__box,
    .sb-gal-box {position:relative; height:auto;}
    .sb-cover__box::before,
    .sb-video__box::before{content:""; display:block; padding-top:56.25%;}
    .sb-gal-box::before{content:""; display:block; padding-top:75%;}
    .sb-cover__img,
    .sb-video__frame,
    .sb-gal-img {position:absolute; inset:0; width:100%; height:100%;}
  }
</style>
@endpush

<section class="py-12">
  <div class="mx-auto max-w-7xl px-6 lg:px-8">

    {{-- !!! БУЛО: lg:grid lg:grid-cols-12 ...  → СТАВИМО завжди одну колонку --}}
    <div class="grid grid-cols-1 gap-10 xl:gap-14">

      {{-- MAIN --}}
      <div>
        {{-- Хлібні крихти --}}
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

        {{-- Заголовок --}}
        <h1 class="mt-4 text-4xl sm:text-5xl font-bold tracking-tight text-secondary">
          {{ $post->title }}
        </h1>

        {{-- Мета --}}
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

        {{-- Компактний хедер (excerpt + обкладинка) --}}
        @php $coverExists = !empty($post->cover_image_path); @endphp
        @if ($coverExists || $post->excerpt)
          <section class="mt-8 grid grid-cols-1 gap-6 md:grid-cols-12 md:items-start">
            <div class="md:col-span-7">
              @if ($post->excerpt)
                <div class="h-full rounded-3xl border border-brand-mint/30 bg-brand-mint/15 p-6">
                  <p class="text-lg text-secondary/85">{{ $post->excerpt }}</p>
                </div>
              @endif
            </div>

            {{-- RIGHT: cover --}}
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

        {{-- ТІЛО СТАТТІ: на всю ширину контейнера --}}
        <article class="mt-10">
          <div class="prose prose-lg prose-slate max-w-none prose-a:text-primary hover:prose-a:text-secondary prose-strong:text-secondary prose-img:rounded-2xl dark:prose-invert">
            {{-- було max-w-[70ch] → прибрано, щоб текст тягнувся на всю ширину --}}
            <div class="leading-relaxed space-y-6 md:space-y-8">
              {!! $post->body !!}
            </div>
          </div>
        </article>

        {{-- Відео, якщо є --}}
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

        {{-- Галерея --}}
        @if ($post->gallery_images)
          <section class="mt-12">
            <h2 class="text-xl font-semibold text-secondary">Галерея</h2>
            <div class="mt-4 grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
              @foreach ($post->gallery_images as $image)
                @php
                  $imgPath = ltrim($image, '/');
                  $ext  = pathinfo($imgPath, PATHINFO_EXTENSION);
                  $name = substr($imgPath, 0, -(strlen($ext)+1));
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
                      data-full="{{ $imgSrc }}"
                      tabindex="0"
                    >
                  </div>
                </figure>
              @endforeach
            </div>
          </section>
        @endif

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

      {{-- !!! САЙДБАР ПЕРЕНЕСЕНО В САМ КІНЕЦЬ І ПЕРЕТВОРЕНО НА ЗВИЧАЙНІ СЕКЦІЇ --}}
      <aside class="mt-4 space-y-6" aria-label="Сайдбар (додатково)">
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

{{-- LIGHTBOX --}}

<dialog id="sb-lightbox" class="sb-lightbox">
  <div class="sb-lightbox__wrap">
    <button type="button" class="sb-lightbox__close" data-close aria-label="Закрити">
      <svg aria-hidden="true" viewBox="0 0 24 24" fill="none">
        <path d="M6 6l12 12M18 6L6 18" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
      </svg>
    </button>
    <img id="sb-lightbox-img" class="sb-lightbox__img" alt="">
  </div>
</dialog>



@push('scripts')
<script>
window.addEventListener('DOMContentLoaded', () => {
  const dlg = document.getElementById('sb-lightbox');
  if (!dlg) return;

  const img = dlg.querySelector('#sb-lightbox-img');
  const closeBtn = dlg.querySelector('[data-close]');

  const addScrollLock = () => {
    document.documentElement.classList.add('modal-open');
    document.body.classList.add('modal-open');
  };
  const removeScrollLock = () => {
    document.documentElement.classList.remove('modal-open');
    document.body.classList.remove('modal-open');
  };

  function openLightbox(ev){
    ev.preventDefault();
    const target = ev.currentTarget || ev.target;
    const src = target.getAttribute('data-full') || target.currentSrc || target.src || '';

    // скиньмо попередні слухачі/стан
    img.onload = null;
    img.onerror = null;

    if (!src) return;

    // 1) Спочатку підпишемось на події
    img.onload = () => {
      // 2) Лише коли зображення точно готове — відкриваємо модалку
      if (dlg.hasAttribute('open')) dlg.close();
      dlg.showModal();
      addScrollLock();
      closeBtn.focus();
    };
    img.onerror = () => {
      console.warn('Lightbox: не вдалося завантажити', src);
      // опційно: показати тост/повідомлення
    };

    // 3) ТЕПЕР задаємо src (тригеримо завантаження)
    img.alt = target.getAttribute('alt') || '';
    img.src = src;
  }

  function closeLightbox(){
    if (dlg.hasAttribute('open')) dlg.close();
    img.removeAttribute('src');
    img.removeAttribute('alt');
    removeScrollLock();
  }

  dlg.addEventListener('click', (e) => {
    const wrap = dlg.querySelector('.sb-lightbox__wrap');
    if (!wrap) return;
    const r = wrap.getBoundingClientRect();
    const inside = e.clientX >= r.left && e.clientX <= r.right && e.clientY >= r.top && e.clientY <= r.bottom;
    if (!inside) closeLightbox();
  });
  closeBtn.addEventListener('click', closeLightbox);
  dlg.addEventListener('cancel', (e) => { e.preventDefault(); closeLightbox(); });
  dlg.addEventListener('close', removeScrollLock);

  // навішуємо на прев’ю
  document.querySelectorAll('.sb-gal-img').forEach(el => {
    el.addEventListener('click', openLightbox);
    el.addEventListener('keydown', (ev) => {
      if (ev.key === 'Enter' || ev.key === ' ') openLightbox(ev);
    });
  });
});

</script>
@endpush

@endsection
