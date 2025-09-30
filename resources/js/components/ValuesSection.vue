<template>
  <section id="values" class="bg-white py-16">
    <div class="mx-auto max-w-6xl px-6">
      <header class="mx-auto mb-10 max-w-3xl text-center">
        <p v-if="kicker" class="badge-accent mb-3 inline-block">{{ kicker }}</p>
        <h2 class="heading-2">{{ heading }}</h2>
        <p v-if="subheading" class="mt-2 text-muted/90">{{ subheading }}</p>
      </header>

      <div class="grid grid-cols-1 gap-6 md:grid-cols-6">
        <article
          v-for="(it, i) in cooked"
          :key="i"
          data-card
          class="relative overflow-hidden rounded-3xl border p-6 sm:p-7"
          :class="[it.spanClass, it.toneClass, it.shapeClass]"
        >
          <span v-if="it.accentNumber" class="chip-step">{{ it.accentNumber }}</span>

          <div v-if="i === 0" aria-hidden="true" class="pointer-events-none absolute -right-10 -top-10 h-32 w-32 rounded-full bg-accent/20 blur-2xl"></div>

          <div v-if="i === 1" aria-hidden="true" class="pointer-events-none absolute right-4 top-4 text-accent/70">
            <svg width="56" height="56" viewBox="0 0 56 56" fill="none">
              <circle cx="28" cy="28" r="26" stroke="currentColor" stroke-width="3"/>
              <circle cx="20" cy="22" r="3" fill="currentColor"/>
              <circle cx="36" cy="22" r="3" fill="currentColor"/>
              <path d="M18 34c3 4 8 6 10 6s7-2 10-6" stroke="currentColor" stroke-width="3" stroke-linecap="round"/>
            </svg>
          </div>

          <div class="relative z-10">
            <div class="mb-4 flex items-center gap-3">
              <img v-if="it.icon" :src="it.icon" alt="" class="h-10 w-10 object-contain" loading="lazy" decoding="async" />
              <h3 class="heading-3 m-0">{{ it.title }}</h3>
            </div>
            <p class="text-[#214E4E]">{{ it.text }}</p>
          </div>
        </article>
      </div>
    </div>
  </section>
</template>

<script setup>
import { computed, onMounted, nextTick, ref } from 'vue'
import gsap from 'gsap'

const props = defineProps({
  heading: { type: String, default: 'Наші цінності' },
  subheading: { type: String, default: '' },
  kicker: { type: String, default: '' },
  items: { type: Array, default: () => [] }
})

const presets = [
  { span: 4, tone: 'light',  shape: 'path'  },
  { span: 2, tone: 'dark',   shape: 'smile' },
  { span: 2, tone: 'soft',   shape: 'coin'  },
  { span: 2, tone: 'brand',  shape: 'puzzle'},
  { span: 2, tone: 'pastel', shape: 'note'  },
]

const spanClassMap = { 2: 'md:col-span-2', 3: 'md:col-span-3', 4: 'md:col-span-4', 6: 'md:col-span-6' }
const toneMap = {
  light:  'border-muted/50 bg-white',
  soft:   'border-muted/60 bg-[#F5FAF8]',
  dark:   'border-[#0C5258] bg-[#0E6973] text-white',
  brand:  'border-primary/40 bg-primary/5',
  pastel: 'border-muted/50 bg-[#EAF5F2]',
}
const shapeMap = {
  path:   'shape shape--path',
  smile:  'shape shape--smile',
  coin:   'shape shape--coin',
  puzzle: 'shape shape--puzzle',
  note:   'shape shape--note',
}

const fallback = [
  { title: 'Індивідуальний шлях', text: 'Програма під ваш рівень та цілі. Плавно, структуровано, з вимірюваними результатами.', span: 4, tone: 'light',  shape: 'path',   accent: true  },
  { title: 'Дружнє середовище',   text: 'Комфортні заняття й підтримка — легше говорити та не боятися помилок.',                 span: 2, tone: 'dark',   shape: 'smile',  accent: false },
  { title: 'Внутрішня мотивація', text: 'Прозорі цілі, міні-нагороди та звички, що тримають у тонусі.',                         span: 2, tone: 'soft',   shape: 'coin',   accent: false },
  { title: 'Ігровий формат',      text: 'Інтерактив, рольові ситуації та сценарії з реального життя.',                          span: 2, tone: 'brand',  shape: 'puzzle', accent: true  },
  { title: 'Мова як хобі',        text: 'Вбудовуємо англійську у ваш день: міні-звички, контент, челенджі.',                    span: 2, tone: 'pastel', shape: 'note',   accent: false },
]

const cooked = computed(() => {
  const src = props.items?.length ? props.items : fallback
  return src.map((x, i) => {
    const base = presets[i % presets.length]
    const span = Number(x.span ?? base.span)
    const tone = x.tone ?? base.tone
    const shape = x.shape ?? base.shape
    return {
      title: x.title ?? '',
      text: x.text ?? '',
      icon: x.icon ?? '',
      spanClass: spanClassMap[span] ?? 'md:col-span-3',
      toneClass: toneMap[tone] ?? toneMap.light,
      shapeClass: shapeMap[shape] ?? '',
      accentNumber: x.accent ? (i + 1) : null,
    }
  })
})

const root = ref(null)
onMounted(async () => {
  await nextTick()
  const cards = document.querySelectorAll('#values [data-card]')
  if (!cards.length) return
  gsap.from(cards, {
    opacity: 0,
    y: 18,
    duration: 0.45,
    ease: 'power2.out',
    stagger: 0.08
  })
})
</script>

<style scoped>
.shape { position: relative; overflow: hidden; }
.shape::after { content:""; position:absolute; inset:0; opacity:.08; pointer-events:none; }
.shape--path::after {
  background: radial-gradient(100% 100% at 0% 0%, #000 0%, transparent 60%),
              repeating-linear-gradient(135deg, #000 0 2px, transparent 2px 10px);
  mix-blend-mode: multiply;
  mask: radial-gradient(90% 90% at 30% 25%, #000 55%, transparent 56%) subtract, none;
}
.shape--smile::after {
  background:
    radial-gradient(40% 40% at 25% 35%, #000 0 40%, transparent 41%) 0 0/50% 50% no-repeat,
    radial-gradient(40% 40% at 75% 35%, #000 0 40%, transparent 41%) 100% 0/50% 50% no-repeat,
    radial-gradient(70% 70% at 50% 90%, #000 0 45%, transparent 46%) 50% 100%/60% 60% no-repeat;
}
.shape--coin::after {
  background: radial-gradient(circle at 50% 50%, #000 0 28%, transparent 29%),
              radial-gradient(circle at 35% 35%, #000 0 6%, transparent 7%),
              radial-gradient(circle at 65% 65%, #000 0 6%, transparent 7%);
}
.shape--puzzle::after {
  background:
    radial-gradient(closest-side, #000 0 38%, transparent 39%) 20% 20%/40% 40% no-repeat,
    radial-gradient(closest-side, #000 0 38%, transparent 39%) 80% 20%/40% 40% no-repeat,
    radial-gradient(closest-side, #000 0 38%, transparent 39%) 20% 80%/40% 40% no-repeat,
    radial-gradient(closest-side, #000 0 38%, transparent 39%) 80% 80%/40% 40% no-repeat;
}
.shape--note::after {
  background-image:
    linear-gradient(#000 1px, transparent 1px),
    linear-gradient(90deg, #000 1px, transparent 1px);
  background-size: 100% 28px, 28px 100%;
}
</style>
