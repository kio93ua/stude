<template>
  <section
    ref="root"
    id="hero"
    class="overflow-hidden bg-gradient-to-br from-brand-mint via-white to-brand-aqua/10"
  >
    <div class="mx-auto grid max-w-6xl gap-10 px-6 pb-16 pt-20 md:grid-cols-2 md:items-center">
      <!-- Ліва колонка -->
      <div ref="leftCol" class="space-y-6">
        <span v-if="badge" class="badge-muted">{{ badge }}</span>
        <component :is="headingTag" class="heading-1">{{ title }}</component>
        <p class="text-lg text-muted">{{ subtitle }}</p>

        <div class="flex flex-col gap-3 sm:flex-row">
          <a v-if="primary?.href && primary?.text" :href="primary.href" class="btn-primary">
            {{ primary.text }}
          </a>
          <a v-if="secondary?.href && secondary?.text" :href="secondary.href" class="btn-outline">
            {{ secondary.text }}
          </a>
        </div>
      </div>

      <!-- Права колонка -->
      <div class="relative">
        <div class="absolute -left-10 top-10 hidden h-24 w-24 rounded-full bg-muted/60 blur-3xl md:block"></div>
        <div class="absolute -right-8 bottom-4 hidden h-20 w-20 rounded-full bg-accent/30 blur-2xl md:block"></div>

        <div ref="card" class="relative rounded-3xl bg-white p-6 shadow-xl shadow-muted/40">
          <div class="space-y-4">
            <!-- Картинка (опційно) з фіксованим aspect-ratio -->
            <div v-if="hasImage" class="overflow-hidden rounded-2xl">
              <div class="relative w-full" :style="aspectBoxStyle">
                <img
                  class="absolute inset-0 h-full w-full object-cover"
                  :src="imageUrl"
                  :alt="imageAltComputed"
                  :width="imageWidth || undefined"
                  :height="imageHeight || undefined"
                  loading="lazy"
                  decoding="async"
                />
              </div>
            </div>

            <h2 class="text-lg font-semibold text-secondary">{{ listTitle }}</h2>

            <!-- Якщо є пункти — показуємо їх, інакше короткий хінт -->
            <ul v-if="visibleBullets.length" ref="bulletsEl" class="space-y-3 text-sm text-muted">
              <li v-for="(item, i) in visibleBullets" :key="i" class="flex items-start gap-3">
                <span class="mt-1 h-2.5 w-2.5 shrink-0 rounded-full bg-primary"></span>
                <span>{{ item }}</span>
              </li>
              <slot name="extra"></slot>
            </ul>
            <p v-else class="text-sm text-muted/60">(Налаштуйте «Список переваг» у адмінці)</p>
          </div>
        </div>
      </div>
    </div>
  </section>
</template>

<script setup>
import { ref, onMounted, onBeforeUnmount, computed } from 'vue'
import gsap from 'gsap'

const props = defineProps({
  headingTag: { type: String, default: 'h1' },
  badge:      { type: String, default: 'Індивідуальні заняття з англійської' },
  title:      { type: String, default: 'Допоможу заговорити англійською впевнено вже за 3 місяці' },
  subtitle:   { type: String, default: 'Я — репетитор з 8-річним досвідом підготовки до IELTS, розмовної практики та бізнес-англійської. Працюю з підлітками та дорослими, комбіную сучасні матеріали та живе спілкування.' },
  listTitle:  { type: String, default: 'Що ви отримаєте' },
  bullets:    { type: Array,  default: () => [] },

  primary:   { type: Object, default: () => ({ text: 'Запис на пробний урок', href: '#contact' }) },
  secondary: { type: Object, default: () => ({ text: 'Дивитися програми', href: '#services' }) },

  imageUrl:   { type: String, default: '' },
  imageAlt:   { type: String, default: '' },
  imageWidth: { type: [Number, String], default: null },
  imageHeight:{ type: [Number, String], default: null },
})

const root = ref(null)
const leftCol = ref(null)
const card = ref(null)
const bulletsEl = ref(null)

const hasImage = computed(() => !!props.imageUrl)
const visibleBullets = computed(() =>
  Array.isArray(props.bullets)
    ? props.bullets.map(v => (typeof v === 'string' ? v.trim() : '')).filter(Boolean)
    : []
)

const aspectBoxStyle = computed(() => {
  const w = Number(props.imageWidth)
  const h = Number(props.imageHeight)
  return (w > 0 && h > 0)
    ? { paddingBottom: `${(h / w) * 100}%` }
    : { paddingBottom: '56.25%' } // 16:9 за замовченням
})

const imageAltComputed = computed(() => props.imageAlt || props.title)

let ctx
onMounted(() => {
  const prefersReduce = window.matchMedia('(prefers-reduced-motion: reduce)').matches
  if (prefersReduce) return

  ctx = gsap.context(() => {
    gsap.set([leftCol.value, card.value], { autoAlpha: 0, y: 12 })

    const tl = gsap.timeline({ defaults: { duration: 0.5, ease: 'power2.out' } })
    tl.to(leftCol.value, { autoAlpha: 1, y: 0 })
      .to(card.value,    { autoAlpha: 1, y: 0 }, '-=0.2')

    if (bulletsEl.value) {
      tl.from(bulletsEl.value.querySelectorAll('li'), { opacity: 0, y: 8, stagger: 0.06, duration: 0.35 }, '-=0.2')
    }
  }, root)
})
onBeforeUnmount(() => { if (ctx) ctx.revert() })
</script>

<style scoped>
:where([ref="leftCol"], [ref="card"], li) { will-change: transform, opacity; }
.relative > .absolute { pointer-events: none; }
</style>
