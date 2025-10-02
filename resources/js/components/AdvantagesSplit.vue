<template>
  <section
    ref="root"
    id="advantages-split"
    class="relative overflow-hidden bg-gradient-to-br from-[#BFF3E2] via-white to-[#DDF9F2] py-16 md:py-24"
    aria-labelledby="advantages-heading"
  >
    <!-- м’які плями -->
    <div aria-hidden ref="blobL"
         class="pointer-events-none absolute -top-20 -left-24 h-80 w-80 rounded-full bg-[rgba(12,124,120,0.25)] blur-3xl will-change-transform" />
    <div aria-hidden ref="blobR"
         class="pointer-events-none absolute -bottom-24 -right-16 h-96 w-96 rounded-full bg-[rgba(15,166,160,0.2)] blur-3xl will-change-transform" />

    <div class="mx-auto max-w-7xl px-6">
      <header ref="headerRef" class="mx-auto mb-8 max-w-2xl text-center md:mb-12">
        <p class="inline-block rounded-2xl bg-white/70 px-4 py-1 font-semibold text-slate-700 ring-1 ring-slate-200 backdrop-blur">
          Переваги навчання
        </p>
        <h2 id="advantages-heading" class="mt-4 text-3xl font-bold tracking-tight text-slate-900 md:text-4xl">
          Вчися ефективно — у зручному для тебе форматі
        </h2>
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
              <h3 class="text-2xl font-bold tracking-tight text-slate-900">
                {{ current.title }}
              </h3>
              <p v-if="current.desc" class="mt-2 text-slate-600">
                {{ current.desc }}
              </p>
            </header>

            <ul v-if="current.bullets?.length" class="grid gap-2" ref="bulletsEl">
              <li v-for="(b, bi) in current.bullets" :key="`b-${active}-${bi}`" class="flex items-start gap-3">
                <span class="mt-1 inline-block h-2 w-2 rounded-full bg-gradient-to-r from-[#0C7C78] to-[#0FA6A0]" aria-hidden="true"></span>
                <span class="text-slate-700">{{ b }}</span>
              </li>
            </ul>

            <div v-if="current.icons?.length" class="mt-6 flex flex-wrap items-center gap-3">
              <img v-for="(ico, ii) in current.icons" :key="`ico-${active}-${ii}`" :src="ico" alt=""
                   class="h-8 w-8 object-contain opacity-90" loading="lazy" decoding="async" draggable="false" />
            </div>

            <div class="mt-6">
              <a href="#contact"
                 class="rounded-2xl bg-gradient-to-r from-[#0C7C78] to-[#0FA6A0] px-5 py-2 font-semibold text-white shadow-sm transition focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-[#0FA6A0] focus-visible:ring-offset-2">
                Записатися
              </a>
            </div>
          </article>
        </div>

        <!-- Праворуч: Swiper з ВБУДОВАНОЮ навігацією -->
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
    <!-- Замість <img> використовуємо блок-фон — 100% заповнення без білих країв -->
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
  title: string
  desc?: string
  bullets?: string[]
  image: { src: string; alt?: string }
  icons?: string[]
}
const props = withDefaults(defineProps<{ items?: Item[] }>(), { items: undefined })

/** 3 потрібні переваги (fallback) */
const fallbackItems: Item[] = [
  {
    title: 'Ігрові методи',
    desc: 'Інтерактиви, рольові сценарії та міні-ігри — мотивація росте, страх помилок зникає.',
    bullets: ['Щотижневі челенджі', 'Сценарії з реального життя', 'Веселі практики замість нудної теорії'],
    image: { src: '/images/adv/gamified.jpg', alt: 'Ігрові методи' },
  },
  {
    title: 'Сучасна програма вивчення',
    desc: 'Комунікативний підхід, мікрозвички та трек прогресу — чіткий результат щотижня.',
    bullets: ['Модульна структура', 'Практика > теорія', 'Персональні рекомендації'],
    image: { src: '/images/adv/modern.jpg', alt: 'Сучасна програма' },
  },
  {
    title: 'Задоволені учні',
    desc: 'Тепла дружня атмосфера й підтримка — легше говорити впевнено.',
    bullets: ['Малі групи або 1-на-1', 'Зворотний зв’язок щотижня', 'Клуби розмовної практики'],
    image: { src: '/images/adv/happy.jpg', alt: 'Задоволені учні' },
  },
]
const itemsToUse = computed(() => (props.items?.length ? props.items : fallbackItems))

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

/* Керування */
function onSwiper(sw: SwiperType) {
  swiperRef.value = sw
  // невеликий “пуш” на випадок, коли DOM підвантажився пізніше
  try { /* @ts-ignore */ sw.navigation?.update?.() } catch {}
}
function goTo(idx: number) {
  if (!swiperRef.value) return
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
