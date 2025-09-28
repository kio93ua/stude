<template>
  <section ref="root" id="about" class="bg-white py-16">
    <div class="mx-auto grid max-w-6xl gap-12 px-6 md:grid-cols-[1.1fr_0.9fr] md:items-center">
      <!-- Текст -->
      <div class="space-y-6" data-stagger="up">
        <h2 class="heading-2" data-item>{{ heading }}</h2>
        <p class="text-muted" data-item>{{ lead }}</p>
        <p class="text-muted" data-item>{{ body }}</p>

        <dl class="grid gap-4 text-sm text-muted sm:grid-cols-2" data-stagger="up">
          <div data-item>
            <dt class="font-semibold text-secondary">Сертифікати</dt>
            <dd>{{ certs }}</dd>
          </div>
          <div data-item>
            <dt class="font-semibold text-secondary">Формат роботи</dt>
            <dd>{{ formats }}</dd>
          </div>
          <div data-item>
            <dt class="font-semibold text-secondary">Додатково</dt>
            <dd>{{ extras }}</dd>
          </div>
          <div data-item>
            <dt class="font-semibold text-secondary">Мова викладання</dt>
            <dd>{{ langs }}</dd>
          </div>
        </dl>
      </div>

      <!-- Акцент-картка -->
      <div class="relative" data-reveal="right">
        <!-- “glow”-фон із м’якою пульсацією + паралакс -->
        <div
          ref="glow"
          class="absolute inset-0 -z-10 rounded-3xl bg-gradient-to-br from-brand-mint/50 via-accent/20 to-white blur-2xl">
        </div>

        <div ref="card" class="overflow-hidden rounded-3xl border border-white/40 bg-white/80 p-8 shadow-xl shadow-muted/40">
          <blockquote class="space-y-4">
            <p class="text-lg font-medium text-secondary">«{{ quote }}»</p>
            <footer class="text-sm font-semibold text-primary">{{ author }}</footer>
          </blockquote>
        </div>
      </div>
    </div>
  </section>
</template>

<script setup>
// без TS-типів (легше збирати). Якщо хочеш — можна додати lang="ts".
import { ref, onMounted, onBeforeUnmount } from 'vue'
import gsap from 'gsap'
import { ScrollTrigger } from 'gsap/ScrollTrigger'
import { gsapReveal } from '@/anim/gsap-reveal' // або відносний '../anim/gsap-reveal'

gsap.registerPlugin(ScrollTrigger)

const props = defineProps({
  heading: { type: String, default: 'Привіт! Я Марія Коваль' },
  lead:    { type: String, default: 'Закінчила КНУ ім. Шевченка, факультет іноземних мов. Сертифікована викладачка CELTA.' },
  body:    { type: String, default: 'Мій підхід — баланс структурованої граматики та інтерактивної практики з автентичними матеріалами.' },
  certs:   { type: String, default: 'CELTA, IELTS Academic 8.5' },
  formats: { type: String, default: 'Zoom, Google Meet, офлайн у Києві' },
  extras:  { type: String, default: 'Навчальні платформи, підтримка в Telegram' },
  langs:   { type: String, default: 'Українська / English only' },
  quote:   { type: String, default: 'Кожен може опанувати англійську, якщо навчання побудовано з любов’ю та увагою до особистості' },
  author:  { type: String, default: 'Марія Коваль' },
})

const root = ref(null)
const card = ref(null)
const glow = ref(null)

let mm = null

onMounted(() => {
  const el = root.value
  if (!el) return

  // 1) Каскадне розкриття текстів (реюз-хелпер)
  gsapReveal({ root: el })

  // 2) Плавна поява картки (pop) при вході в в’юпорт
  if (card.value) {
    gsap.fromTo(card.value,
      { opacity: 0, y: 28, scale: 0.98 },
      {
        opacity: 1, y: 0, scale: 1, duration: 0.7, ease: 'power2.out',
        scrollTrigger: { trigger: card.value, start: 'top 85%', toggleActions: 'play none none reverse' },
      }
    )
  }

  // 3) Паралакс і “дихання” для glow-фону + легкий паралакс картки
  mm = gsap.matchMedia()

  mm.add('(pointer:fine)', () => {
    if (glow.value) {
      // нескінченна м’яка пульсація
      gsap.to(glow.value, { x: 8, y: -12, duration: 6, yoyo: true, repeat: -1, ease: 'sine.inOut' })
      // паралакс на скрол
      gsap.to(glow.value, {
        y: -30, ease: 'none',
        scrollTrigger: { trigger: el, start: 'top bottom', end: 'bottom top', scrub: 0.5 }
      })
    }
    if (card.value) {
      gsap.to(card.value, {
        y: -18, ease: 'none',
        scrollTrigger: { trigger: el, start: 'top bottom', end: 'bottom top', scrub: 0.45 }
      })
    }
  })

  // Мобільні: спрощений паралакс (менші зміщення, без нескінченного “дихання”)
  mm.add('(pointer:coarse)', () => {
    if (glow.value) {
      gsap.to(glow.value, {
        y: -16, ease: 'none',
        scrollTrigger: { trigger: el, start: 'top bottom', end: 'bottom top', scrub: 0.4 }
      })
    }
    if (card.value) {
      gsap.to(card.value, {
        y: -10, ease: 'none',
        scrollTrigger: { trigger: el, start: 'top bottom', end: 'bottom top', scrub: 0.4 }
      })
    }
  })
})

onBeforeUnmount(() => {
  if (mm) mm.revert() // прибирає всі ScrollTrigger-и/твіни, створені в add()
  // запасне прибирання всіх тригерів у межах секції
  ScrollTrigger.getAll().forEach(st => {
    const trg = root.value
    if (trg && st.trigger && (st.trigger === trg || (st.trigger instanceof Element && trg.contains(st.trigger)))) {
      st.kill()
    }
  })
})
</script>

<style>
/* підказуємо браузеру, що тут часто змінюються transform/opacity */
[data-stagger],[data-item] { will-change: transform, opacity; }
</style>
