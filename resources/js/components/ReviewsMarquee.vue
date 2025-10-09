<!-- resources/js/components/ReviewsMarquee.vue -->
<template>
  <section
    class="reviews-marquee relative overflow-hidden section-surface"
    aria-label="Відгуки студентів — безперервна стрічка"
  >
    <div
      ref="blobTopLeft"
      aria-hidden="true"
      class="pointer-events-none absolute -top-24 -left-24 h-72 w-72 rounded-full blur-3xl opacity-10 mix-blend-multiply"
      style="background: radial-gradient(closest-side, rgba(15,166,160,.55), transparent 70%);"
    />
    <div
      ref="blobBottomRight"
      aria-hidden="true"
      class="pointer-events-none absolute -bottom-28 -right-32 h-80 w-80 rounded-full blur-3xl opacity-10 mix-blend-multiply"
      style="background: radial-gradient(closest-side, rgba(15,166,160,.45), transparent 70%);"
    />

    <div class="mx-auto max-w-7xl px-6 py-16 md:py-24">
      <header class="mx-auto mb-8 max-w-3xl text-center md:mb-12">
        <p v-if="badgeText" class="badge-muted font-display inline-block mb-3">{{ badgeText }}</p>
        <h2 class="heading-1 font-display tracking-tight text-secondary">{{ titleText }}</h2>
      </header>

      <div class="relative" aria-label="Стрічка відгуків — трек 1">
        <Swiper
          :modules="swiperModules"
          :loop="canLoop"
          :watch-overflow="true"
          slides-per-view="auto"
          :slides-per-group="1"
          :space-between="20"
          :breakpoints="{ 768:{ spaceBetween:24 }, 1024:{ spaceBetween:28 } }"
          :free-mode="{ enabled:true, momentum:false }"
          :allow-touch-move="true"
          :autoplay="autoplayOpts1"
          :speed="12000"
          :grab-cursor="true"
          :a11y="true"
          class="!overflow-visible"
          aria-roledescription="carousel"
          aria-label="Слайдер відгуків, трек 1"
          @swiper="onSwiper1"
          @autoplayStop="scheduleResume1"
        >
          <SwiperSlide v-for="(r,i) in track1" :key="`t1-${i}`" class="!w-auto">
            <article
              class="group w-[320px] sm:w-[380px] md:w-[460px] lg:w-[520px]
                     me-5 rounded-2xl bg-white/80 backdrop-blur ring-1 ring-slate-200
                     shadow-[0_8px_30px_rgba(0,0,0,0.06)]
                     px-6 py-5 md:px-7 md:py-6
                     transition-transform duration-200 ease-out hover:-translate-y-0.5
                     hover:shadow-[0_16px_40px_rgba(0,0,0,0.10)]
                     focus:outline-none focus-visible:ring-2 focus-visible:ring-teal-500/60"
              tabindex="0" aria-label="Картка відгуку"
            >
              <div class="flex items-center gap-4">
                <img :src="r.avatar" :alt="`Аватар ${r.name}`"
                     class="h-14 w-14 md:h-16 md:w-16 rounded-full object-cover ring-1 ring-slate-200/70"
                     loading="lazy" width="64" height="64" />
                <div class="min-w-0">
                  <div class="flex items-center gap-2">
                    <h4 class="truncate text-base md:text-lg font-semibold text-slate-900">{{ r.name }}</h4>
                    <span v-if="r.course"
                          class="inline-flex shrink-0 items-center rounded-full bg-teal-50 px-2 py-0.5 text-[12px] md:text-sm font-medium text-teal-700 ring-1 ring-teal-200">
                      {{ r.course }}
                    </span>
                  </div>
                  <div class="mt-1 text-[14px] md:text-[15px] leading-none text-teal-600" role="img"
                       :aria-label="`Рейтинг: ${r.stars} з 5`">
                    {{ '★★★★★'.slice(0, r.stars) }}
                  </div>
                </div>
              </div>
              <p class="mt-4 text-[15px] md:text-[16px] leading-relaxed text-slate-700/90 transition-opacity duration-200 group-hover:opacity-95">
                {{ r.text }}
              </p>
            </article>
          </SwiperSlide>
        </Swiper>
      </div>

      <div class="relative mt-8 hidden md:block" aria-label="Стрічка відгуків — трек 2">
        <Swiper
          :modules="swiperModules"
          :loop="canLoop"
          :watch-overflow="true"
          slides-per-view="auto"
          :slides-per-group="1"
          :space-between="20"
          :breakpoints="{ 768:{ spaceBetween:24 }, 1024:{ spaceBetween:28 } }"
          :free-mode="{ enabled:true, momentum:false }"
          :allow-touch-move="true"
          :autoplay="autoplayOpts2"
          :speed="18000"
          :grab-cursor="true"
          :a11y="true"
          class="!overflow-visible"
          aria-roledescription="carousel"
          aria-label="Слайдер відгуків, трек 2"
          @swiper="onSwiper2"
          @autoplayStop="scheduleResume2"
        >
          <SwiperSlide v-for="(r,i) in track2" :key="`t2-${i}`" class="!w-auto">
            <article
              class="group w-[320px] sm:w-[380px] md:w-[460px] lg:w-[520px]
                     me-5 rounded-2xl bg-white/80 backdrop-blur ring-1 ring-slate-200
                     shadow-[0_8px_30px_rgba(0,0,0,0.06)]
                     px-6 py-5 md:px-7 md:py-6
                     transition-transform duration-200 ease-out hover:-translate-y-0.5
                     hover:shadow-[0_16px_40px_rgba(0,0,0,0.10)]
                     focus:outline-none focus-visible:ring-2 focus-visible:ring-teal-500/60"
              tabindex="0" aria-label="Картка відгуку"
            >
              <div class="flex items-center gap-4">
                <img :src="r.avatar" :alt="`Аватар ${r.name}`"
                     class="h-14 w-14 md:h-16 md:w-16 rounded-full object-cover ring-1 ring-slate-200/70"
                     loading="lazy" width="64" height="64" />
                <div class="min-w-0">
                  <div class="flex items-center gap-2">
                    <h4 class="truncate text-base md:text-lg font-semibold text-slate-900">{{ r.name }}</h4>
                    <span v-if="r.course"
                          class="inline-flex shrink-0 items-center rounded-full bg-teal-50 px-2 py-0.5 text-[12px] md:text-sm font-medium text-teal-700 ring-1 ring-teal-200">
                      {{ r.course }}
                    </span>
                  </div>
                  <div class="mt-1 text-[14px] md:text-[15px] leading-none text-teal-600" role="img"
                       :aria-label="`Рейтинг: ${r.stars} з 5`">
                    {{ '★★★★★'.slice(0, r.stars) }}
                  </div>
                </div>
              </div>
              <p class="mt-4 text-[15px] md:text-[16px] leading-relaxed text-slate-700/90 transition-opacity duration-200 group-hover:opacity-95">
                {{ r.text }}
              </p>
            </article>
          </SwiperSlide>
        </Swiper>
      </div>

      <div class="mt-12 text-center">
        <a
          :href="buttonData.href"
          target="_blank"
          rel="noopener noreferrer"
          class="btn-outline inline-flex items-center gap-2 rounded-xl px-5 py-3 font-display
                 ring-1 ring-teal-300/60 text-teal-800 hover:bg-teal-50/70 hover:ring-teal-400
                 focus:outline-none focus-visible:ring-2 focus-visible:ring-teal-500/70"
          aria-label="Більше відгуків — відкриється в новій вкладці"
        >
          <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5" aria-hidden="true">
            <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 6H18m0 0v4.5M18 6l-6 6M8.25 7.5H7.5A2.25 2.25 0 0 0 5.25 9.75v6A2.25 2.25 0 0 0 7.5 18h6a2.25 2.25 0 0 0 2.25-2.25v-.75" />
          </svg>
          <span>{{ buttonData.text }}</span>
        </a>
        <p class="mt-2 text-sm text-secondary/70">
          Перейдіть у наш профіль — там ще більше сторіз і відгуків від студентів.
        </p>
      </div>
    </div>
  </section>
</template>

<script setup lang="ts">
import { ref, computed, onMounted } from 'vue'
import { Swiper, SwiperSlide } from 'swiper/vue'
import { Autoplay, FreeMode, A11y } from 'swiper/modules'
import 'swiper/css'
import 'swiper/css/free-mode'

type Review = { name: string; avatar: string; text: string; stars: number; course?: string }
type ReviewInput = { name?: string; avatar?: string; text?: string; stars?: number; course?: string | null }

const FALLBACK_BADGE = 'Наші відгуки'
const FALLBACK_TITLE = 'Нам довіряють — результати студентів це наша перевага'
const FALLBACK_BUTTON_TEXT = 'Більше відгуків в Instagram'
const FALLBACK_BUTTON_URL = 'https://instagram.com/your.profile'
const FALLBACK_REVIEWS: Review[] = [
  { name: 'Марія Коваль', avatar: 'https://i.pravatar.cc/96?img=1', stars: 5, course: 'IELTS', text: 'Класні уроки, багато розмовної практики і чіткий план підготовки. За місяць стала впевненіше говорити, рекомендую!' },
  { name: 'Олег С.', avatar: 'https://i.pravatar.cc/96?img=2', stars: 5, course: 'Business English', text: 'Сучасні завдання, реальні кейси з роботи. Дуже подобається формат — завжди тримає фокус і дає результат.' },
  { name: 'Ірина Ч.', avatar: 'https://i.pravatar.cc/96?img=3', stars: 5, course: 'General', text: 'Дуже комфортно й ефективно. Індивідуальний підхід, помітний прогрес вже за кілька тижнів.' },
  { name: 'Андрій П.', avatar: 'https://i.pravatar.cc/96?img=4', stars: 5, course: 'Speaking Club', text: 'Динамічні зустрічі, багато говоріння, виправлення помилок у реальному часі — супер!' },
]

const props = defineProps<{
  badge?: string
  title?: string
  buttonText?: string
  buttonUrl?: string
  reviews?: ReviewInput[]
}>()

const trimString = (value: unknown, fallback = ''): string => {
  if (typeof value === 'string') {
    const trimmed = value.trim()
    return trimmed !== '' ? trimmed : fallback
  }
  if (typeof value === 'number') {
    const trimmed = String(value).trim()
    return trimmed !== '' ? trimmed : fallback
  }
  return fallback
}

const badgeText = computed(() => trimString(props.badge, FALLBACK_BADGE))
const titleText = computed(() => trimString(props.title, FALLBACK_TITLE))
const buttonData = computed(() => ({
  text: trimString(props.buttonText, FALLBACK_BUTTON_TEXT),
  href: trimString(props.buttonUrl, FALLBACK_BUTTON_URL),
}))

const normalizedReviews = computed<Review[]>(() => {
  if (! Array.isArray(props.reviews)) {
    return []
  }

  return props.reviews
    .map((item) => {
      const name = trimString(item?.name, '')
      const text = trimString(item?.text, '')
      if (name === '' || text === '') {
        return null
      }

      const course = trimString(item?.course ?? '', '') || undefined
      const starsRaw = typeof item?.stars === 'number' ? item.stars : Number(item?.stars)
      let stars = Number.isFinite(starsRaw) ? Math.round(starsRaw) : 5
      stars = Math.max(0, Math.min(5, stars))

      const avatarCandidate = trimString(item?.avatar, '')
      const avatar = avatarCandidate !== '' ? avatarCandidate : 'https://i.pravatar.cc/96?img=7'

      return { name, text, course, stars, avatar }
    })
    .filter((entry): entry is Review => entry !== null)
})

const baseReviews = computed(() => (normalizedReviews.value.length ? normalizedReviews.value : FALLBACK_REVIEWS))
const unique = computed(() => baseReviews.value.length)
const canLoop = computed(() => unique.value >= 3)

const reduceMotion = typeof window !== 'undefined'
  ? window.matchMedia?.('(prefers-reduced-motion: reduce)')?.matches === true
  : false

const autoplayBase = reduceMotion ? false : { delay: 0, disableOnInteraction: false, pauseOnMouseEnter: true }
const autoplayOpts1 = autoplayBase
const autoplayOpts2 = autoplayBase && typeof autoplayBase === 'object'
  ? { ...autoplayBase, reverseDirection: true }
  : autoplayBase

const track1 = computed(() => baseReviews.value.concat(baseReviews.value))
const track2Base = computed(() => [...baseReviews.value].reverse())
const track2 = computed(() => track2Base.value.concat(track2Base.value))

const swiperModules = [Autoplay, FreeMode, A11y]

const blobTopLeft = ref<HTMLElement | null>(null)
const blobBottomRight = ref<HTMLElement | null>(null)
const sw1 = ref<any | null>(null)
const sw2 = ref<any | null>(null)
let resumeT1: ReturnType<typeof setTimeout> | null = null
let resumeT2: ReturnType<typeof setTimeout> | null = null

function onSwiper1(sw: any) { sw1.value = sw }
function onSwiper2(sw: any) { sw2.value = sw }

function scheduleResume1() {
  if (resumeT1) clearTimeout(resumeT1)
  resumeT1 = setTimeout(() => { sw1.value?.autoplay?.start?.() }, 20000)
}
function scheduleResume2() {
  if (resumeT2) clearTimeout(resumeT2)
  resumeT2 = setTimeout(() => { sw2.value?.autoplay?.start?.() }, 20000)
}

onMounted(async () => {
    try {
        const { gsap } = await import('gsap')
        const { ScrollTrigger } = await import('gsap/ScrollTrigger')
        gsap.registerPlugin(ScrollTrigger)
        gsap.timeline({ scrollTrigger: { trigger: document.body, start: 'top top', end: 'bottom bottom', scrub: 0.6 } })
          .to(blobTopLeft.value, { x: 60, y: 40 }, 0)
          .to(blobBottomRight.value, { x: -80, y: -50 }, 0)
    } catch (error) {
        console.warn('GSAP lazy load failed', error)
    }
})
</script>

<style scoped>
.reviews-marquee .swiper-slide { width: auto !important; }
.reviews-marquee .swiper-wrapper { transition-timing-function: linear !important; }
</style>
