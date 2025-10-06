<template>
  <section ref="root" id="hero" class="section-surface relative overflow-hidden py-16 sm:py-24">
    <!-- декоративний шар всередині секції (не торкається країв, без швів) -->
    
    <div
      aria-hidden
      class="pointer-events-none absolute inset-[clamp(12px,3vw,36px)] -z-10 rounded-[2rem]"
      style="
        isolation:isolate;
        background-image:
          radial-gradient(24rem 24rem at 8% 10%, rgba(12,124,120,.16), transparent 60%),
          radial-gradient(20rem 20rem at 90% 88%, rgba(15,166,160,.14), transparent 62%);
        background-repeat:no-repeat;
      "
    ></div>

    <div class="mx-auto grid max-w-6xl gap-10 px-6 md:grid-cols-2 md:items-center">
      <!-- Ліва колонка -->
      <div ref="leftCol" class="space-y-6">
        <span v-if="badge" ref="badgeEl" class="badge-muted font-display">{{ badge }}</span>
        <component ref="titleEl" :is="headingTag" class="heading-1 font-display tracking-tight text-secondary">
          {{ title }}
        </component>
        <p ref="subtitleEl" class="text-lg text-secondary/85 font-sans">
          {{ subtitle }}
        </p>

        <div ref="ctaEl" class="flex flex-col gap-3 sm:flex-row">
          <a v-if="primary?.href && primary?.text" :href="primary.href" class="btn-primary font-display">{{ primary.text }}</a>
          <a v-if="secondary?.href && secondary?.text" :href="secondary.href" class="btn-outline font-display">{{ secondary.text }}</a>
        </div>
      </div>

      <!-- Права колонка -->
      <div class="relative">
        <!-- верхній ікон-блок (залишаємо, але безпечний для фону) -->
        <div ref="iconTop" class="pointer-events-none absolute -top-12 -left-14 md:-top-14 md:-left-16 z-10" :style="topIconStyle">
          <picture v-if="top1x">
            <source type="image/webp" :srcset="`${top1x} 1x, ${top2x} 2x`" />
            <img :src="top1x" :alt="topIconAlt" class="block drop-shadow-md will-change-transform"
                 :width="topIconW" :height="topIconH" loading="lazy" decoding="async" />
          </picture>
          <slot name="icon-top" />
        </div>

        <div ref="card" class="relative rounded-3xl bg-white p-6 shadow-xl shadow-muted/40">
          <div class="space-y-4">
            <div v-if="hasImage" class="overflow-hidden rounded-2xl">
              <div class="relative w-full" :style="aspectBoxStyle">
                <img
                  class="absolute inset-0 h-full w-full object-cover block"
                  :style="{ objectPosition: imageObjectPosition }"
                  :src="imageUrl"
                  :alt="imageAltComputed"
                  :width="imageWidth || undefined"
                  :height="imageHeight || undefined"
                  loading="eager" fetchpriority="high" decoding="async"
                />
              </div>
            </div>

            <h2 class="text-lg font-semibold text-secondary font-display">{{ listTitle }}</h2>

            <ul v-if="visibleBullets.length" ref="bulletsEl" class="space-y-3 text-sm text-secondary/85 font-sans">
              <li v-for="(item, i) in visibleBullets" :key="i" class="flex items-start gap-3">
                <span class="mt-1 h-2.5 w-2.5 shrink-0 rounded-full bg-teal-600"></span>
                <span>{{ item }}</span>
              </li>
              <slot name="extra"></slot>
            </ul>
            <p v-else class="text-sm text-secondary/60 font-sans">(Налаштуйте «Список переваг» у адмінці)</p>
          </div>
        </div>

        <!-- нижній ікон-блок -->
        <div ref="iconBottom" class="pointer-events-none absolute -bottom-14 -right-14 md:-bottom-16 md:-right-20 z-10" :style="bottomIconStyle">
          <picture v-if="bottom1x">
            <source type="image/webp" :srcset="`${bottom1x} 1x, ${bottom2x} 2x`" />
            <img :src="bottom1x" :alt="bottomIconAlt" class="block drop-shadow-md will-change-transform"
                 :width="bottomIconW" :height="bottomIconH" loading="lazy" decoding="async" />
          </picture>
          <slot name="icon-bottom" />
        </div>
      </div>
    </div>
  </section>
</template>


<script setup>
import { ref, onMounted, onBeforeUnmount, computed, nextTick } from 'vue'
import gsap from 'gsap'

defineOptions({ name: 'HeroSection' })

const props = defineProps({
  headingTag: { type: String, default: 'h1' },
  badge:      { type: String, default: 'Індивідуальні заняття з англійської' },
  title:      { type: String, default: 'Допоможу заговорити англійською впевнено вже за 3 місяці' },
  subtitle:   { type: String, default: 'Я — репетитор з 8-річним досвідом підготовки до IELTS, розмовної практики та бізнес-англійської. Працюю з підлітками та дорослими, комбіную сучасні матеріали та живе спілкування.' },
  listTitle:  { type: String, default: 'Що ви отримаєте' },
  bullets:    { type: Array,  default: () => [] },
  primary:    { type: Object, default: () => ({ text: 'Запис на пробний урок', href: '#contact' }) },
  secondary:  { type: Object, default: () => ({ text: 'Дивитися програми', href: '#services' }) },
  imageUrl:   { type: String, default: '' },
  imageAlt:   { type: String, default: '' },
  imageWidth: { type: [Number, String], default: null },
  imageHeight:{ type: [Number, String], default: null },
  imageAspect:     { type: String, default: '4:3' },
  imageObjectPos:  { type: String, default: '50% 45%' },
  topIconUrl:     { type: String, default: '' },
  topIcon1xUrl:   { type: String, default: '' },
  topIcon2xUrl:   { type: String, default: '' },
  topIconAlt:     { type: String, default: '' },
  topIconW:       { type: [Number, String], default: 230 },
  topIconH:       { type: [Number, String], default: 230 },
  bottomIconUrl:  { type: String, default: '' },
  bottomIcon1xUrl:{ type: String, default: '' },
  bottomIcon2xUrl:{ type: String, default: '' },
  bottomIconAlt:  { type: String, default: '' },
  bottomIconW:    { type: [Number, String], default: 230 },
  bottomIconH:    { type: [Number, String], default: 230 },
  iconSwing: { type: Number, default: 12 },
  iconTilt:  { type: Number, default: 2 },
  stepDelay:       { type: Number, default: 0.5 },
  bulletsStagger:  { type: Number, default: 1.0 },
})

const leftCol = ref(null)
const card = ref(null)
const bulletsEl = ref(null)
const iconTop = ref(null)
const iconBottom = ref(null)
const badgeEl = ref(null)
const titleEl = ref(null)
const subtitleEl = ref(null)
const ctaEl = ref(null)

const hasImage = computed(() => !!props.imageUrl)
const visibleBullets = computed(() =>
  Array.isArray(props.bullets) ? props.bullets.map(v => (typeof v === 'string' ? v.trim() : '')).filter(Boolean) : []
)

const aspectBoxStyle = computed(() => {
  const w = Number(props.imageWidth), h = Number(props.imageHeight)
  if (w > 0 && h > 0) return { paddingBottom: `${(h / w) * 100}%` }
  const [aw, ah] = (props.imageAspect || '4:3').split(':').map(n => Number(n) || 0)
  const ratio = aw > 0 && ah > 0 ? ah / aw : 3 / 4
  return { paddingBottom: `${ratio * 100}%` }
})

const imageObjectPosition = computed(() => props.imageObjectPos)
const topIconStyle = computed(() => ({ width: `${props.topIconW}px`, height: `${props.topIconH}px` }))
const bottomIconStyle = computed(() => ({ width: `${props.bottomIconW}px`, height: `${props.bottomIconH}px` }))
const imageAltComputed = computed(() => props.imageAlt || props.title)

const top1x = computed(() => props.topIcon1xUrl || props.topIconUrl || '')
const top2x = computed(() => props.topIcon2xUrl || props.topIcon1xUrl || props.topIconUrl || '')
const bottom1x = computed(() => props.bottomIcon1xUrl || props.bottomIconUrl || '')
const bottom2x = computed(() => props.bottomIcon2xUrl || props.bottomIcon1xUrl || props.bottomIconUrl || '')

let tl, topTl, bottomTl, iconsIntro

onMounted(async () => {
  if (typeof window === 'undefined') return
  if (window.matchMedia?.('(prefers-reduced-motion: reduce)').matches) return
  await nextTick()

  gsap.set([badgeEl.value, titleEl.value, subtitleEl.value, ctaEl.value], { autoAlpha: 0, y: 12 })

  const items = bulletsEl.value?.querySelectorAll('li')
  if (items?.length) gsap.set(items, { opacity: 0, y: 8 })

  if (iconTop.value) gsap.set(iconTop.value, { autoAlpha: 0, y: -6 })
  if (iconBottom.value) gsap.set(iconBottom.value, { autoAlpha: 0, y: 6 })

  const d = props.stepDelay

  tl = gsap.timeline({ defaults: { ease: 'power2.out' } })
    .to(badgeEl.value,    { autoAlpha: 1, y: 0, duration: 0.45 }, d * 1)
    .to(titleEl.value,    { autoAlpha: 1, y: 0, duration: 0.55 }, d * 2)
    .to(subtitleEl.value, { autoAlpha: 1, y: 0, duration: 0.55 }, d * 3)
    .to(ctaEl.value,      { autoAlpha: 1, y: 0, duration: 0.45 }, d * 4)

  if (items?.length) {
    tl.to(items, { opacity: 1, y: 0, duration: 0.4, stagger: props.bulletsStagger }, d * 4.2)
  }

  iconsIntro = gsap.timeline()
  if (iconTop.value) iconsIntro.to(iconTop.value, { autoAlpha: 1, y: 0, duration: 0.35 }, 0.2)
  if (iconBottom.value) iconsIntro.to(iconBottom.value, { autoAlpha: 1, y: 0, duration: 0.35 }, 0.25)

  const swing = props.iconSwing, tilt = props.iconTilt
  if (iconTop.value) {
    topTl = gsap.to(iconTop.value, { x: swing, rotation: tilt, duration: 2.8, ease: 'sine.inOut', yoyo: true, repeat: -1, delay: 5 })
  }
  if (iconBottom.value) {
    bottomTl = gsap.to(iconBottom.value, { x: -swing, rotation: -tilt, duration: 3.1, ease: 'sine.inOut', yoyo: true, repeat: -1, delay: 5 })
  }
})

onBeforeUnmount(() => {
  [tl, topTl, bottomTl, iconsIntro].forEach(t => { try { t && t.kill() } catch {} })
  ;[badgeEl.value, titleEl.value, subtitleEl.value, ctaEl.value, iconTop.value, iconBottom.value].forEach(n => n && gsap.killTweensOf(n))
})
</script>

<style>
.reveal-left, .reveal-card, li { will-change: transform, opacity; }
.relative > .absolute { pointer-events: none; }
</style>
