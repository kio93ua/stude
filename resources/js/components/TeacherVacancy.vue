<!-- resources/js/components/TeacherVacancy.vue -->
<template>
  <section
    ref="root"
    class="relative overflow-hidden bg-gradient-to-br from-[#BFF3E2] via-white to-[#DDF9F2]"
    aria-labelledby="vacancy-title"
  >
    <!-- декоративні плями -->
    <div
      ref="blobL"
      aria-hidden="true"
      class="pointer-events-none absolute -top-24 -left-24 h-72 w-72 rounded-full blur-3xl"
      :style="{ background: 'linear-gradient(135deg, rgba(12,124,120,0.35), rgba(15,166,160,0.2))' }"
    />
    <div
      ref="blobR"
      aria-hidden="true"
      class="pointer-events-none absolute -bottom-28 -right-32 h-80 w-80 rounded-full blur-3xl"
      :style="{ background: 'linear-gradient(135deg, rgba(15,166,160,0.3), rgba(12,124,120,0.2))' }"
    />

    <div class="mx-auto max-w-7xl px-6 py-16 md:py-24">
      <!-- заголовок -->
      <header ref="headerRef" class="mx-auto mb-10 max-w-3xl text-center">
        <p class="inline-block rounded-2xl bg-white/70 px-4 py-1 font-semibold text-slate-700 ring-1 ring-slate-200 backdrop-blur">
          Вакансія
        </p>
        <h2 id="vacancy-title" class="mt-4 text-3xl font-bold tracking-tight text-slate-900 md:text-4xl">
          Запрошуємо вчителів англійської
        </h2>
        <p class="mt-3 text-slate-700 tracking-tight">
          Гнучкий графік, сучасні матеріали та дружня команда. Розвивайся разом із нами й впливай на результати студентів.
        </p>
      </header>

      <!-- сітка -->
      <div class="grid items-stretch gap-8 md:grid-cols-2">
        <!-- медіа-картка -->
        <aside class="order-1 md:order-none">
          <figure
            ref="mediaCard"
            class="relative h-full overflow-hidden rounded-2xl bg-white/80 ring-1 ring-slate-200 shadow-[0_12px_40px_rgba(0,0,0,0.08)]"
          >
            <!-- маска-ревіл: НЕ блокує кліки -->
            <div
              class="pointer-events-none absolute inset-0"
              :style="{ background: 'linear-gradient(to bottom, rgba(255,255,255,0.96), rgba(255,255,255,0))', transformOrigin: 'top', transform: `scaleY(${curtain})` }"
              aria-hidden="true"
            />
            <img
              :src="mediaUrl"
              alt="Ілюстрація викладача, який подає заявку"
              class="block h-full w-full object-cover"
              loading="lazy"
              decoding="async"
            />
          </figure>
        </aside>

        <!-- права колонка -->
        <div class="flex">
          <article
            ref="cardRef"
            class="relative flex h-full w-full min-h-[540px] flex-col justify-between rounded-2xl border border-slate-200/80 bg-white/80 p-6 shadow-[0_8px_30px_rgba(0,0,0,0.06)] backdrop-blur md:min-h-[560px] md:p-8"
          >
            <div>
              <p
                ref="badgeRef"
                class="mb-3 inline-flex items-center gap-2 rounded-full bg-teal-50 px-3 py-1 text-xs font-semibold text-teal-700 ring-1 ring-teal-200"
              >
                Чому з нами класно
              </p>

              <h3 ref="titleRef" class="text-2xl font-bold tracking-tight text-slate-900 md:text-3xl">
                Рости разом із школою — ми підтримуємо ініціативу
              </h3>

              <p ref="leadRef" class="mt-3 text-slate-700">
                Дамо свободу у методиці, сучасні матеріали та стабільний потік вмотивованих студентів. Ви зосереджуєтесь на викладанні — ми закриваємо операційку.
              </p>

              <!-- список з «пульсуючими» точками -->
              <ul ref="listRef" class="mt-6 grid gap-3">
                <li class="flex items-start gap-3">
                  <span class="dot-pulse mt-1"></span>
                  <span class="text-slate-800">Гнучкий графік: обирайте слоти під себе</span>
                </li>
                <li class="flex items-start gap-3">
                  <span class="dot-pulse mt-1"></span>
                  <span class="text-slate-800">Матеріали, планування та тім-саппорт (перевірка, клуби)</span>
                </li>
                <li class="flex items-start gap-3">
                  <span class="dot-pulse mt-1"></span>
                  <span class="text-slate-800">Прозора оплата та бонуси за результати студентів</span>
                </li>
              </ul>

              <div ref="chipsRef" class="mt-6 flex flex-wrap items-center gap-2">
                <span class="chip">Speaking Clubs</span>
                <span class="chip">IELTS/Business</span>
                <span class="chip">Малі групи / 1-на-1</span>
              </div>
            </div>

            <!-- CTA -->
            <div class="mt-7">
              <a
                ref="ctaRef"
                :href="formUrl"
                target="_blank"
                rel="noopener"
                class="relative z-10 inline-flex w-full items-center justify-center rounded-2xl bg-gradient-to-r from-[#0C7C78] to-[#0FA6A0] px-5 py-3 font-semibold text-white shadow-sm transition will-change-transform hover:-translate-y-0.5 hover:shadow-lg focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-[#0FA6A0] focus-visible:ring-offset-2"
              >
                Заповнити анкету
              </a>
              <p class="mt-3 text-xs text-slate-500">
                Відкриється безпечна форма Google. Заповнення триває 2–3 хвилини.
              </p>
            </div>

            <!-- декоративний глянець (не перекриває події) -->
            <div
              class="pointer-events-none absolute -top-24 -right-16 h-64 w-64 rotate-12 rounded-full bg-gradient-to-br from-white/40 to-transparent blur-3xl"
              aria-hidden="true"
            />
          </article>
        </div>
      </div>
    </div>
  </section>
</template>

<script setup lang="ts">
import { ref, computed, onMounted } from 'vue'
import gsap from 'gsap'
import { ScrollTrigger } from 'gsap/ScrollTrigger'
gsap.registerPlugin(ScrollTrigger)

const props = defineProps<{
  mediaSrc?: string   // шлях у /public, напр.: "/images/teacher-apply.png"
  formUrl?: string    // лінк на Google Form
}>()

const mediaUrl = computed(() => props.mediaSrc || '/images/teacher-apply.png')
const formUrl  = computed(() =>
  props.formUrl ||
  'https://docs.google.com/forms/d/e/1FAIpQLSew8oe-A0p3wS7omGT_u3h9ts04egW_Mr0SfIgYzXn8tQUekA/viewform?usp=header'
)

const root = ref<HTMLElement | null>(null)
const headerRef = ref<HTMLElement | null>(null)
const mediaCard = ref<HTMLElement | null>(null)
const cardRef   = ref<HTMLElement | null>(null)
const badgeRef  = ref<HTMLElement | null>(null)
const titleRef  = ref<HTMLElement | null>(null)
const leadRef   = ref<HTMLElement | null>(null)
const listRef   = ref<HTMLElement | null>(null)
const chipsRef  = ref<HTMLElement | null>(null)
const ctaRef    = ref<HTMLElement | null>(null)
const blobL     = ref<HTMLElement | null>(null)
const blobR     = ref<HTMLElement | null>(null)
const curtain   = ref(1)

onMounted(() => {
  // анімація заголовку секції
  if (headerRef.value) {
    gsap.from(headerRef.value.children, {
      y: 18,
      opacity: 0,
      duration: 0.6,
      ease: 'power2.out',
      stagger: 0.06,
      immediateRender: false // щоб коректно програлося в поєднанні з ScrollTrigger, якщо додасте його пізніше. :contentReference[oaicite:0]{index=0}
    })
  }

  // м’який ревіл для зображення + легкий «поп»
  if (mediaCard.value) {
    curtain.value = 1
    gsap.to(curtain, { value: 0, duration: 0.7, ease: 'power2.out' })
    gsap.fromTo(
      mediaCard.value,
      { y: 10, opacity: 0.9, scale: 1.02 },
      { y: 0, opacity: 1, scale: 1, duration: 0.7, ease: 'power2.out', delay: 0.05 }
    )
  }

  // права колонка — каскадний вхід елементів при скролі
  if (cardRef.value) {
    const tl = gsap.timeline({
      defaults: { ease: 'power2.out', immediateRender: false },
      scrollTrigger: { trigger: cardRef.value!, start: 'top 80%', once: true } // базова схема ScrollTrigger. :contentReference[oaicite:1]{index=1}
    })

    tl.from([badgeRef.value, titleRef.value, leadRef.value], {
      y: 14, opacity: 0, duration: 0.6, stagger: 0.08
    })
    .from(listRef.value?.children ?? [], {
      y: 10, opacity: 0, duration: 0.45, stagger: 0.06
    }, '-=0.2')
    .from(chipsRef.value?.children ?? [], {
      y: 8, opacity: 0, duration: 0.4, stagger: 0.05
    }, '-=0.25')
    .from(ctaRef.value, {
      y: 10, opacity: 0, duration: 0.5
    }, '-=0.15')
  }

  // плавний паралакс плям
  if (root.value && blobL.value && blobR.value) {
    gsap.timeline({
      scrollTrigger: { trigger: root.value, start: 'top bottom', end: 'bottom top', scrub: true }
    })
    .to(blobL.value, { x: 30, y: -40, ease: 'none' }, 0)
    .to(blobR.value, { x: -40, y: 50, ease: 'none' }, 0)
  }
})
</script>

<style scoped>
/* бейджі */
.chip {
  @apply rounded-full bg-slate-50 px-3 py-1 text-xs font-medium text-slate-700 ring-1 ring-slate-200;
}

/* пульсуюча зелена точка (два кола: м’яка хвиля + ядро) */
.dot-pulse {
  @apply relative inline-block;
  width: 10px;
  height: 10px;
}
.dot-pulse::before {
  content: "";
  @apply absolute inset-0 rounded-full bg-teal-500/80;
}
.dot-pulse::after {
  content: "";
  @apply absolute inset-0 rounded-full bg-teal-500/30;
  animation: pulse 1.8s cubic-bezier(0.2, 0.7, 0.3, 1) infinite;
}

@keyframes pulse {
  0% { transform: scale(1); opacity: 0.7; }
  70% { transform: scale(2.2); opacity: 0; }
  100% { transform: scale(2.2); opacity: 0; }
}
</style>
