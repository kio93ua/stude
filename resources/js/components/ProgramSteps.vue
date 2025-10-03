<!-- resources/js/components/ProgramStepsV.vue -->
<template>
  <section
    id="program-steps-v"
    ref="sectionEl"
    aria-labelledby="stepsv-heading"
    class="relative overflow-visible
           /* ⬇ Градієнт без “провалу”: змінюй відсотки нижче */
           bg-[linear-gradient(to_bottom,rgba(12,124,120,0.10)_10%,rgba(191,243,226,0.40)_50%,rgba(12,124,120,0.10)_90%)]"
  >
    <!-- М’які плями з легким parallax (aria-hidden) -->
    <div aria-hidden class="js-blob pointer-events-none absolute -top-24 -left-24 h-72 w-72 rounded-full blur-3xl opacity-30"
         :style="blobStyleA"></div>
    <div aria-hidden class="js-blob pointer-events-none absolute top-1/3 left-1/2 h-64 w-64 -translate-x-1/2 rounded-full blur-3xl opacity-25"
         :style="blobStyleB"></div>
    <div aria-hidden class="js-blob pointer-events-none absolute -bottom-24 -right-20 h-80 w-80 rounded-full blur-3xl opacity-20"
         :style="blobStyleC"></div>

    <div class="mx-auto max-w-7xl px-6 py-16 md:py-24">
      <!-- Хедер -->
      <header class="max-w-3xl">
        <span ref="badgeEl"
              class="inline-block rounded-full bg-teal-100/80 px-4 py-1 text-sm font-semibold text-teal-700 ring-1 ring-teal-200">
          Програма навчання
        </span>
        <h2 id="stepsv-heading" ref="titleEl"
            class="mt-4 text-3xl md:text-4xl font-bold tracking-tight text-slate-900">
          4 етапи до впевненого говоріння
        </h2>
        <p ref="subtitleEl" class="mt-3 text-base md:text-lg text-slate-700">
          Кожен крок — чіткий фокус і практичні завдання. Анімації з’являються, коли ви дійшли до блоку.
        </p>
      </header>

      <!-- Прогрес-бар секції -->
      <div class="sticky mt-6 top-14 md:top-16 z-10 -mb-2 h-1 rounded-full bg-slate-200/70">
        <div ref="progressEl"
             class="h-1 w-0 rounded-full bg-gradient-to-r from-[#0C7C78] to-[#0FA6A0]"
             role="progressbar"
             aria-label="Прогрес перегляду програми"
             :aria-valuemin="0"
             :aria-valuemax="100"
             :aria-valuenow="progressNow"
             tabindex="0" />
      </div>

      <!-- Сітка етапів (керуємо класом для FLIP при перестановці) -->
      <div ref="gridEl"
           :class="['mt-10 grid gap-6 md:gap-8', isDesktop ? 'grid-cols-2' : 'grid-cols-1']">
        <article v-for="(s, i) in steps" :key="s.id"
                 :ref="el => cardRefs.set(s.id, el)"
                 class="step-card rounded-2xl ring-1 ring-slate-200 bg-white/80 backdrop-blur
                        shadow-[0_8px_30px_rgba(0,0,0,0.06)] p-5 md:p-6 focus-within:ring-teal-300 transition"
        >
          <div class="flex items-start gap-4">
            <img v-if="s.icon" :src="s.icon" :alt="`${s.title} іконка`" class="mt-1 h-9 w-9 shrink-0" @load="onMediaLoaded" />
            <div class="flex-1">
              <h3 class="text-lg md:text-xl font-semibold tracking-tight text-slate-900">
                Step {{ i + 1 }} — {{ s.title }}
              </h3>
              <p class="mt-1 text-sm md:text-base text-slate-600">{{ s.summary }}</p>
            </div>
          </div>

          <ul class="mt-4 space-y-2.5">
            <li v-for="(d, k) in s.details" :key="k" class="detail-item flex items-start gap-3">
              <span class="mt-1 inline-block h-2.5 w-2.5 rounded-full bg-[#0FA6A0]" aria-hidden></span>
              <p class="text-slate-700 text-[15px] md:text-[16px] leading-6">{{ d }}</p>
            </li>
          </ul>
        </article>
      </div>

      <div v-if="debug" class="mt-6 text-xs text-slate-500">Debug markers: <strong>on</strong></div>
    </div>
  </section>
</template>

<script setup>
import { onMounted, onUnmounted, reactive, ref, nextTick } from 'vue'
import { gsap } from 'gsap'
import { ScrollTrigger } from 'gsap/ScrollTrigger'
import { Flip } from 'gsap/Flip'
gsap.registerPlugin(ScrollTrigger, Flip)

/* ---------- Props + демо (4 етапи) ---------- */
const props = defineProps({
  steps: {
    type: Array,
    default: () => ([
      {
        id: 's1',
        title: 'Діагностика та цілі',
        summary: 'Вхідна розмова, міні-тест, персональна траєкторія.',
        details: ['Оцінка рівня й бар’єрів', 'Цілі: іспит / робота / подорожі', 'Карта модулів', 'Ресурси під рівень'],
        icon: '/images/steps/diagnostics.svg',
      },
      {
        id: 's2',
        title: 'Фундамент + вимова',
        summary: 'Закриваємо прогалини, ставимо звучання.',
        details: ['Патерни у діалогах', 'Shadowing і ритміка', 'Лексика за темами', 'Домашні з фідбеком'],
        icon: '/images/steps/pronunciation.svg',
      },
      {
        id: 's3',
        title: 'Практика та проєкти',
        summary: 'Сценарії з життя, міні-проєкт і презентація.',
        details: ['Рольові ситуації', 'Speaking club щотижня', 'Міні-проєкт у парі/групі', 'Soft skills англійською'],
        icon: '/images/steps/practice.svg',
      },
      {
        id: 's4',
        title: 'Стратегії та фінал',
        summary: 'Екзаменаційні техніки й план підтримки.',
        details: ['Техніки для усіх модулів', 'Мок-тест + розбір', '30-денний план після курсу'],
        icon: '/images/steps/finish.svg',
      },
    ]),
  },
  /** Вимкнути/увімкнути markers для дебагу ScrollTrigger */
  debug: { type: Boolean, default: false },
})

/* ---------- Refs ---------- */
const sectionEl  = ref(null)
const gridEl     = ref(null)
const progressEl = ref(null)
const badgeEl    = ref(null)
const titleEl    = ref(null)
const subtitleEl = ref(null)
const cardRefs   = new Map()

/* ---------- Стан ---------- */
const progressNow = ref(0)     // для aria-valuenow
const isDesktop   = ref(false) // керуємо класом сітки (FLIP)

/* ---------- М’які плями ---------- */
const blobStyleA = reactive({ background: 'linear-gradient(135deg, #0C7C78, #0FA6A0)' })
const blobStyleB = reactive({ background: 'linear-gradient(135deg, #0FA6A0, #0C7C78)' })
const blobStyleC = reactive({ background: 'linear-gradient(135deg, #0C7C78, #0FA6A0)' })

/* ---------- GSAP infra ---------- */
let mm
let triggers = []
const onMediaLoaded = () => ScrollTrigger.refresh()

onMounted(() => {
  const MARKERS = props.debug
  mm = gsap.matchMedia() // responsive та auto-revert :contentReference[oaicite:1]{index=1}

  /* ===== Вхід секції ===== */
  const intro = (reduced = false) => {
    const base = [badgeEl.value, titleEl.value, subtitleEl.value]
    if (reduced) {
      gsap.from(base, { opacity: 0, duration: 0.35, stagger: 0.08 })
    } else {
      gsap.from(base, {
        opacity: 0, y: 16, duration: 0.6, ease: 'power2.out', stagger: 0.12
      })
    }
  }

  /* ===== Паралакс плям ===== */
  const parallaxBlobs = () => {
    const blobs = sectionEl.value?.querySelectorAll('.js-blob') || []
    blobs.forEach((el, i) => {
      gsap.to(el, {
        yPercent: [-12, 8, -10][i] || 0,
        ease: 'none',
        scrollTrigger: {
          trigger: sectionEl.value,
          /* ⬇ Можна підкрутити: пороги скролу паралакса */
          start: 'top bottom',
          end: 'bottom top',
          scrub: true,
          markers: MARKERS,
        },
      })
    })
  }

  /* ===== Прогрес секції (акуратний) =====
     ⬇ Пороги: змінюй start/end тут */
  const sectionProgress = () => {
    const t = ScrollTrigger.create({
      trigger: sectionEl.value,
      start: 'top bottom',   // ⬅ початок заповнення
      end:   'bottom top',   // ⬅ кінець заповнення
      scrub: true,
      onUpdate: (self) => {
        const p = Math.round(self.progress * 100)
        progressNow.value = p
        if (progressEl.value) progressEl.value.style.width = p + '%'
      },
      markers: MARKERS,
    })
    triggers.push(t)
  }

  /* ===== Вхід карток =====
     Десктоп: clip-path + y/opacity; Мобільний: лише fade/translate;
     prefers-reduced-motion: тільки легкі fades. */
  const animateCards = (opts) => {
    props.steps.forEach((s) => {
      const card = cardRefs.get(s.id)
      if (!card) return
      const detailItems = card.querySelectorAll('.detail-item')

      if (opts.reduced || opts.mobile) {
        // Спрощені ефекти
        gsap.from(card, {
          opacity: 0, y: (opts.reduced ? 0 : 12), duration: 0.5, ease: 'power2.out',
          scrollTrigger: {
            trigger: card,
            start: opts.mobile ? 'top 85%' : 'top 82%', // ⬅ поріг для моб./легких ефектів
            once: !!opts.mobile,                        // ⬅ моб: анімуємо один раз
            toggleActions: opts.mobile ? 'play none none none' : 'play none none reverse',
            markers: MARKERS,
          },
        })
      } else {
        // Повні ефекти (десктоп)
        gsap.fromTo(card,
          { clipPath: 'inset(0 0 100% 0)', opacity: 0.6, y: 16 },
          {
            clipPath: 'inset(0 0 0% 0)', opacity: 1, y: 0,
            duration: 0.8, ease: 'power3.out',
            scrollTrigger: {
              trigger: card,
              start: 'top 72%',     // ⬅ змінюй поріг старту
              end:   'bottom 62%',  // ⬅ і кінець; reverse для виходу
              toggleActions: 'play none none reverse',
              markers: MARKERS,
            },
          }
        )
      }

      // Stagger для пунктів
      gsap.from(detailItems, {
        opacity: 0, y: (opts.reduced ? 0 : 8),
        duration: 0.35,
        ease: 'power2.out',
        stagger: 0.06, // ⬅ змінюй stagger тут
        scrollTrigger: {
          trigger: card,
          start: opts.mobile ? 'top 86%' : 'top 80%',
          once: !!opts.mobile,
          toggleActions: opts.mobile ? 'play none none none' : 'play none none reverse',
          markers: MARKERS,
        },
      })
    })
  }

  /* ===== FLIP при переході grid-cols-1 ↔ grid-cols-2 ===== */
  const flipGrid = async (desktop) => {
    const nodes = Array.from(gridEl.value?.children || [])
    if (!nodes.length) return
    const state = Flip.getState(nodes) // фіксуємо до зміни
    isDesktop.value = !!desktop        // змінюємо клас сітки
    await nextTick()
    Flip.from(state, {
      duration: 0.45, ease: 'power2.out', absolute: true, stagger: 0.04,
    }) // рекомендації щодо FLIP для grid/flex: absolute:true :contentReference[oaicite:2]{index=2}
    ScrollTrigger.refresh() // важливо після FLIP/перекомпонування :contentReference[oaicite:3]{index=3}
  }

  /* ===== matchMedia: десктоп/мобайл + reduced motion ===== */
  mm.add({
    desktop: '(min-width: 768px)',
    mobile:  '(max-width: 767.98px)',
    reduce:  '(prefers-reduced-motion: reduce)',
  }, (ctx) => {
    const { desktop, mobile, reduce } = ctx.conditions

    intro(Boolean(reduce))
    if (!reduce) parallaxBlobs()
    sectionProgress()

    flipGrid(desktop)        // перелаштування сітки з FLIP
    animateCards({ mobile, reduced: reduce })

    // Після ініціалізації всіх тригерів:
    ScrollTrigger.refresh()
  })
})

onUnmounted(() => {
  if (mm) mm.revert() // прибирає всі тригери анімацій, створені в matchMedia
  triggers.forEach(t => t?.kill?.())
  triggers = []
})
</script>
