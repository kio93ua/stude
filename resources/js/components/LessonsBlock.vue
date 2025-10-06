<!-- resources/js/components/LessonsBlock.vue -->
<template>
  <section
    id="lessons"
    aria-labelledby="lessons-heading"
    class="relative section-surface"
  >
    <div class="mx-auto max-w-7xl px-6 py-16 sm:py-24">
      <!-- Хедер -->
      <header class="mx-auto max-w-3xl text-center space-y-4">
        <p class="badge-muted font-display inline-block">{{ introLocal.badge }}</p>

        <h2 id="lessons-heading" class="heading-1 font-display tracking-tight text-secondary">
          {{ introLocal.title }}
        </h2>

        <p class="text-secondary/85 text-base sm:text-lg font-sans">
          {{ introLocal.subtitle }}
        </p>
      </header>

      <!-- Сітка: текст ліворуч, 1 відео праворуч -->
      <div class="mt-12 grid gap-8 md:grid-cols-[1.1fr_1.7fr]">
        <!-- Ліва колонка -->
        <div class="space-y-6">
          <section class="rounded-2xl bg-white/90 ring-1 ring-slate-200 p-5 sm:p-6 shadow-sm">
            <h3 class="text-lg sm:text-xl font-semibold text-slate-900">Як проходять заняття</h3>
            <p class="mt-3 text-slate-700 text-base sm:text-lg">
              На уроках поєднуємо розмовну практику, граматику та роботу з лексикою.
              Кожне заняття має чітку мету та відчутний прогрес.
            </p>
          </section>

          <section class="rounded-2xl bg-white/90 ring-1 ring-slate-200 p-5 sm:p-6 shadow-sm">
            <h3 class="text-lg sm:text-xl font-semibold text-slate-900">Що ви отримуєте</h3>
            <ul class="mt-3 space-y-3">
              <li class="flex items-start gap-3">
                <span class="mt-2 inline-block h-2.5 w-2.5 flex-none rounded-full bg-teal-500"></span>
                <p class="text-slate-700">Живі приклади та рольові діалоги для впевненого спілкування.</p>
              </li>
              <li class="flex items-start gap-3">
                <span class="mt-2 inline-block h-2.5 w-2.5 flex-none rounded-full bg-teal-500"></span>
                <p class="text-slate-700">Персональні фідбеки, щоб швидше позбутися типових помилок.</p>
              </li>
              <li class="flex items-start gap-3">
                <span class="mt-2 inline-block h-2.5 w-2.5 flex-none rounded-full bg-teal-500"></span>
                <p class="text-slate-700">Баланс говоріння, слухання та письма — без перевантаження.</p>
              </li>
            </ul>
          </section>
        </div>

        <!-- Права колонка: велика відео-картка -->
        <div class="w-full mx-auto max-w-3xl sm:max-w-[900px]">
          <article
            v-if="rightVideo"
            class="rounded-2xl bg-white/90 ring-1 ring-slate-200 overflow-hidden shadow-sm transition hover:shadow-lg hover:ring-teal-300"
          >
            <div class="p-5 sm:p-6">
              <h3 class="mb-3 text-base sm:text-lg font-semibold text-slate-900">
                {{ rightVideo.title }}
              </h3>

              <!-- 16:9 / мін. висота 400px -->
              <div
                class="w-full aspect-video min-h-[400px] rounded-xl overflow-hidden bg-black"
                :data-yt-id="rightVideo.id"
                :ref="setObserveTarget"
              >
                <!-- Постер до кліку -->
                <button
                  v-if="!played[rightVideo.id]"
                  class="relative block h-full w-full"
                  type="button"
                  :aria-label="`Відтворити: ${rightVideo.title}`"
                  @click="played[rightVideo.id] = true"
                >
                  <img
                    class="h-full w-full object-cover"
                    :src="thumb(rightVideo.id)"
                    alt=""
                    loading="eager"
                    decoding="async"
                    fetchpriority="high"
                  />
                  <span class="absolute inset-0 grid place-items-center">
                    <span class="rounded-full bg-white/90 p-4 shadow text-slate-900">▶</span>
                  </span>
                </button>

                <!-- Iframe після взаємодії -->
                <iframe
                  v-else
                  class="h-full w-full"
                  :src="nocookieSrc(rightVideo.id)"
                  loading="eager"
                  :title="`Відеоурок: ${rightVideo.title}`"
                  allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share"
                  allowfullscreen
                  referrerpolicy="strict-origin-when-cross-origin"
                ></iframe>
              </div>

              <p v-if="rightVideo.description" class="mt-3 text-sm text-slate-600">
                {{ rightVideo.description }}
              </p>
            </div>
          </article>

          <div class="pt-3">
            <a
              v-if="ctaLocal.href"
              :href="ctaLocal.href"
              class="inline-flex items-center justify-center rounded-2xl border border-teal-200 bg-white/80 px-4 py-2 text-sm font-semibold text-teal-800 shadow-sm transition hover:-translate-y-0.5 hover:shadow-md hover:ring-1 hover:ring-teal-300 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-teal-600"
            >
              {{ ctaLocal.text }}
            </a>
          </div>
        </div>
      </div>
    </div>
  </section>
</template>


<script setup>
import { computed, onMounted, ref } from 'vue'

/* ---------- Props ---------- */
const props = defineProps({
  intro: {
    type: Object,
    default: () => ({
      badge: 'Програми навчання',
      title: 'Наші уроки',
      subtitle: 'Баланс розмовної практики, граматики та лексики. Кожне заняття — ще один крок до вільної англійської.'
    })
  },
  cta: {
    type: Object,
    default: () => ({ text: 'Усі уроки', href: '#lessons' })
  },
  // беремо лише перше з масиву для правої колонки (решта ігнорується)
  videos: {
    type: Array,
    default: () => ([
      { id: 'ysz5S6PUM-U', title: 'Розмовна практика: small talk', description: 'Фрази для щоденного спілкування.' },
      { id: 'ScMzIvxBSi4', title: 'Граматика без болю: часи', description: 'Як швидко згадати й застосувати часи.' },
      { id: 'dQw4w9WgXcQ', title: 'Intro: як ми вчимося', description: 'Короткий огляд підходу та структури уроків.' }
    ])
  },
  /** Увімкнути авто-вставку iframe при вході у в’юпорт (true/false) */
  autoplayOnView: { type: Boolean, default: false }
})

const introLocal = computed(() => props.intro)
const ctaLocal = computed(() => {
  const text = typeof props.cta?.text === 'string' && props.cta.text.trim() !== '' ? props.cta.text.trim() : 'Усі уроки'
  const href = typeof props.cta?.href === 'string' && props.cta.href.trim() !== '' ? props.cta.href.trim() : null
  return { text, href }
})
const videosLocal = computed(() => (Array.isArray(props.videos) ? props.videos : []))
const rightVideo  = computed(() => videosLocal.value[0] || null)

/* ---------- Lightweight embed helpers ---------- */
const played = ref({}) // { [id]: true } після кліку

function nocookieSrc(id) {
  // Privacy-Enhanced Mode (менше трекінгу; все одно потребує allowlist/CSP)
  return `https://www.youtube-nocookie.com/embed/${id}?rel=0&modestbranding=1&playsinline=1`
}
function thumb(id) {
  return `https://i.ytimg.com/vi/${id}/hqdefault.jpg`
}

/* ---------- Preconnect + IntersectionObserver ---------- */
function preconnectOnce() {
  const list = [
    { href: 'https://www.youtube-nocookie.com', rel: 'preconnect' },
    { href: 'https://i.ytimg.com', rel: 'preconnect' }
  ]
  for (const { href, rel } of list) {
    if (document.head.querySelector(`link[rel="${rel}"][href="${href}"]`)) continue
    const l = document.createElement('link')
    l.rel = rel
    l.href = href
    l.crossOrigin = ''
    document.head.appendChild(l)
  }
}

const observeTargets = ref([])
/** збираємо елементи через функцію-реф (Vue 3) */
function setObserveTarget(el) {
  if (el) observeTargets.value.push(el)
}

onMounted(() => {
  preconnectOnce()

  // Лінивий «активатор»: створюємо iframe тільки після кліку
  // або при вході в зону видимості, якщо autoplayOnView=true
  const io = new IntersectionObserver((entries) => {
    for (const e of entries) {
      const id = e.target?.dataset?.ytId
      if (!id) continue
      if (e.isIntersecting) {
        if (props.autoplayOnView) played.value[id] = true
        io.unobserve(e.target)
      }
    }
  }, { root: null, rootMargin: '200px 0px', threshold: 0.01 })

  observeTargets.value.forEach(el => el && io.observe(el))
})
</script>

<style scoped>
/* Виправлено попередження: fullscreen тепер тільки через allowfullscreen (без дубля в allow) */
iframe { border: 0; display: block; min-height: 400px; }
</style>
