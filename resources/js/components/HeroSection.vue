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

      <!-- Права колонка (картка) -->
      <div class="relative">
        <!-- фонові «баббли» фіксованого розміру — не впливають на flow -->
        <div class="absolute -left-10 top-10 hidden h-24 w-24 rounded-full bg-muted/60 blur-3xl md:block"></div>
        <div class="absolute -right-8 bottom-4 hidden h-20 w-20 rounded-full bg-accent/30 blur-2xl md:block"></div>

        <div ref="card" class="relative rounded-3xl bg-white p-6 shadow-xl shadow-muted/40">
          <div class="space-y-4">
            <h2 class="text-lg font-semibold text-secondary">{{ listTitle }}</h2>
            <ul ref="bulletsEl" class="space-y-3 text-sm text-muted">
              <li v-for="(item, i) in bullets" :key="i" class="flex items-start gap-3">
                <span class="mt-1 h-2.5 w-2.5 shrink-0 rounded-full bg-primary"></span>
                <span>{{ item }}</span>
              </li>
              <slot name="extra"></slot>
            </ul>
          </div>
        </div>
      </div>
    </div>
  </section>
</template>

<script setup>
import { ref, onMounted, onBeforeUnmount } from 'vue'
import gsap from 'gsap'

const props = defineProps({
  headingTag: { type: String, default: 'h1' },
  badge:      { type: String, default: 'Індивідуальні заняття з англійської' },
  title:      { type: String, default: 'Допоможу заговорити англійською впевнено вже за 3 місяці' },
  subtitle:   { type: String, default: 'Я — репетитор з 8-річним досвідом підготовки до IELTS, розмовної практики та бізнес-англійської. Працюю з підлітками та дорослими, комбіную сучасні матеріали та живе спілкування.' },
  listTitle:  { type: String, default: 'Що ви отримаєте' },
  bullets:    {
    type: Array,
    default: () => ([
      'Онлайн та офлайн заняття у зручному графіку',
      'Персональний план під ваш рівень та цілі',
      'Цифрові матеріали, Д/З та регулярний фідбек',
    ])
  },
  primary:   { type: Object, default: () => ({ text: 'Запис на пробний урок', href: '#contact' }) },
  secondary: { type: Object, default: () => ({ text: 'Дивитися програми', href: '#services' }) },
})

const root = ref(null)
const leftCol = ref(null)
const card = ref(null)
const bulletsEl = ref(null)

let ctx

onMounted(() => {
  // Якщо користувач просить менше руху — показати все одразу без анімацій
  const prefersReduce = window.matchMedia('(prefers-reduced-motion: reduce)').matches
  if (prefersReduce) return

  ctx = gsap.context(() => {
    // Початкові стани (тільки opacity/transform -> без CLS)  :contentReference[oaicite:1]{index=1}
    gsap.set([leftCol.value, card.value], { autoAlpha: 0, y: 12 }) // 12px — ледь помітно

    // Вхідний таймлайн
    const tl = gsap.timeline({ defaults: { duration: 0.5, ease: 'power2.out' } })
    tl.to(leftCol.value, { autoAlpha: 1, y: 0 })
      .to(card.value,    { autoAlpha: 1, y: 0 }, '-=0.2')

    // Легка «поява» пунктів списку (без зсуву макета)  :contentReference[oaicite:2]{index=2}
    if (bulletsEl.value) {
      tl.from(
        bulletsEl.value.querySelectorAll('li'),
        { opacity: 0, y: 8, stagger: 0.06, duration: 0.35 },
        '-=0.2'
      )
    }
  }, root)
})

onBeforeUnmount(() => {
  if (ctx) ctx.revert()
})
</script>

<style scoped>
/* Підказка браузеру: елементи тільки фейдяться/зсуваються композитом */
:where([ref="leftCol"], [ref="card"], li) { will-change: transform, opacity; }

/* Запобігаємо небажаним подіям на декоративних «бабблах» */
.relative > .absolute { pointer-events: none; }
</style>
