<template>
  <section
    ref="root"
    id="advantages-split"
    class="section-surface section-surface--fixedhack py-16 md:py-24"
    aria-labelledby="advantages-heading"
  >
    <!-- (декоративні плями прибрано; за потреби додай їх як легкий декор із малою opacity, не як фон) -->

    <div class="mx-auto max-w-7xl px-6">
      <header ref="headerRef" class="mx-auto mb-8 max-w-3xl text-center md:mb-12">
        <p v-if="headerBadge" class="badge-muted font-display inline-block mb-3">{{ headerBadge }}</p>
        <h2 id="advantages-heading" class="heading-1 font-display tracking-tight text-secondary">
          {{ headerTitle }}
        </h2>
        <p v-if="headerSubtitle" class="mt-3 text-lg text-secondary/85 font-sans">{{ headerSubtitle }}</p>
      </header>

      <!-- Split -->
      <div class="grid gap-8 md:grid-cols-2 md:items-stretch">
        <!-- Ліворуч: таби + контент -->
        <div class="flex h-full flex-col gap-6 min-w-0">
          <nav aria-label="Список переваг" role="tablist"
               class="rounded-2xl bg-white/80 p-2 ring-1 ring-slate-200 backdrop-blur">
            <div class="tabs-scroll flex gap-2 overflow-x-auto px-1 py-1 snap-x snap-mandatory scroll-px-2">
              <button
                v-for="(it, idx) in itemsToUse"
                :key="`tab-${idx}`"
                type="button"
                role="tab"
                class="snap-start whitespace-nowrap rounded-full border border-teal-200/40 bg-white/80 px-3 py-2 text-sm font-medium text-slate-700 outline-none transition hover:bg-teal-50/50"
                :class="idx === active
                  ? 'ring-1 ring-[#0FA6A0]/50 bg-gradient-to-r from-[#0C7C78]/10 to-[#0FA6A0]/10 shadow-[0_0_0_3px_rgba(15,166,160,0.06)]'
                  : ''"
                :aria-selected="idx === active"
                :tabindex="idx === active ? 0 : -1"
                @click="goTo(idx)"
                @keydown.enter.prevent="goTo(idx)"
              >
                {{ it.title }}
              </button>
            </div>
          </nav>

          <article
            class="flex h-full flex-col rounded-2xl bg-white/80 p-6 shadow-sm ring-1 ring-slate-200 backdrop-blur md:p-8"
            aria-live="polite"
          >
            <header class="mb-4">
              <h3 class="heading-3 text-secondary">
                {{ current.title }}
              </h3>
              <p v-if="current.desc" class="mt-2 text-secondary/80">
                {{ current.desc }}
              </p>
            </header>

            <ul v-if="current.bullets?.length" class="grid gap-2" ref="bulletsEl">
              <li v-for="(b, bi) in current.bullets" :key="`b-${active}-${bi}`" class="flex items-start gap-3">
                <span class="mt-1 inline-block h-2 w-2 rounded-full bg-gradient-to-r from-[#0C7C78] to-[#0FA6A0]" aria-hidden="true"></span>
                <span class="text-secondary/90">{{ b }}</span>
              </li>
            </ul>

            <div v-if="current.icons?.length" class="mt-6 flex flex-wrap items-center gap-3">
              <img v-for="(ico, ii) in current.icons" :key="`ico-${active}-${ii}`" :src="ico" alt=""
                   class="h-8 w-8 object-contain opacity-90" loading="lazy" decoding="async" draggable="false" />
            </div>

            <div class="mt-6">
              <a :href="cta.href"
                 class="rounded-2xl bg-gradient-to-r from-[#0C7C78] to-[#0FA6A0] px-5 py-2 font-semibold text-white shadow-sm transition focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-[#0FA6A0] focus-visible:ring-offset-2">
                {{ cta.text }}
              </a>
            </div>
          </article>
        </div>

        <!-- Праворуч: Swiper з навігацією -->
        <div class="relative h-full min-w-0">
          <Swiper
            class="adv-swiper h-full w-full overflow-hidden rounded-2xl bg-white/80 ring-1 ring-slate-200 backdrop-blur"
            :modules="swiperModules"
            :slides-per-view="1"
            :space-between="24"
            :loop="itemsToUse.length > 1"
            :speed="650"
            :pagination="{ clickable: true }"
            :navigation="true"
            :allow-touch-move="true"
            :observer="true"
            :observe-parents="true"
            effect="fade"
            :fade-effect="{ crossFade: true }"
            @slideChange="onSlideChange"
            @swiper="onSwiper"
            aria-label="Фотослайдер переваг">
            <SwiperSlide v-for="(it, idx) in itemsToUse" :key="`slide-${idx}`" class="adv-slide">
              <figure class="adv-figure">
                <div
                  class="adv-media"
                  :style="{ backgroundImage: `url('${it.image?.src}')` }"
                  :aria-label="it.image?.alt || it.title"
                  role="img"
                  :ref="el => (photoMasks[idx] = el as HTMLElement)"
                />
                <div aria-hidden class="adv-glow" />
              </figure>
            </SwiperSlide>
          </Swiper>
        </div>
      </div>
    </div>
  </section>
</template>


<script setup lang="ts">
import { ref, computed, onMounted, onBeforeUnmount, onBeforeUpdate } from 'vue'

import { Swiper, SwiperSlide } from 'swiper/vue'
import { Navigation, Pagination, A11y, EffectFade } from 'swiper/modules'
import type { Swiper as SwiperType } from 'swiper'
import 'swiper/css'
import 'swiper/css/navigation'
import 'swiper/css/pagination'
import 'swiper/css/effect-fade'

import gsap from 'gsap'
import { ScrollTrigger } from 'gsap/ScrollTrigger'
gsap.registerPlugin(ScrollTrigger)

type Item = {
  title?: string
  desc?: string | null
  bullets?: string[] | null
  image?: { src?: string; alt?: string | null } | null
  icons?: string[] | null
}

type Cta = {
  text?: string | null
  href?: string | null
}

const props = withDefaults(defineProps<{
  items?: Item[]
  badge?: string | null
  title?: string | null
  subtitle?: string | null
  cta?: Cta | null
}>(), {
  items: undefined,
  badge: undefined,
  title: undefined,
  subtitle: undefined,
  cta: () => ({ text: 'Записатися', href: '#contact' }),
})

/** 3 потрібні переваги (fallback) */
const fallbackItems = [
  {
    title: 'Ігрові методи',
    desc: 'Інтерактиви, рольові сценарії та міні-ігри — мотивація росте, страх помилок зникає.',
    bullets: ['Щотижневі челенджі', 'Сценарії з реального життя', 'Веселі практики замість нудної теорії'],
    image: { src: '/images/adv/gamified.jpg', alt: 'Ігрові методи' },
    icons: [],
  },
  {
    title: 'Сучасна програма вивчення',
    desc: 'Комунікативний підхід, мікрозвички та трек прогресу — чіткий результат щотижня.',
    bullets: ['Модульна структура', 'Практика > теорія', 'Персональні рекомендації'],
    image: { src: '/images/adv/modern.jpg', alt: 'Сучасна програма' },
    icons: [],
  },
  {
    title: 'Задоволені учні',
    desc: 'Тепла дружня атмосфера й підтримка — легше говорити впевнено.',
    bullets: ['Малі групи або 1-на-1', 'Зворотний зв’язок щотижня', 'Клуби розмовної практики'],
    image: { src: '/images/adv/happy.jpg', alt: 'Задоволені учні' },
    icons: [],
  },
]
const normalizedItems = computed(() => {
  const source = Array.isArray(props.items) ? props.items : []

  const mapped = source
    .map((item) => {
      const title = typeof item?.title === 'string' ? item.title.trim() : ''
      const imageSrc = typeof item?.image?.src === 'string' ? item.image.src.trim() : ''
      if (!title || !imageSrc) {
        return null
      }

      const desc = typeof item?.desc === 'string' ? item.desc.trim() : ''

      const bullets = Array.isArray(item?.bullets)
        ? item.bullets
            .map((v) => (typeof v === 'string' ? v.trim() : ''))
            .filter((v): v is string => v !== '')
        : []

      const icons = Array.isArray(item?.icons)
        ? item.icons
            .map((v) => (typeof v === 'string' ? v.trim() : ''))
            .filter((v): v is string => v !== '')
        : []

      return {
        title,
        desc: desc !== '' ? desc : undefined,
        bullets,
        image: {
          src: imageSrc,
          alt: typeof item?.image?.alt === 'string' && item.image.alt.trim() !== '' ? item.image.alt.trim() : title,
        },
        icons,
      }
    })
    .filter((entry): entry is Required<ReturnType<typeof mapped[0]>> => entry !== null)

  return mapped.length ? mapped : fallbackItems
})
const itemsToUse = normalizedItems

/* Стан */
const active = ref(0)
const swiperRef = ref<SwiperType | null>(null)

const root = ref<HTMLElement | null>(null)
const headerRef = ref<HTMLElement | null>(null)
const bulletsEl = ref<HTMLElement | null>(null)
const photoMasks = ref<HTMLElement[]>([])
const blobL = ref<HTMLElement | null>(null)
const blobR = ref<HTMLElement | null>(null)

onBeforeUpdate(() => { photoMasks.value = [] })

const swiperModules = [Navigation, Pagination, A11y, EffectFade]
const current = computed(() => itemsToUse.value?.[active.value] ?? { title: '' })

const headerBadge = computed(() => (typeof props.badge === 'string' ? props.badge.trim() : 'Переваги навчання'))
const headerTitle = computed(() => (typeof props.title === 'string' && props.title.trim() !== '' ? props.title.trim() : 'Вчися ефективно — у зручному для тебе форматі'))
const headerSubtitle = computed(() => (typeof props.subtitle === 'string' && props.subtitle.trim() !== '' ? props.subtitle.trim() : ''))

const cta = computed(() => {
  const text = typeof props.cta?.text === 'string' && props.cta.text.trim() !== '' ? props.cta.text.trim() : 'Записатися'
  const href = typeof props.cta?.href === 'string' && props.cta.href.trim() !== '' ? props.cta.href.trim() : '#contact'
  return { text, href }
})

/* Керування */
function onSwiper(sw: SwiperType) {
  swiperRef.value = sw
  // невеликий “пуш” на випадок, коли DOM підвантажився пізніше
  try { /* @ts-ignore */ sw.navigation?.update?.() } catch {}
}
function goTo(idx: number) {
  if (!swiperRef.value || itemsToUse.value.length === 0) return
  const total = itemsToUse.value.length
  const t = ((idx % total) + total) % total
  active.value = t
  swiperRef.value.params.loop ? swiperRef.value.slideToLoop(t) : swiperRef.value.slideTo(t)
}
function onSlideChange(sw: SwiperType) {
  active.value = sw.realIndex
  revealPhoto(sw.realIndex)
  staggerBullets()
}

/* Анімації */
function revealPhoto(idx: number) {
  const media = photoMasks.value[idx]           // .adv-media
  if (!media) return

  // забрати будь-який clip-path, якщо лишився
  media.style.removeProperty('clip-path')
  ;(media as any).style?.removeProperty?.('clipPath')

  const figure = media.closest('.adv-figure') as HTMLElement
  if (!figure) return

  // старт: завіса закрита (—curtain = 1), фото трохи збільшене і прозоре
  figure.style.setProperty('--curtain', '1')
  gsap.fromTo(
    media,
    { opacity: 0, scale: 1.03, yPercent: -2, filter: 'blur(6px)' },
    { opacity: 1, scale: 1, yPercent: 0, filter: 'blur(0px)', duration: 0.6, ease: 'power2.out' }
  )

  // «завіса» з’їжджає вгору (вайп), НЕ обрізаючи саме зображення
  gsap.to(figure, { duration: 0.6, ease: 'power2.out', '--curtain': 0 })
}

function staggerBullets() {
  if (!bulletsEl.value) return
  gsap.fromTo(bulletsEl.value.children, { y: 10, opacity: 0 }, { y: 0, opacity: 1, duration: 0.35, ease: 'power2.out', stagger: 0.06 })
}
function introRevealHeader() {
  if (!headerRef.value) return
  gsap.from(headerRef.value.children, { y: 16, opacity: 0, duration: 0.6, ease: 'power2.out', stagger: 0.06 })
}
function blobsParallax() {
  if (!root.value || !blobL.value || !blobR.value) return
  gsap.timeline({ scrollTrigger: { trigger: root.value, start: 'top bottom', end: 'bottom top', scrub: true } })
    .to(blobL.value, { y: -40, x: 20, ease: 'none' }, 0)
    .to(blobR.value, { y: 40, x: -20, ease: 'none' }, 0)
}

onMounted(() => {
  introRevealHeader()
  blobsParallax()
  revealPhoto(active.value)
  staggerBullets()
})

onBeforeUnmount(() => { ScrollTrigger.getAll().forEach((st) => st.kill()) })
</script>

<style scoped>
/* tabs — скрол без смуги */
.tabs-scroll { -ms-overflow-style: none; scrollbar-width: none; }
.tabs-scroll::-webkit-scrollbar { display: none; }

/* =========================
   Swiper: усі фони прозорі
   ========================= */
:deep(.adv-swiper),
:deep(.adv-swiper .swiper),
:deep(.adv-swiper .swiper-wrapper),
:deep(.adv-swiper .swiper-slide) {
  background: transparent !important;
}

/* Фігура слайду: обовʼязково overflow-hidden + радіуси, щоб нічого не «вилазило» */
.slide-figure { background: transparent; }

/* Контейнер зображення:
   - object-fit: cover на <img> (див. MDN)
   - легка маска по краях, щоб приховати можливі «шви» за радіусами (CSS mask-image)
*/
.media-mask {
  position: relative;
  overflow: hidden;
  border-radius: 1rem; /* 16px — узгоджено з rounded-2xl */
  /* Edge-fade: чорне = видно, прозоре = приховано */
  -webkit-mask-image: radial-gradient(130% 130% at 50% 50%, #000 75%, rgba(0,0,0,0.0) 105%);
          mask-image: radial-gradient(130% 130% at 50% 50%, #000 75%, rgba(0,0,0,0.0) 105%);
}
.media-mask > img {
  display: block;
  width: 100%;
  height: 100%;
  object-fit: cover; /* перекриває весь контейнер */
}

/* =========================
   Стрілки (вбудована навігація)
   ========================= */
:deep(.swiper-button-prev),
:deep(.swiper-button-next) {
  --size: 46px;
  width: var(--size);
  height: var(--size);
  border-radius: 9999px;
  display: grid;
  place-items: center;
  color: #fff;
  background: linear-gradient(135deg, #0C7C78, #0FA6A0);
  box-shadow: 0 10px 30px rgba(12,124,120,0.28);
  border: 1px solid rgba(15,166,160,0.35);
  backdrop-filter: saturate(120%) blur(4px);
  transition: transform .18s ease, filter .18s ease, box-shadow .18s ease;
  z-index: 5;
}
:deep(.swiper-button-prev svg),
:deep(.swiper-button-next svg) {
  width: 60%;
  /* height: var(--size); */
  
}
:deep(.swiper-button-prev:hover),
:deep(.swiper-button-next:hover) {
  transform: translateY(-1px);
  filter: brightness(1.06);
  box-shadow: 0 14px 36px rgba(12,124,120,0.32);
}
/* Іконки стрілок (стандартні ::after) */
:deep(.swiper-button-prev::after),
:deep(.swiper-button-next::after) {
  font-size: 16px;
  font-weight: 800;
}
/* Краще розміщення всередині контейнера з радіусом */
:deep(.swiper-button-prev), :deep(.swiper-button-next) {
  top: 50%;
  transform: translateY(-50%);
}

/* Пагінація */
:deep(.swiper-pagination-bullet) {
  background: #0fa6a0;
  opacity: 0.35;
}
:deep(.swiper-pagination-bullet-active) {
  opacity: 1;
  background: linear-gradient(135deg, #0C7C78, #0FA6A0);
}
:deep(.adv-swiper){
  --adv-radius: 16px;           /* закруглення слайдера */
  --adv-arrow-size: 36px;       /* розмір стрілок */
  --adv-arrow-offset: 12px;     /* відступ від краю */
  border-radius: var(--adv-radius);
}

/* фікс висоти/розтяжки слайдів */
:deep(.adv-swiper .swiper-wrapper){ align-items: stretch; }
.adv-slide{ height:100%; }
.adv-figure{
  position:relative;
  height:100%;
  border-radius: var(--adv-radius);   /* той самий радіус на фігурі */
  overflow: hidden;                   /* нічого не «вилазить» у кутах */
  background: transparent;
}

/* медіа як фон: ніяких білих щілин при будь-якій пропорції */
.adv-media{
  position:absolute; inset:0;
  background-position:center;
  background-repeat:no-repeat;
  background-size:cover;              /* аналог object-fit:cover для фону */
  display:block;
}

/* декоративна пляма (можна вимкнути) */
.adv-glow{
  position:absolute; right:1rem; bottom:1rem;
  width:2.5rem; height:2.5rem; border-radius:9999px;
  background: rgba(217, 70, 239, 0.55);
  filter: blur(8px);
  pointer-events:none;
}

/* ---- Стрілки Swiper (вбудовані) ---- */
:deep(.adv-swiper .swiper-button-prev),
:deep(.adv-swiper .swiper-button-next){
  width: var(--adv-arrow-size);
  height: var(--adv-arrow-size);
  top: 50%; transform: translateY(-50%);
  border-radius: 9999px;
  display:grid; place-items:center;
  background: linear-gradient(135deg,#0C7C78,#0FA6A0);
  color:#fff;
  box-shadow: 0 10px 26px rgba(12,124,120,.25);
  outline: none;
}
:deep(.adv-swiper .swiper-button-prev){ left: var(--adv-arrow-offset); }
:deep(.adv-swiper .swiper-button-next){ right: var(--adv-arrow-offset); }

:deep(.adv-swiper .swiper-button-prev:hover),
:deep(.adv-swiper .swiper-button-next:hover){ filter:brightness(1.06); }
:deep(.adv-swiper .swiper-button-prev:after),
:deep(.adv-swiper .swiper-button-next:after){
  font-size: 14px; font-weight: 700;
}

/* пагінація під стиль теми */
:deep(.adv-swiper .swiper-pagination-bullet){ background:#0fa6a0; opacity:.35; }
:deep(.adv-swiper .swiper-pagination-bullet-active){
  opacity:1; background:linear-gradient(135deg,#0C7C78,#0FA6A0);
}
.adv-media { clip-path: none !important; }

/* завіса для вайпа: не рамка, не обрізання, просто накладка */
.adv-figure { --curtain: 0; position: relative; }
.adv-figure::after {
  content: "";
  position: absolute; inset: 0;
  background: linear-gradient(to bottom, rgba(255,255,255,0.95), rgba(255,255,255,0));
  transform-origin: top;
  transform: scaleY(var(--curtain)); /* 1 → 0 */
  pointer-events: none;
}
/* невеликий "пояс" для мобайлу, щоб фото не «дихали» */
@media (max-width: 767px){
  :deep(.adv-swiper){ --adv-radius: 14px; --adv-arrow-size: 32px; --adv-arrow-offset: 8px; }
}
@media (max-width: 767px){
  /* щоб слайдер не стискався до 0 висоти */
  .adv-slide{ min-height: 220px; }          /* можна 200–260px */
  .adv-figure{ height:auto; aspect-ratio:16/9; border-radius:14px; }
  :deep(.adv-swiper){ --adv-radius:14px; --adv-arrow-size:32px; --adv-arrow-offset:8px; }
}

#advantages-split { overflow-x: hidden; }

/* десктоп/планшет: стабільна висота картки зі слайдом */
@media (min-width: 768px){
  .adv-figure{
    height: auto;
    min-height: 420px;                /* підбери 380–480px за дизайном */
    border-radius: var(--adv-radius,16px);
  }
}

/* мобільні: картинка завжди влазить у в'юпорт */
@media (max-width: 767px){
  .adv-figure{
    height: auto;
    aspect-ratio: 16 / 9;             /* стабільна пропорція */
    max-height: 70vh;                  /* не вище екрана */
    border-radius: 14px;
  }
  .adv-slide{ min-height: auto; }      /* забираємо штучні мін-висоти */
  /* таби не обрізаються і переносяться */
  .tabs-scroll{
    flex-wrap: wrap;
    overflow-x: visible;
    gap: .5rem;
  }
  .tabs-scroll > button{ white-space: normal; }
}

</style>
