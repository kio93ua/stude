<!-- resources/js/components/FounderStory.vue -->
<template>
  <section
    id="founder-story"
    ref="sectionEl"
    class="section-surface relative overflow-hidden py-16 md:py-24"
    aria-labelledby="founder-heading"
  >
    <div aria-hidden class="fs-decor"></div>

    <div class="mx-auto max-w-7xl px-6">
      <header class="max-w-3xl">
        <p v-if="badgeText" ref="badgeEl" class="badge-muted font-display inline-block">{{ badgeText }}</p>
        <h2 id="founder-heading" ref="titleEl" class="mt-4 heading-1 font-display tracking-tight text-secondary">
          {{ founderLocal.name }} — {{ founderLocal.role }}
        </h2>
        <p v-if="subtitleText" ref="subtitleEl" class="mt-3 text-secondary/85 max-w-2xl">
          {{ subtitleText }}
        </p>
      </header>

      <div class="mt-10 grid md:grid-cols-2 gap-10 items-start">
        <aside ref="stickyEl">
          <article class="overflow-hidden rounded-2xl ring-1 ring-slate-200 bg-white/80 backdrop-blur shadow-[0_8px_30px_rgba(0,0,0,0.06)]">
            <figure class="relative">
              <img
                :src="founderLocal.photo.src"
                :alt="founderLocal.photo.alt"
                class="w-full object-cover aspect-[4/5] h-[56vh] md:h-[min(70vh,720px)]"
                ref="photoEl"
                @load="onMediaLoaded"
              />
            </figure>
            <div class="p-5">
              <h3 class="text-lg font-semibold text-slate-900">{{ founderLocal.name }}</h3>
              <p class="text-slate-600">{{ founderLocal.role }}</p>

              <div class="mt-4 flex items-center gap-3">
                <a v-if="founderLocal.socials.linkedin" :href="founderLocal.socials.linkedin" target="_blank" rel="noopener"
                   class="inline-flex h-9 w-9 items-center justify-center rounded-full ring-1 ring-slate-200 text-slate-500 hover:text-teal-700 hover:ring-teal-300 transition"
                   aria-label="LinkedIn"></a>
                <a v-if="founderLocal.socials.instagram" :href="founderLocal.socials.instagram" target="_blank" rel="noopener"
                   class="inline-flex h-9 w-9 items-center justify-center rounded-full ring-1 ring-slate-200 text-slate-500 hover:text-teal-300 transition"
                   aria-label="Instagram"></a>
                <a v-if="founderLocal.socials.site" :href="founderLocal.socials.site" target="_blank" rel="noopener"
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

          <section v-for="(sec, idx) in contentLocal" :key="`sec-${idx}`" class="mt-8">
            <h3 class="text-xl md:text-2xl font-semibold text-slate-900">{{ sec.heading }}</h3>
            <div class="mt-3 space-y-4 text-slate-700 text-base md:text-[18px] leading-7 md:leading-8">
              <p v-for="(paragraph, i) in sec.body" :key="`sec-${idx}-${i}`">{{ paragraph }}</p>
            </div>

            <blockquote v-if="sec.quote" class="mt-6 rounded-2xl bg-white/80 backdrop-blur ring-1 ring-slate-200 p-5">
              <p class="text-slate-800 italic">“{{ sec.quote.text }}”</p>
              <footer class="mt-2 text-sm text-slate-500">— {{ sec.quote.author }}</footer>
            </blockquote>
          </section>

          <section
            v-for="(extraSection, extraIdx) in extraLocal"
            :key="`extra-${extraIdx}`"
            :class="extraIdx === 0 ? 'mt-12' : 'mt-10'"
          >
            <h3 class="text-xl md:text-2xl font-semibold text-slate-900">{{ extraSection.heading }}</h3>
            <div class="mt-3 space-y-4 text-slate-700 text-base md:text-[18px] leading-7 md:leading-8">
              <p v-for="(paragraph, pi) in extraSection.body" :key="`extra-${extraIdx}-${pi}`">{{ paragraph }}</p>
            </div>
          </section>
        </div>
      </div>
    </div>
  </section>
</template>

<script setup>
import { onMounted, onUnmounted, reactive, ref, computed } from 'vue'
import { gsap } from 'gsap'
import { ScrollTrigger } from 'gsap/ScrollTrigger'
gsap.registerPlugin(ScrollTrigger)

/** Локальні фолбеки для обчислень (не використовуються в defineProps) */
const FB_BADGE = 'Історія засновника'
const FB_SUBTITLE = 'Від першого учня до власної школи: шлях, помилки і відкриття, які сформували наш підхід до навчання.'
const FB_FOUNDER = {
  name: 'Олена Коваль',
  role: 'Засновниця школи англійської',
  photo: { src: '/images/founder.png', alt: 'Портрет засновниці' },
}

/** ВАЖЛИВО: дефолти інлайн — без посилань на локальні змінні */
const props = defineProps({
  badge: { type: String, default: 'Історія засновника' },
  subtitle: {
    type: String,
    default: 'Від першого учня до власної школи: шлях, помилки і відкриття, які сформували наш підхід до навчання.',
  },
  founder: {
    type: Object,
    default: () => ({
      name: 'Іветта Тимканич',
      role: 'Засновниця школи англійської',
      photo: { src: '/images/founder.png', alt: 'Портрет засновниці' },
      socials: {
        linkedin: 'https://www.linkedin.com/',
        instagram: 'https://www.instagram.com/',
        site: 'https://example.com',
      },
    }),
  },
  content: {
    type: Array,
    default: () => ([
      {
        heading: 'Початок шляху',
        body: [
          'Усе почалося з індивідуальних занять удома: один стіл, ноутбук та велике бажання допомогти студентам заговорити впевнено.',
          'Поступово сформувалася методика, що поєднує комунікативний підхід та завдання з реальних ситуацій.',
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
          'Мета — дати інструменти і впевненість, щоб англійська працювала у реальному житті.',
        ],
      },
    ]),
  },
  extra: {
    type: Array,
    default: () => ([
      {
        heading: 'Як з’явилася методика',
        body: [
          'Ми пробували різні формати — від класичних підручників до ситуативних рольових ігор. Залишили те, що реально працює для дорослих і підлітків: короткі міні-цілі, регулярні спікінг-сесії, фокус на лексичних блоках і практиці зі сценаріями з життя.',
          'Кожен модуль завершується підсумковими розмовами і мікро-рефлексією: що вже виходить, де є бар’єр, і як його зняти. Так все поступово перетворюється на стабільну звичку говорити.',
        ],
      },
      {
        heading: 'Сьогодні та далі',
        body: [
          'Системно оновлюємо програми кожен семестр: додаємо сучасні теми, короткі відео, завдання на слух і говоріння та адаптуємо матеріали під реальні задачі студентів — робота, подорожі, навчання, переїзд.',
          'Наша мета — зробити англійську корисним інструментом на щодень. Ви отримуєте зрозумілий план, підтримку викладача і прозорі критерії прогресу без зайвої “води”.',
        ],
      },
    ]),
  },
  debug: { type: Boolean, default: false },
})

/** Утиліти нормалізації */
const trimString = (value, fallback = '') => {
  const str = typeof value === 'string' ? value.trim() : typeof value === 'number' ? String(value).trim() : ''
  return str !== '' ? str : fallback
}
const optionalUrl = (value) => {
  const str = trimString(value, '')
  return str !== '' ? str : null
}
const mapParagraphs = (value) => {
  if (!Array.isArray(value)) return []
  return value
    .map((item) => {
      if (typeof item === 'string') return item.trim()
      if (item && typeof item === 'object') return trimString(item.value ?? item.text ?? '', '')
      return ''
    })
    .filter((p) => p !== '')
}
const mapSections = (value, withQuote = false) => {
  if (!Array.isArray(value)) return []
  const sections = []
  for (const entry of value) {
    if (!entry || typeof entry !== 'object') continue
    const heading = trimString(entry.heading ?? '', '')
    const body = mapParagraphs(entry.body)
    if (heading === '' || body.length === 0) continue
    const record = { heading, body }
    if (withQuote) {
      const quoteText = trimString(entry.quote?.text ?? entry.quote_text ?? '', '')
      if (quoteText !== '') {
        record.quote = {
          text: quoteText,
          author: trimString(entry.quote?.author ?? entry.quote_author ?? '', ''),
        }
      }
    }
    sections.push(record)
  }
  return sections
}

/** Обчислені значення з безпечними фолбеками */
const badgeText = computed(() => trimString(props.badge, FB_BADGE))
const subtitleText = computed(() => {
  const s = trimString(props.subtitle, '')
  return s !== '' ? s : null
})
const founderLocal = computed(() => {
  const source = props.founder ?? {}
  const name = trimString(source.name ?? '', FB_FOUNDER.name)
  const role = trimString(source.role ?? '', FB_FOUNDER.role)
  const photoSrc = trimString(source.photo?.src ?? '', FB_FOUNDER.photo.src)
  const photoAlt = trimString(source.photo?.alt ?? '', `Фото: ${name}`)
  return {
    name,
    role,
    photo: { src: photoSrc, alt: photoAlt },
    socials: {
      linkedin: optionalUrl(source.socials?.linkedin ?? source.linkedin ?? null),
      instagram: optionalUrl(source.socials?.instagram ?? source.instagram ?? null),
      site: optionalUrl(source.socials?.site ?? source.site ?? null),
    },
  }
})
const contentLocal = computed(() => {
  const normalized = mapSections(props.content, true)
  return normalized.length ? normalized : mapSections([
    { heading: 'Початок шляху', body: ['…'] },
  ], true)
})
const extraLocal = computed(() => {
  const normalized = mapSections(props.extra, false)
  return normalized.length ? normalized : []
})

/** Refs + GSAP */
const sectionEl  = ref(null)
const stickyEl   = ref(null)
const contentEl  = ref(null)
const progressEl = ref(null)
const photoEl    = ref(null)
const badgeEl    = ref(null)
const titleEl    = ref(null)
const subtitleEl = ref(null)
const progressNow = ref(0)

const blobStyleA = reactive({ background: 'linear-gradient(135deg, #0C7C78, #0FA6A0)' })
const blobStyleB = reactive({ background: 'linear-gradient(135deg, #0FA6A0, #0C7C78)' })
const blobStyleC = reactive({ background: 'linear-gradient(135deg, #0C7C78, #0FA6A0)' })

let mm
let triggers = []
const onMediaLoaded = () => ScrollTrigger.refresh()

onMounted(() => {
  const MARKERS = props.debug
  mm = gsap.matchMedia()

  const intro = () => {
    const items = [badgeEl.value, titleEl.value, subtitleEl.value].filter(Boolean)
    if (items.length) {
      gsap.from(items, { opacity: 0, y: 16, duration: 0.6, stagger: 0.12, ease: 'power2.out' })
    }
    if (photoEl.value) {
      gsap.fromTo(photoEl.value,
        { clipPath: 'inset(0 0 100% 0)', opacity: 0.6 },
        { clipPath: 'inset(0 0 0% 0)', opacity: 1, duration: 0.9, ease: 'power3.out', delay: 0.1 }
      )
    }
  }

  const readingProgress = () => {
    const t = ScrollTrigger.create({
      trigger: contentEl.value,
      start: 'top top',
      end: 'bottom bottom',
      onUpdate: self => {
        const p = Math.round(self.progress * 100)
        progressNow.value = p
        if (progressEl.value) progressEl.value.style.width = `${p}%`
      },
      scrub: true,
      markers: MARKERS,
    })
    triggers.push(t)
  }

  const pinLeft = () => {
    const headerOffset = 88
    const t = ScrollTrigger.create({
      trigger: contentEl.value,
      pin: stickyEl.value,
      start: `top top+=${headerOffset}`,
      end: () => "+=" + Math.max(0, contentEl.value.scrollHeight - stickyEl.value.offsetHeight),
      pinSpacing: true,
      anticipatePin: 1, // за потреби можна змінити або прибрати
      invalidateOnRefresh: true,
      pinReparent: true,
      markers: MARKERS,
    })
    triggers.push(t)
  }

  mm.add(
    { desktop: '(min-width: 768px)', mobile: '(max-width: 767.98px)' },
    (ctx) => {
      const { desktop } = ctx.conditions
      intro()
      readingProgress()
      if (desktop) pinLeft()
      ScrollTrigger.refresh()
    }
  )
})

onUnmounted(() => {
  if (mm) mm.revert()
  triggers.forEach(t => t.kill())
  triggers = []
})
</script>

<style scoped>
:root { --fs-edge-safe: clamp(18px, 3.5vw, 42px); }

.fs-decor{
  position: absolute;
  z-index: -1;
  inset: var(--fs-edge-safe);
  pointer-events: none;
  isolation: isolate;
  background-image:
    radial-gradient(28rem 28rem at -3rem -3rem, rgba(12,124,120,.16), transparent 62%),
    radial-gradient(30rem 30rem at calc(100% + 3rem) calc(100% + 1.5rem), rgba(15,166,160,.14), transparent 64%),
    radial-gradient(22rem 22rem at 50% 38%, rgba(12,124,120,.10), transparent 66%);
  background-repeat: no-repeat;
  background-blend-mode: normal;
  -webkit-mask-image: radial-gradient(140% 140% at 50% 50%, #000 72%, transparent 100%);
  mask-image: radial-gradient(140% 140% at 50% 50%, #000 72%, transparent 100%);
  -webkit-mask-repeat: no-repeat;
  mask-repeat: no-repeat;
}

.section-surface{ backface-visibility: hidden; transform: translateZ(0); }
</style>
