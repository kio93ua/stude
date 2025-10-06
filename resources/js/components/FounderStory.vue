<!-- resources/js/components/FounderStory.vue -->
<template>
  <section
    id="founder-story"
    ref="sectionEl"
    class="section-surface relative overflow-hidden py-16 md:py-24"
    aria-labelledby="founder-heading"
  >
    <!-- ДЕКОР: один внутрішній шар з radial-gradient (edge-safe + feather mask) -->
    <div aria-hidden class="fs-decor"></div>

    <div class="mx-auto max-w-7xl px-6">
      <!-- Хедер -->
      <header class="max-w-3xl">
        <p ref="badgeEl" class="badge-muted font-display inline-block">Історія засновника</p>
        <h2 id="founder-heading" ref="titleEl" class="mt-4 heading-1 font-display tracking-tight text-secondary">
          {{ founder.name }} — {{ founder.role }}
        </h2>
        <p ref="subtitleEl" class="mt-3 text-secondary/85 max-w-2xl">
          Від першого учня до власної школи: шлях, помилки і відкриття, які сформували наш підхід до навчання.
        </p>
      </header>

      <!-- Дві колонки (контент без змін) -->
      <div class="mt-10 grid md:grid-cols-2 gap-10 items-start">
        <aside ref="stickyEl">
          <article class="overflow-hidden rounded-2xl ring-1 ring-slate-200 bg-white/80 backdrop-blur shadow-[0_8px_30px_rgba(0,0,0,0.06)]">
            <figure class="relative">
              <img
                :src="founder.photo?.src"
                :alt="founder.photo?.alt || `Фото: ${founder.name}`"
                class="w-full object-cover aspect-[4/5] h-[56vh] md:h-[min(70vh,720px)]"
                ref="photoEl"
                @load="onMediaLoaded"
              />
            </figure>
            <div class="p-5">
              <h3 class="text-lg font-semibold text-slate-900">{{ founder.name }}</h3>
              <p class="text-slate-600">{{ founder.role }}</p>

              <div class="mt-4 flex items-center gap-3">
                <a v-if="founder.socials?.linkedin" :href="founder.socials.linkedin" target="_blank" rel="noopener"
                   class="inline-flex h-9 w-9 items-center justify-center rounded-full ring-1 ring-slate-200 text-slate-500 hover:text-teal-700 hover:ring-teal-300 transition"
                   aria-label="LinkedIn">
                  <!-- svg -->
                </a>
                <a v-if="founder.socials?.instagram" :href="founder.socials.instagram" target="_blank" rel="noopener"
                   class="inline-flex h-9 w-9 items-center justify-center rounded-full ring-1 ring-slate-200 text-slate-500 hover:text-teal-700 hover:ring-teal-300 transition"
                   aria-label="Instagram">
                  <!-- svg -->
                </a>
                <a v-if="founder.socials?.site" :href="founder.socials.site" target="_blank" rel="noopener"
                   class="inline-flex h-9 px-4 items-center justify-center rounded-full ring-1 ring-slate-200 text-slate-600 hover:text-teal-700 hover:ring-teal-300 transition text-sm">
                  Сайт
                </a>
              </div>
            </div>
          </article>
        </aside>

        <div class="relative" ref="contentEl">
          <div class="sticky top-0 z-10 -mt-2 h-1 bg-slate-200/70 rounded-full">
            <div
              ref="progressEl"
              class="h-1 w-0 rounded-full bg-gradient-to-r from-[#0C7C78] to-[#0FA6A0]"
              role="progressbar"
              aria-label="Прогрес читання історії засновника"
              :aria-valuemin="0" :aria-valuemax="100" :aria-valuenow="progressNow"
              tabindex="0"
            />
          </div>

          <section v-for="(sec, idx) in content" :key="idx" class="mt-8">
            <h3 class="text-xl md:text-2xl font-semibold text-slate-900">{{ sec.heading }}</h3>
            <div class="mt-3 space-y-4 text-slate-700 text-base md:text-[18px] leading-7 md:leading-8">
              <p v-for="(p, i) in sec.body" :key="i">{{ p }}</p>
            </div>

            <blockquote v-if="sec.quote" class="mt-6 rounded-2xl bg-white/80 backdrop-blur ring-1 ring-slate-200 p-5">
              <p class="text-slate-800 italic">“{{ sec.quote.text }}”</p>
              <footer class="mt-2 text-sm text-slate-500">— {{ sec.quote.author }}</footer>
            </blockquote>

            <div v-if="sec.milestones?.length" class="mt-6 pl-4">
              <div class="relative">
                <div class="js-timeline-line absolute left-3 top-0 bottom-0 w-px bg-gradient-to-b from-[#0FA6A0]/30 to-transparent"></div>
                <ul class="space-y-4">
                  <li v-for="(m, k) in sec.milestones" :key="k" class="relative group">
                    <div class="js-m-dot absolute -left-[6px] top-1.5 h-3 w-3 rounded-full bg-[#0FA6A0] shadow-[0_0_0_4px_rgba(15,166,160,0.15)] group-hover:shadow-[0_0_0_6px_rgba(15,166,160,0.25)] transition"></div>
                    <div class="js-m-card rounded-2xl ring-1 ring-slate-200 bg-white/80 backdrop-blur p-4 transition-shadow">
                      <div class="text-sm font-semibold text-teal-700">{{ m.year }}</div>
                      <div class="text-slate-700 mt-1">{{ m.text }}</div>
                    </div>
                  </li>
                </ul>
              </div>
            </div>
          </section>
        </div>
      </div>
    </div>
  </section>
</template>


<script setup>
import { onMounted, onUnmounted, reactive, ref } from 'vue'
import { gsap } from 'gsap'
import { ScrollTrigger } from 'gsap/ScrollTrigger'
gsap.registerPlugin(ScrollTrigger)

// ===== Props + демо-дані =====
const props = defineProps({
  founder: {
    type: Object,
    default: () => ({
      name: 'Олена Коваль',
      role: 'Засновниця школи англійської',
      photo: { src: '/images/founder.png', alt: 'Портрет засновниці' },
      socials: { linkedin: '#', instagram: '#', site: '#' },
    }),
  },
  content: {
    type: Array,
    default: () => ([
      {
        heading: 'Початок шляху',
        body: [
          'Усе почалося з індивідуальних занять удома: один стіл, ноутбук та безмежне бажання допомогти студентам говорити впевнено.',
          'Поступово сформувалася методика, що поєднує комунікативний підхід та завдання з реальних ситуацій.',
        ],
        milestones: [
          { year: '2015', text: 'Перші 10 учнів і відгуки, що надихнули рухатися далі.' },
          { year: '2016', text: 'Перші групи вихідного дня: розмовні клуби та міні-проєкти.' },
        ],
      },
      {
        heading: 'Розвиток і помилки',
        body: [
          'Зростання — це експерименти. Частину форматів ми відкинули, залишивши тільки ті, що реально працюють.',
          'Фокус — на практиці й результаті: тестові розмови, мікроцілі та підсумки після кожного модуля.',
        ],
        quote: { text: 'Мова — це інструмент. Коли ним користуєшся щодня, він залишається гострим.', author: 'Олена' },
      },
      {
        heading: 'Сьогодні',
        body: [
          'Ми зібрали найкращий досвід у структуровані програми та продовжуємо оновлювати матеріали щосеместру.',
          'Мета — дати студентам інструменти і впевненість, щоб англійська працювала на них у реальному житті.',
        ],
      },
    ]),
  },
  /** Увімкнути debug-маркери ScrollTrigger */
  debug: { type: Boolean, default: false },
})

// ===== Refs (експортуємо згідно вимог) =====
const sectionEl  = ref(null)
const stickyEl   = ref(null)
const contentEl  = ref(null)
const progressEl = ref(null)
const photoEl    = ref(null)

// для анімацій хедера
const badgeEl    = ref(null)
const titleEl    = ref(null)
const subtitleEl = ref(null)

// Прогрес читання (0..100)
const progressNow = ref(0)

// Фон-плями
const blobStyleA = reactive({ background: 'linear-gradient(135deg, #0C7C78, #0FA6A0)' })
const blobStyleB = reactive({ background: 'linear-gradient(135deg, #0FA6A0, #0C7C78)' })
const blobStyleC = reactive({ background: 'linear-gradient(135deg, #0C7C78, #0FA6A0)' })

let mm
let triggers = []

const onMediaLoaded = () => ScrollTrigger.refresh()

onMounted(() => {
  const MARKERS = props.debug // утиліта для дебагу

  mm = gsap.matchMedia() // Responsive setup. :contentReference[oaicite:1]{index=1}

  // --- Паралакс плям ---
  const parallax = () => {
    const blobs = sectionEl.value?.querySelectorAll('.js-blob') || []
    blobs.forEach((el, i) => {
      gsap.to(el, {
        yPercent: [-10, 8, -6][i] || 0,
        ease: 'none',
        scrollTrigger: {
          trigger: sectionEl.value,
          start: 'top bottom',
          end: 'bottom top',
          scrub: true,
          markers: MARKERS,
        },
      })
    })
  }

  // --- Вступні анімації ---
  const intro = () => {
    gsap.from([badgeEl.value, titleEl.value, subtitleEl.value], {
      opacity: 0, y: 16, duration: 0.6, stagger: 0.12, ease: 'power2.out'
    })
    gsap.fromTo(photoEl.value,
      { clipPath: 'inset(0 0 100% 0)', opacity: 0.6 },
      { clipPath: 'inset(0 0 0% 0)',   opacity: 1, duration: 0.9, ease: 'power3.out', delay: 0.1 }
    )
  }

  // --- Прогрес-бар читання правої колонки ---
  const readingProgress = () => {
    const t = ScrollTrigger.create({
      trigger: contentEl.value,
      start: 'top top',
      end: 'bottom bottom',
      onUpdate: self => {
        const p = Math.round(self.progress * 100)
        progressNow.value = p
        if (progressEl.value) progressEl.value.style.width = p + '%'
      },
      scrub: true,
      markers: MARKERS,
    })
    triggers.push(t)
  }

  // --- Пін лівої картки через GSAP (без sticky CSS) ---
  const pinLeft = () => {
    const headerOffset = 88 // змінюй під висоту шапки
    const t = ScrollTrigger.create({
      trigger: contentEl.value,
      pin: stickyEl.value,
      start: `top top+=${headerOffset}`,
      end: () => "+=" + Math.max(0, contentEl.value.scrollHeight - stickyEl.value.offsetHeight),
      pinSpacing: true,
      anticipatePin: 1,
      invalidateOnRefresh: true,
      pinReparent: true, // іноді допомагає при складних лейаутах
      markers: MARKERS,
    })
    triggers.push(t)
  }

  // --- Таймлайн (лінія + точки + картки) ---
  const animateTimelines = () => {
    const sections = contentEl.value.querySelectorAll('section')
    sections.forEach(sec => {
      const line  = sec.querySelector('.js-timeline-line')
      const dots  = sec.querySelectorAll('.js-m-dot')
      const cards = sec.querySelectorAll('.js-m-card')

      if (line) {
        gsap.fromTo(line, { scaleY: 0, transformOrigin: 'top' }, {
          scaleY: 1, ease: 'none',
          scrollTrigger: {
            trigger: sec,
            start: 'top 80%',
            end: 'bottom 20%',
            scrub: true,
            markers: MARKERS,
          },
        })
      }

      if (dots.length || cards.length) {
        const tl = gsap.timeline({
          defaults: { duration: 0.5, ease: 'power2.out' },
          scrollTrigger: { trigger: sec, start: 'top 75%', toggleActions: 'play none none reverse', markers: MARKERS }
        })
        dots.length  && tl.from(dots,  { opacity: 0, scale: 0.6, stagger: 0.08 }, 0)
        cards.length && tl.from(cards, { opacity: 0, y: 16,     stagger: 0.10 }, 0)
      }
    })
  }

  // --- matchMedia: десктоп / мобайл ---
  mm.add({
    desktop: '(min-width: 768px)',
    mobile:  '(max-width: 767.98px)',
  }, (ctx) => {
    const { desktop } = ctx.conditions

    intro()
    parallax()
    readingProgress()
    animateTimelines()

    if (desktop) pinLeft()

    // ОБОВʼЯЗКОВО: після всіх тригерів — refresh. :contentReference[oaicite:2]{index=2}
    ScrollTrigger.refresh()
  })
})

onUnmounted(() => {
  if (mm) mm.revert()
  triggers.forEach(t => t.kill())
  triggers = []
})
</script>
<style scoped>
/* скільки «не доходити» до країв секції декоративному шару */
:root { --fs-edge-safe: clamp(18px, 3.5vw, 42px); }

/* декоративний шар секції */
.fs-decor{
  position: absolute;
  z-index: -1;
  inset: var(--fs-edge-safe);
  pointer-events: none;

  /* ІЗОЛЯЦІЯ блендингу */
  isolation: isolate; /* створює свій stacking context */
  /* mdn: https://developer.mozilla.org/docs/Web/CSS/isolation */ /* :contentReference[oaicite:4]{index=4} */

  /* власне плями — кілька radial-gradient усередині секції */
  background-image:
    radial-gradient(28rem 28rem at -3rem -3rem, rgba(12,124,120,.16), transparent 62%),
    radial-gradient(30rem 30rem at calc(100% + 3rem) calc(100% + 1.5rem), rgba(15,166,160,.14), transparent 64%),
    radial-gradient(22rem 22rem at 50% 38%, rgba(12,124,120,.10), transparent 66%);
  background-repeat: no-repeat;
  background-blend-mode: normal; /* без mix-blend-mode для стабільності */
  /* mdn: https://developer.mozilla.org/docs/Web/CSS/gradient/radial-gradient */ /* :contentReference[oaicite:5]{index=5} */

  /* м’яке “пір’я” по краях, щоб НІЧОГО не торкалось меж секції */
  /* mdn: https://developer.mozilla.org/docs/Web/CSS/mask-image */ /* :contentReference[oaicite:6]{index=6} */
  -webkit-mask-image: radial-gradient(140% 140% at 50% 50%, #000 72%, transparent 100%);
  mask-image: radial-gradient(140% 140% at 50% 50%, #000 72%, transparent 100%);
  -webkit-mask-repeat: no-repeat;
  mask-repeat: no-repeat;
}

/* перестраховка від субпіксельних артефактів на дуже великих масштабах */
.section-surface{ backface-visibility: hidden; transform: translateZ(0); }
</style>