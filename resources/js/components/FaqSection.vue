<!-- FaqSection.vue -->
<template>
  <section
    class="section-surface py-16 md:py-24"
    aria-labelledby="faq-heading"
    ref="rootEl"
  >
    <div class="mx-auto max-w-7xl px-6 lg:px-8">
      <header class="mx-auto mb-10 max-w-3xl text-center">
        <!-- за бажанням можна додати бейдж:
        <p class="badge-muted font-display mb-3">Питання та відповіді</p> -->
        <h2 id="faq-heading" class="heading-1 font-display tracking-tight text-secondary">
          {{ computedTitle }}
        </h2>
        <p v-if="computedSubtitle" class="mt-3 text-secondary/85 max-w-[70ch] leading-relaxed font-sans mx-auto">
          {{ computedSubtitle }}
        </p>
      </header>

      <!-- Two independent columns so opening on the left doesn't push the right -->
      <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
        <!-- LEFT column -->
        <div class="space-y-4">
          <div
            v-for="i in leftIndices"
            :key="i"
            class="rounded-2xl border border-teal-200/60 bg-white/70 backdrop-blur-sm shadow-sm hover:shadow-md transition"
          >
            <h3 :id="headingId(i)" class="text-base">
              <button
                type="button"
                class="flex w-full items-start justify-between gap-4 p-4 lg:p-5 text-slate-900 font-semibold text-left rounded-xl focus:outline-none focus-visible:ring-2 focus-visible:ring-[#0FA6A0]/40"
                :aria-expanded="isOpen(i)"
                :aria-controls="panelId(i)"
                :data-index="i"
                @click="toggle(i)"
                @keydown="onHeaderKeydown"
                :ref="el => el && (headerButtons[i] = el as HTMLElement)"
              >
                <span class="pr-8">{{ list[i].q }}</span>
                <svg
                  class="mt-1 h-6 w-6 shrink-0 transition-transform duration-300"
                  :class="isOpen(i) ? 'rotate-180 text-[#0C7C78]' : 'rotate-0 text-[#0FA6A0]'"
                  xmlns="http://www.w3.org/2000/svg"
                  viewBox="0 0 24 24"
                  fill="currentColor"
                  aria-hidden="true"
                >
                  <path v-if="!isOpen(i)" d="M12 5a1 1 0 011 1v5h5a1 1 0 010 2h-5v5a1 1 0 01-2 0v-5H6a1 1 0 010-2h5V6a1 1 0 011-1z" />
                  <path v-else d="M5 12a1 1 0 011-1h12a1 1 0 010 2H6a1 1 0 01-1-1z" />
                </svg>
              </button>
            </h3>

            <div
              :id="panelId(i)"
              class="overflow-hidden transition-[max-height,opacity] duration-300 ease-in-out"
              :style="panelStyle(i)"
              role="region"
              :aria-labelledby="headingId(i)"
              :aria-hidden="!isOpen(i)"
              :inert="!isOpen(i) ? '' : null"
            >
              <div class="prose prose-slate max-w-none p-4 pt-0 lg:p-5 lg:pt-0">
                <div class="max-w-[70ch]" v-html="list[i].a" />
              </div>
            </div>
          </div>
        </div>

        <!-- RIGHT column -->
        <div class="space-y-4">
          <div
            v-for="i in rightIndices"
            :key="i"
            class="rounded-2xl border border-teal-200/60 bg-white/70 backdrop-blur-sm shadow-sm hover:shadow-md transition"
          >
            <h3 :id="headingId(i)" class="text-base">
              <button
                type="button"
                class="flex w-full items-start justify-between gap-4 p-4 lg:p-5 text-slate-900 font-semibold text-left rounded-xl focus:outline-none focus-visible:ring-2 focus-visible:ring-[#0FA6A0]/40"
                :aria-expanded="isOpen(i)"
                :aria-controls="panelId(i)"
                :data-index="i"
                @click="toggle(i)"
                @keydown="onHeaderKeydown"
                :ref="el => el && (headerButtons[i] = el as HTMLElement)"
              >
                <span class="pr-8">{{ list[i].q }}</span>
                <svg
                  class="mt-1 h-6 w-6 shrink-0 transition-transform duration-300"
                  :class="isOpen(i) ? 'rotate-180 text-[#0C7C78]' : 'rotate-0 text-[#0FA6A0]'"
                  xmlns="http://www.w3.org/2000/svg"
                  viewBox="0 0 24 24"
                  fill="currentColor"
                  aria-hidden="true"
                >
                  <path v-if="!isOpen(i)" d="M12 5a1 1 0 011 1v5h5a1 1 0 010 2h-5v5a1 1 0 01-2 0v-5H6a1 1 0 010-2h5V6a1 1 0 011-1z" />
                  <path v-else d="M5 12a1 1 0 011-1h12a1 1 0 010 2H6a1 1 0 01-1-1z" />
                </svg>
              </button>
            </h3>

            <div
              :id="panelId(i)"
              class="overflow-hidden transition-[max-height,opacity] duration-300 ease-in-out"
              :style="panelStyle(i)"
              role="region"
              :aria-labelledby="headingId(i)"
              :aria-hidden="!isOpen(i)"
              :inert="!isOpen(i) ? '' : null"
            >
              <div class="prose prose-slate max-w-none p-4 pt-0 lg:p-5 lg:pt-0">
                <div class="max-w-[70ch]" v-html="list[i].a" />
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- JSON-LD -->
    <script type="application/ld+json">{{ jsonLd }}</script>
  </section>
</template>


<script setup lang="ts">
import { computed, nextTick, onMounted, ref, watch } from 'vue'

type FaqItem = { q: string; a: string }

const props = defineProps<{
  items?: FaqItem[]
  title?: string
  subtitle?: string
}>()

/* === 12 default items (short & practical) === */
const fallback: FaqItem[] = [
  { q: 'Скільки часу потрібно, щоб заговорити?', a: '8–12 тижнів за планом: щоденні міні-сесії + 1–2 розмовні уроки на тиждень.' },
  { q: 'Граматика обовʼязкова?', a: 'Лише ~20% від заняття. Подання — через приклади й одразу в говорінні.' },
  { q: 'Онлайн чи офлайн?', a: 'Формат змішаний: обираєте за графіком. Є запис пропущених занять.' },
  { q: 'З яких рівнів навчаємо?', a: 'Від Starter/A1 до Upper-Intermediate/B2. Є підготовчі групи для початку з нуля.' },
  { q: 'Чи є домашні?', a: 'Так, короткі практики 10–15 хв. щодня + трек мотивації.' },
  { q: 'Матеріали включені?', a: 'Так. Доступ до власних конспектів, карток і відео в LMS.' },
  { q: 'Є speaking-клуби?', a: 'Щотижня: тематичні зустрічі з тьютором, міні-проєкти й дебати.' },
  { q: 'Групи чи індивідуальні?', a: 'Обидва варіанти. У групах 6–8 людей; 1-на-1 — за індивідуальним планом.' },
  { q: 'Оплата і повернення?', a: 'Оплата помісячно; повернення за правилами договору до початку модуля.' },
  { q: 'Пауза/заморозка?', a: 'Можна заморозити абонемент до 30 днів без втрати місця.' },
  { q: 'Готуєте до IELTS/співбесіди?', a: 'Так, окремі треки з тренуванням speaking/writing і мок-інтервʼю.' },
  { q: 'Як стартуємо?', a: 'Безкоштовна діагностика рівня та рекомендації — далі стартуємо з найближчим потоком.' },
]

const list = computed<FaqItem[]>(() => (props.items?.length ? props.items : fallback))
const computedTitle = computed(() => props.title ?? 'FAQ (коротко)')
const computedSubtitle = computed(() => props.subtitle ?? '')

/* === Split into two independent columns === */
const indices = computed(() => list.value.map((_, i) => i))
const leftIndices = computed(() => indices.value.filter(i => i % 2 === 0))
const rightIndices = computed(() => indices.value.filter(i => i % 2 === 1))

/* === Multi-open accordion state === */
const openSet = ref<Set<number>>(new Set())
const isOpen = (i: number) => openSet.value.has(i)
const toggle = (i: number) => {
  isOpen(i) ? openSet.value.delete(i) : openSet.value.add(i)
  nextTick(() => updatePanelHeight(i))
}

/* === IDs and refs === */
const baseId = Math.random().toString(36).slice(2)
const headingId = (i: number) => `faq-h-${baseId}-${i}`
const panelId = (i: number) => `faq-p-${baseId}-${i}`

const headerButtons = ref<Record<number, HTMLElement>>({})
const rootEl = ref<HTMLElement | null>(null)

/* === Animated height (respects prefers-reduced-motion) === */
const panelHeights = ref<Record<number, number>>({})
const reducedMotion = ref(false)

const panelStyle = (i: number) => {
  const open = isOpen(i)
  const h = panelHeights.value[i] ?? 0
  return reducedMotion.value
    ? { maxHeight: open ? 'none' : '0px', opacity: open ? 1 : 0 }
    : { maxHeight: open ? `${h}px` : '0px', opacity: open ? 1 : 0 }
}

function updatePanelHeight(i?: number) {
  const idxs = typeof i === 'number' ? [i] : indices.value
  idxs.forEach(id => {
    const el = document.getElementById(panelId(id))
    const inner = el?.firstElementChild as HTMLElement | null
    panelHeights.value[id] = inner?.scrollHeight ? inner.scrollHeight + 1 : 0
  })
}

/* === Keyboard navigation: ArrowUp/Down, Home/End === */
function onHeaderKeydown(e: KeyboardEvent) {
  const current = e.currentTarget as HTMLElement
  const i = Number(current.dataset.index)
  const order = indices.value
  const pos = order.indexOf(i)
  if (pos < 0) return

  const focusIdx = (targetIndex: number) => {
    const btn = headerButtons.value[targetIndex]
    btn?.focus()
  }

  switch (e.key) {
    case 'ArrowDown':
      e.preventDefault()
      focusIdx(order[Math.min(pos + 1, order.length - 1)])
      break
    case 'ArrowUp':
      e.preventDefault()
      focusIdx(order[Math.max(pos - 1, 0)])
      break
    case 'Home':
      e.preventDefault()
      focusIdx(order[0])
      break
    case 'End':
      e.preventDefault()
      focusIdx(order[order.length - 1])
      break
  }
}

/* === JSON-LD (strip HTML in answers) === */
const jsonLd = ref('')
const stripHtml = (html: string) => {
  const tmp = document.createElement('div')
  tmp.innerHTML = html
  return tmp.textContent || tmp.innerText || ''
}
function buildJsonLd() {
  const payload = {
    '@context': 'https://schema.org',
    '@type': 'FAQPage',
    mainEntity: list.value.map(it => ({
      '@type': 'Question',
      name: it.q,
      acceptedAnswer: {
        '@type': 'Answer',
        text: stripHtml(it.a),
      },
    })),
  }
  jsonLd.value = JSON.stringify(payload)
}

/* === Lifecycle === */
onMounted(() => {
  reducedMotion.value =
    window.matchMedia?.('(prefers-reduced-motion: reduce)')?.matches ?? false
  buildJsonLd()
  updatePanelHeight()
})

watch(list, () => {
  buildJsonLd()
  nextTick(updatePanelHeight)
})
</script>

<style scoped>
/* Minor perf hint for smoother expand/collapse */
[role="region"] { will-change: max-height, opacity; }
/* Optional: brand accent for links inside answers (Typography plugin keeps it tidy) */
.prose a { color: #0FA6A0; text-decoration: underline transparent; transition: text-decoration-color .2s ease; }
.prose a:hover { text-decoration-color: currentColor; }
/* Respect reduced motion */
@media (prefers-reduced-motion: reduce) {
  .transition-\[max-height,opacity\] { transition: none !important; }
}
/* === Глобалізуємо стилі прайсу, щоб працювали поза FaqSection.vue === */
:global(.price-card:hover .sticker-bottom) { transform: translateY(-2px); }
:global(.price-card:hover .sticker-bottom::before) { opacity: 1; filter: blur(6px); }

/* БАЗА для декоративного «стікера» — інакше ::before не видно */
:global(.sticker-bottom) {
  position: relative;            /* потрібно для абсолютного ::before */
}
:global(.sticker-bottom::before) {
  content: "";                    /* створюємо псевдоелемент */
  position: absolute;
  left: 0; right: 0; bottom: -2px;
  height: 10px;                   /* підсвітка/післясвітіння під стикером */
  border-radius: 9999px;
  background: radial-gradient(closest-side, rgba(0,0,0,.12), rgba(0,0,0,0));
  opacity: 0;                     /* активується на hover/active */
  filter: blur(0);
  transition: opacity .3s ease, filter .3s ease;
  pointer-events: none;
}

/* Хвильові плями — обовʼязково мати контекст позиціювання */
:global(.wave) { position: relative; }
:global(.wave::before),
:global(.wave::after) {
  content:"";
  position:absolute;
  pointer-events:none;
  border-radius:50%;
  opacity:0;
  transform:scale(.6);
  transition: opacity .38s ease, transform .7s cubic-bezier(.2,.6,.2,1);
}
:global(.wave::before){
  width:240px; height:240px; right:-48px; top:-48px;
  background: radial-gradient(closest-side, rgba(255,255,255,.55), transparent 70%);
  filter: blur(6px);
}
:global(.wave::after){
  width:280px; height:280px; left:-58px; bottom:-58px;
  background: radial-gradient(closest-side, rgba(var(--hl), .35), transparent 70%);
  filter: blur(8px);
}
:global(.wave:hover::before),
:global(.wave:hover::after) { opacity:1; transform:scale(1); }

/* --- Постійно активна (лише для другої картки) --- */
:global(.price-card--active .sticker-bottom) { transform: translateY(-2px); }
:global(.price-card--active .sticker-bottom::before) { opacity: 1; filter: blur(6px); }
:global(.wave--active::before),
:global(.wave--active::after) { opacity: 1; transform: scale(1); }

/* Обережно з will-change — тільки там, де є анімації */
:global(.sticker-bottom) { will-change: transform, opacity; }
:global(.wave::before), :global(.wave::after) { will-change: transform, opacity; }

/* === Accessibility: менше руху для тих, хто просить === */
@media (prefers-reduced-motion: reduce) {
  .transition-\[max-height,opacity\] { transition: none !important; }
  :global(.price-card *),
  :global(.wave::before), :global(.wave::after) { transition: none !important; }
}
</style>
