@extends('layouts.app')

@section('content')
<section class="py-16">
  <div class="mx-auto max-w-5xl px-6">
    <h1 class="text-3xl font-bold text-primary sm:text-4xl">Блог Study School</h1>
    <p class="mt-2 text-secondary/80">
      Читайте новини школи, поради з навчання та історії успіху наших студентів.
    </p>

    <div class="mt-10 grid gap-8 md:grid-cols-2">
      @forelse ($posts as $post)
        <article class="flex h-full flex-col overflow-hidden rounded-2xl border border-brand-mint/40 bg-white/90 shadow-sm">
          @if ($post->cover_image_path)
            <a href="{{ route('posts.show', $post) }}" class="block overflow-hidden">
              <img
                src="{{ asset('storage/' . ltrim($post->cover_image_path, '/')) }}"
                alt="{{ $post->title }}"
                class="h-52 w-full object-cover transition duration-300 hover:scale-[1.02]"
                loading="lazy"
              >
            </a>
          @endif

          <div class="flex flex-1 flex-col p-6">
            <div class="flex items-center gap-3 text-xs font-semibold uppercase tracking-wide text-secondary/60">
              <span>{{ optional($post->published_at)->format('d.m.Y') }}</span>
              <span class="h-1 w-1 rounded-full bg-secondary/40"></span>
              <span>Блог</span>
            </div>

            <h2 class="mt-3 text-2xl font-semibold text-secondary">
              <a href="{{ route('posts.show', $post) }}" class="transition hover:text-primary">
                {{ $post->title }}
              </a>
            </h2>

            <p class="mt-3 text-secondary/80">
              {{ $post->excerpt ?: \Illuminate\Support\Str::limit(strip_tags($post->body), 160) }}
            </p>

            <div class="mt-6 flex items-center justify-between border-t border-brand-mint/30 pt-4">
              <a href="{{ route('posts.show', $post) }}"
                 class="inline-flex items-center gap-2 text-sm font-semibold text-primary transition hover:text-secondary">
                Читати далі
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="h-4 w-4">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5" />
                </svg>
              </a>

              @if ($post->youtube_embed_url)
                <span class="rounded-full bg-brand-mint/20 px-3 py-1 text-xs font-semibold text-primary">Відео</span>
              @endif
            </div>
          </div>
        </article>
      @empty
        <div class="rounded-2xl border border-dashed border-brand-mint/40 bg-white/70 px-6 py-12 text-center text-secondary/70 md:col-span-2">
          Поки що тут порожньо. Статті з'являться зовсім скоро!
        </div>
      @endforelse
    </div>

    @if ($posts instanceof \Illuminate\Pagination\Paginator || $posts instanceof \Illuminate\Pagination\LengthAwarePaginator)
      <div class="mt-10">
        {{ $posts->links() }}
      </div>
    @endif
  </div>
</section>
@endsection
