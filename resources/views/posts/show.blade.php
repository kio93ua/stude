@extends('layouts.app')

@section('content')
<section class="pb-16 pt-12">
  <div class="mx-auto max-w-4xl px-6">
    <div class="flex items-center gap-2 text-sm text-secondary/70">
      <a href="{{ route('posts.index') }}" class="inline-flex items-center gap-1 font-semibold text-primary transition hover:text-secondary">
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="h-4 w-4">
          <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5L8.25 12l7.5-7.5" />
        </svg>
        До блогу
      </a>
      <span class="text-secondary/40">/</span>
      <span>Публікація</span>
    </div>

    <h1 class="mt-4 text-4xl font-bold leading-tight text-secondary sm:text-5xl">{{ $post->title }}</h1>

    <div class="mt-4 flex flex-wrap items-center gap-3 text-sm text-secondary/60">
      <span>{{ optional($post->published_at)->format('d.m.Y H:i') }}</span>
      <span class="h-1 w-1 rounded-full bg-secondary/30"></span>
      <span>Автор: Study School</span>
      @if ($post->youtube_embed_url)
        <span class="rounded-full bg-brand-mint/30 px-3 py-1 text-xs font-semibold uppercase tracking-wide text-primary">Відео</span>
      @endif
    </div>

    @if ($post->cover_image_path)
      <figure class="mt-8 overflow-hidden rounded-3xl border border-brand-mint/40">
        <img
          src="{{ asset('storage/' . ltrim($post->cover_image_path, '/')) }}"
          alt="{{ $post->title }}"
          class="h-auto w-full object-cover"
          loading="lazy"
        >
      </figure>
    @endif

    @if ($post->excerpt)
      <p class="mt-8 rounded-2xl border border-brand-mint/30 bg-brand-mint/15 px-6 py-4 text-lg text-secondary/80">
        {{ $post->excerpt }}
      </p>
    @endif

    <article class="prose prose-lg prose-slate mt-10 max-w-none prose-a:text-primary hover:prose-a:text-secondary dark:prose-invert">
      {!! $post->body !!}
    </article>

    @if ($post->youtube_embed_url)
      <div class="mt-12 overflow-hidden rounded-2xl border border-brand-mint/40 bg-black/80 shadow-lg">
        <div class="relative" style="padding-top: 56.25%;">
          <iframe
            src="{{ $post->youtube_embed_url }}"
            title="YouTube video player"
            allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share"
            allowfullscreen
            class="absolute inset-0 h-full w-full border-0"
            loading="lazy"
          ></iframe>
        </div>
      </div>
    @endif

    @if ($post->gallery_images)
      <div class="mt-12">
        <h2 class="text-xl font-semibold text-secondary">Галерея</h2>
        <div class="mt-4 grid gap-4 sm:grid-cols-2">
          @foreach ($post->gallery_images as $image)
            <figure class="overflow-hidden rounded-2xl border border-brand-mint/40 bg-white/70">
              <img
                src="{{ asset('storage/' . ltrim($image, '/')) }}"
                alt="{{ $post->title }} — фото {{ $loop->iteration }}"
                class="h-56 w-full object-cover"
                loading="lazy"
              >
            </figure>
          @endforeach
        </div>
      </div>
    @endif

    @if ($recentPosts->isNotEmpty())
      <div class="mt-16 border-t border-brand-mint/30 pt-8">
        <h2 class="text-xl font-semibold text-secondary">Інші матеріали</h2>
        <div class="mt-4 space-y-4">
          @foreach ($recentPosts as $recent)
            <a href="{{ route('posts.show', $recent) }}" class="flex items-start justify-between rounded-xl border border-transparent bg-white/70 px-4 py-3 transition hover:border-brand-mint/40 hover:bg-white">
              <div>
                <p class="text-sm uppercase tracking-wide text-secondary/60">{{ optional($recent->published_at)->format('d.m.Y') }}</p>
                <p class="mt-1 text-base font-semibold text-secondary">{{ $recent->title }}</p>
              </div>
              <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="mt-1 h-5 w-5 text-primary">
                <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5" />
              </svg>
            </a>
          @endforeach
        </div>
      </div>
    @endif
  </div>
</section>
@endsection
