<!-- resources/js/components/FounderStory.vue -->
<template>
  <section
    id="founder-story"
    ref="sectionEl"
    class="relative overflow-visible bg-gradient-to-br from-[#BFF3E2] via-white to-[#DDF9F2]"
    aria-labelledby="founder-heading"
  >
    <!-- Фонові м’які плями (parallax) -->
    <div aria-hidden class="js-blob pointer-events-none absolute -top-24 -left-24 h-72 w-72 rounded-full blur-3xl opacity-30"
         :style="blobStyleA"></div>
    <div aria-hidden class="js-blob pointer-events-none absolute -bottom-28 -right-20 h-80 w-80 rounded-full blur-3xl opacity-25"
         :style="blobStyleB"></div>
    <div aria-hidden class="js-blob pointer-events-none absolute top-1/3 left-1/2 h-64 w-64 -translate-x-1/2 rounded-full blur-3xl opacity-20"
         :style="blobStyleC"></div>

    <div class="mx-auto max-w-7xl px-6 py-16 md:py-24">
      <!-- Хедер -->
      <header class="max-w-3xl">
        <div ref="badgeEl"
             class="inline-flex items-center gap-2 rounded-full bg-teal-100/80 px-4 py-1 text-sm font-semibold text-teal-700 ring-1 ring-teal-200">
          Історія засновника
        </div>
        <h2 id="founder-heading" ref="titleEl"
            class="mt-4 text-3xl md:text-4xl font-bold tracking-tight text-slate-900">
          {{ founder.name }} — {{ founder.role }}
        </h2>
        <p ref="subtitleEl" class="mt-3 text-base md:text-lg text-slate-600 max-w-2xl">
          Від першого учня до власної школи: шлях, помилки і відкриття, які сформували наш підхід до навчання.
        </p>
      </header>

      <!-- Дві колонки -->
      <div class="mt-10 grid md:grid-cols-2 gap-10 items-start">
        <!-- Ліва панель (ПІНУЄ GSAP, НЕ sticky) -->
        <aside ref="stickyEl" class="">
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

              <!-- Соцкнопки -->
              <div class="mt-4 flex items-center gap-3">
                <a v-if="founder.socials?.linkedin" :href="founder.socials.linkedin" target="_blank" rel="noopener"
                   class="inline-flex h-9 w-9 items-center justify-center rounded-full ring-1 ring-slate-200 text-slate-500 hover:text-teal-700 hover:ring-teal-300 transition"
                   aria-label="LinkedIn">
                  <svg viewBox="0 0 24 24" class="h-5 w-5" fill="currentColor" aria-hidden="true"><path d="M6.94 8.5H3.56V21h3.38V8.5zM5.25 3C4.17 3 3.3 3.86 3.3 4.94c0 1.07.86 1.94 1.94 1.94s1.94-.86 1.94-1.94S6.33 3 5.25 3zm6.69 5.5c-2 0-3.2 1.09-3.74 2.13h-.05V8.5H5.9c.04 1.58 0 12.5 0 12.5h3.36v-7c0-.37.03-.75.14-1.02.31-.75 1.02-1.53 2.2-1.53 1.55 0 2.17 1.16 2.17 2.86V21h3.36v-7.11c0-3.8-2.03-5.56-4.76-5.56z"/></svg>
                </a>
                <a v-if="founder.socials?.instagram" :href="founder.socials.instagram" target="_blank" rel="noopener"
                   class="inline-flex h-9 w-9 items-center justify-center rounded-full ring-1 ring-slate-200 text-slate-500 hover:text-teal-700 hover:ring-teal-300 transition"
                   aria-label="Instagram">
                  <svg viewBox="0 0 24 24" class="h-5 w-5" fill="currentColor" aria-hidden="true"><path d="M12 2.2c3.2 0 3.6 0 4.9.1 1.2.1 1.8.2 2.2.4.6.2 1 .5 1.5.9.5.5.7.9.9 1.5.2.4.3 1 .4 2.2.1 1.3.1 1.7.1 4.9s0 3.6-.1 4.9c-.1 1.2-.2 1.8-.4 2.2-.2.6-.5 1-1 1.5s-.9.7-1.5.9c-.4.2-1 .3-2.2.4-1.3.1-1.7.1-4.9.1s-3.6 0-4.9-.1c-1.2-.1-1.8-.2-2.2-.4-.6-.2-1-.5-1.5-1s-.7-.9-.9-1.5c-.2-.4-.3-1-.4-2.2C2.2 15.6 2.2 15.2 2.2 12s0-3.6.1-4.9c.1-1.2.2-1.8.4-2.2.2-.6.5-1 1-1.5S4.6 2.5 5.2 2.3c.4-.2 1-.3 2.2-.4C8.7 1.8 9.1 1.8 12 1.8m0 1.8c-3.1 0-3.5 0-4.7.1-1 .1-1.6.2-1.9.3-.5.2-.9.4-1.2.8-.4.4-.6.7-.8 1.2-.1.3-.2.9-.3 1.9-.1 1.2-.1 1.6-.1 4.7s0 3.5.1 4.7c.1 1 .2 1.6.3 1.9.2.5.4.9.8 1.2.4.4.7.6 1.2.8.3.1.9.2 1.9.3 1.2.1 1.6.1 4.7.1s3.5 0 4.7-.1zm0 2.7A5.7 5.7 0 1 1 6.3 12 5.7 5.7 0 0 1 12 6.3zm0 9.3A3.6 3.6 0 1 0 8.4 12 3.6 3.6 0 0 0 12 15.6zm5.9-9.9a1.3 1.3 0 1 1-1.3-1.3 1.3 1.3 0 0 1 1.3 1.3z"/></svg>
                </a>
                <a v-if="founder.socials?.site" :href="founder.socials.site" target="_blank" rel="noopener"
                   class="inline-flex h-9 px-4 items-center justify-center rounded-full ring-1 ring-slate-200 text-slate-600 hover:text-teal-700 hover:ring-teal-300 transition text-sm">
                  Сайт
                </a>
              </div>
            </div>
          </article>
        </aside>

        <!-- Права колонка -->
        <div class="relative" ref="contentEl">
          <!-- Sticky прогрес-бар (праворуч вгорі правої колонки) -->
          <div class="sticky top-0 z-10 -mt-2 h-1 bg-slate-200/70 rounded-full">
            <div
              ref="progressEl"
              class="h-1 w-0 rounded-full bg-gradient-to-r from-[#0C7C78] to-[#0FA6A0]"
              role="progressbar"
              aria-label="Прогрес читання історії засновника"
              :aria-valuemin="0"
              :aria-valuemax="100"
              :aria-valuenow="progressNow"
              tabindex="0"
            />
          </div>

          <!-- Контентні секції -->
          <section v-for="(sec, idx) in content" :key="idx" class="mt-8">
            <h3 class="text-xl md:text-2xl font-semibold text-slate-900">{{ sec.heading }}</h3>
            <div class="mt-3 space-y-4 text-slate-700 text-base md:text-[18px] leading-7 md:leading-8">
              <p v-for="(p, i) in sec.body" :key="i">{{ p }}</p>
            </div>

            <!-- Опційна цитата -->
            <blockquote v-if="sec.quote" class="mt-6 rounded-2xl bg-white/80 backdrop-blur ring-1 ring-slate-200 p-5">
              <p class="text-slate-800 italic">“{{ sec.quote.text }}”</p>
              <footer class="mt-2 text-sm text-slate-500">— {{ sec.quote.author }}</footer>
            </blockquote>

            <!-- Опційні віхи -->
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
      photo: { src: '/images/founder.jpg', alt: 'Портрет засновниці' },
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
