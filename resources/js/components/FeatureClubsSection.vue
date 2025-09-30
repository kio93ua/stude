<template>
  <section
    ref="root"
    id="feature-clubs"
    class="relative overflow-hidden bg-gradient-to-br from-[#BFF3E2] via-white to-[#DDF9F2]"
    :style="cssVars"
  >
    <!-- Фонова дуга -->
    <svg class="pointer-events-none absolute z-0"
         :style="{ left:'var(--arc-left)', bottom:'var(--arc-bottom)', width:'var(--arc-size)', height:'var(--arc-size)' }"
         viewBox="0 0 600 600" fill="none" aria-hidden="true">
      <path d="M300,560 A260,260 0 1,1 559,301" stroke="#7B5BFF" stroke-opacity="0.42" stroke-width="120" stroke-linecap="round" />
    </svg>

    <div class="relative z-10 mx-auto grid max-w-[1200px] grid-cols-1 items-center gap-10 px-6 py-20 md:grid-cols-[1.05fr_0.95fr] md:py-24 lg:py-28">
      <!-- Ліва колонка -->
      <div class="relative z-30 space-y-6">
        <svg ref="burstLeft" class="absolute hidden md:block"
             :style="{ top:'var(--burstL-top)', left:'var(--burstL-left)', width:'var(--burstL-w)', height:'var(--burstL-w)', color:'var(--violet)'}"
             viewBox="0 0 48 48" fill="none" stroke="currentColor" stroke-width="4"><path stroke-linecap="round" d="M24 4v8M24 36v8M4 24h8M36 24h8M9 9l6 6M33 33l6 6M9 39l6-6M33 15l6-6"/></svg>

        <h2 ref="title" class="font-display text-[clamp(28px,4.2vw,48px)] leading-[1.05] font-extrabold text-[#08344A]">
          {{ heading }} <span class="block">{{ headingAccent }}</span>
        </h2>

        <p ref="subtitle" class="max-w-[44ch] text-[clamp(16px,1.6vw,18px)] text-[#215E73]">{{ subheading }}</p>

        <div class="pt-1.5">
          <a ref="ctaLeft" :href="leftCta.href"
             class="inline-flex items-center rounded-full px-6 py-3 font-display text-white transition hover:-translate-y-0.5"
             :style="{ background:'var(--violet)', boxShadow:'0 14px 26px rgba(123,91,255,.38)'}">
            {{ leftCta.text }}
          </a>
        </div>
      </div>

      <!-- Права колонка -->
      <div class="relative z-20 min-h-[360px] md:min-h-[420px]">
        <!-- хвиля/декор -->
        <svg ref="wave" class="absolute hidden md:block"
             :style="{ top:'var(--wave-top)', right:'var(--wave-right)', width:'var(--wave-w)', color:'var(--violet)' }"
             viewBox="0 0 220 60" fill="none"><path d="M6 44c24-28 48-28 72 0s48 28 72 0 48-28 64-14" stroke="currentColor" stroke-width="8" stroke-linecap="round"/></svg>

        <svg ref="starTeal" class="absolute hidden md:block"
             :style="{ top:'var(--starT-top)', right:'var(--starT-right)', width:'var(--starT-w)', height:'var(--starT-w)', color:'var(--teal)' }"
             viewBox="0 0 64 64" fill="currentColor"><path d="M32 8l8 24-8 24-8-24 8-24Z"/></svg>

        <svg ref="starYellow" class="absolute hidden md:block"
             :style="{ top:'var(--starY-top)', left:'var(--starY-left)', width:'var(--starY-w)', height:'var(--starY-w)', color:'var(--yellow)' }"
             viewBox="0 0 64 64" fill="currentColor"><path d="M32 8l8 24-8 24-8-24 8-24Z"/></svg>

        <svg ref="plus" class="absolute hidden md:block"
             :style="{ top:'var(--plus-top)', left:'var(--plus-left)', width:'var(--plus-w)', height:'var(--plus-w)', color:'var(--violet)' }"
             viewBox="0 0 24 24" fill="currentColor"><path d="M10 4h4v6h6v4h-6v6h-4v-6H4v-4h6V4z"/></svg>

        <svg ref="burstRight" class="absolute hidden md:block"
             :style="{ top:'var(--burstR-top)', right:'var(--burstR-right)', width:'var(--burstR-w)', height:'var(--burstR-w)', color:'var(--teal)' }"
             viewBox="0 0 48 48" fill="none" stroke="currentColor" stroke-width="4"><path stroke-linecap="round" d="M24 4v8M24 36v8M4 24h8M36 24h8M9 9l6 6M33 33l6 6M9 39l6-6M33 15l6-6"/></svg>

        <!-- ПУНКТИР: позиціюємо ПІКСЕЛЯМИ і можемо крутити -->
        <svg ref="dotted" class="absolute z-20"
             :style="{
               top: 'var(--dot-top-px)', left: 'var(--dot-left-px)',
               width: 'var(--dot-w-px)', height: 'var(--dot-h-px)',
               transform: 'rotate(' + cssVars['--dot-rot-deg'] + ')',
               color: 'rgba(22,179,167,.75)'
             }"
             viewBox="0 0 220 80" fill="none" aria-hidden="true">
          <!-- форма дуги -->
          <path ref="dottedPath"
                d="M6 72C44 18 176 18 214 72"
                stroke="currentColor"
                :stroke-width="cssVars['--dot-stroke']"
                stroke-linecap="round"
                :stroke-dasharray="dotDasharray"/>
        </svg>

        <!-- glows -->
        <div ref="glowRight" class="absolute rounded-full blur-3xl"
             :style="{ top:'var(--glowR-top)', right:'var(--glowR-right)', width:'var(--glowR-size)', height:'var(--glowR-size)', background:'rgba(255,255,255,.7)' }"></div>
        <div ref="glowLeft" class="absolute rounded-full blur-3xl"
             :style="{ bottom:'var(--glowL-bottom)', left:'var(--glowL-left)', width:'var(--glowL-size)', height:'var(--glowL-size)', background:'rgba(255,255,255,.7)' }"></div>

        <!-- Полароіди -->
        <figure ref="polaroidRight" class="polaroid absolute"
                :style="{ top:'var(--polR-top)', right:'var(--polR-right)', transform:'rotate(var(--polR-rot))' }">
          <div class="polaroid__photo">
            <img v-if="rightImageUrl" :src="rightImageUrl" :alt="rightImageAlt || rightCta.text" class="h-full w-full object-cover" decoding="async" loading="lazy"/>
            <div v-else class="h-full w-full bg-slate-200"></div>
          </div>
        </figure>

        <figure ref="polaroidLeft" class="polaroid absolute"
                :style="{ bottom:'var(--polL-bottom)', left:'var(--polL-left)', transform:'rotate(var(--polL-rot))' }">
          <div class="polaroid__photo">
            <img v-if="leftImageUrl" :src="leftImageUrl" :alt="leftImageAlt || leftCta.text" class="h-full w-full object-cover" decoding="async" loading="lazy"/>
            <div v-else class="h-full w-full bg-slate-200"></div>
          </div>
        </figure>

        <!-- Пілл -->
        <a ref="pill" :href="rightCta.href"
           class="absolute z-40 inline-flex items-center rounded-full px-5 py-2 font-display text-sm text-white"
           :style="{ top:'var(--pill-top)', right:'var(--pill-right)', background:'var(--violet)', transform:'rotate(var(--pill-rot))', boxShadow:'0 12px 26px rgba(123,91,255,.36)' }">
          {{ rightCta.text }}
        </a>

        <!-- Кривулька -->
        <svg ref="scribble" class="pointer-events-none absolute hidden md:block"
             :style="{ bottom:'var(--scribble-bottom)', right:'var(--scribble-right)', width:'var(--scribble-w)', color:'rgba(123,91,255,.8)' }"
             viewBox="-8 -8 216 156" fill="none"><path d="M5 60 C40 10, 80 10, 110 60 S180 110, 195 40" stroke="currentColor" stroke-width="12" stroke-linecap="round" stroke-linejoin="round" vector-effect="non-scaling-stroke"/></svg>
      </div>
    </div>
  </section>
</template>

<script setup>
import { ref, onMounted, onBeforeUnmount, nextTick, computed } from 'vue'
import gsap from 'gsap'

defineOptions({ name: 'FeatureClubsSection' })

const props = defineProps({
  heading: { type: String, default: 'Розмовні клуби —' },
  headingAccent: { type: String, default: 'більше практики, більше впевненості!' },
  subheading: { type: String, default: 'Наші розмовні клуби — це чудова можливість покращити свою англійську чи німецьку, спілкуючись у невимушеній атмосфері.' },
  leftCta: { type: Object, default: () => ({ text: 'Для наших студентів', href: '#contact' }) },
  rightCta: { type: Object, default: () => ({ text: 'Для тих, хто вивчає інше', href: '#contact' }) },
  leftImageUrl: { type: String, default: '' },
  leftImageAlt: { type: String, default: '' },
  rightImageUrl: { type: String, default: '' },
  rightImageAlt: { type: String, default: '' },
})

/* ====== КЕРУВАННЯ ПУНКТИРОМ/ПОЗИЦІЯМИ ЧЕРЕЗ PX-ЗМІННІ ====== */
const cssVars = computed(() => ({
  '--violet': '#7B5BFF',
  '--teal':   '#16B3A7',
  '--yellow': '#F8B200',

  '--arc-left':   '-48px',
  '--arc-bottom': '-160px',
  '--arc-size':   '680px',

  '--burstL-top':  '2px',  '--burstL-left': '-56px', '--burstL-w': '48px',
  '--wave-top':   '40px',  '--wave-right': '24px',   '--wave-w':  '192px',

  '--starT-top':  '52px',  '--starT-right': '64px', '--starT-w': '28px',
  '--starY-top':  '260px', '--starY-left':  '22px', '--starY-w': '32px',
  '--plus-top':   '240px', '--plus-left':   '320px','--plus-w':  '28px',
  '--burstR-top': '270px', '--burstR-right':'72px', '--burstR-w':'40px',

  /* пунктир (ВСЕ — px) */
  '--dot-top-px': '150px',
  '--dot-left-px':'110px',
  '--dot-w-px':   '300px',
  '--dot-h-px':   '120px',      /* висота контейнера svg, якщо треба */
  '--dot-rot-deg':'-13deg',       /* якщо треба повернути дугу */
  '--dot-stroke': '6',          /* товщина лінії */
  '--dot-len':    '10',         /* довжина “крапки” в px */
  '--dot-gap':    '18',         /* проміжок між крапками в px */

  '--glowR-top': '6px','--glowR-right': '48px','--glowR-size': '190px',
  '--glowL-bottom':'40px','--glowL-left':'32px','--glowL-size':'170px',

  '--polR-top':'-18px','--polR-right':'6%','--polR-rot':'12deg',
  '--polL-bottom':'-8px','--polL-left':'6%','--polL-rot':'-10deg',

  '--pill-top':'44%','--pill-right':'2%','--pill-rot':'-8deg',

  '--scribble-bottom':'8px','--scribble-right':'8px','--scribble-w':'176px',
}))

/* динамічний dasharray рядок із варів (для реактивного рендеру) */
const dotDasharray = computed(() => `${cssVars.value['--dot-len']} ${cssVars.value['--dot-gap']}`)

/* ====== GSAP ====== */
const title = ref(null), subtitle = ref(null), ctaLeft = ref(null)
const burstLeft = ref(null), wave = ref(null), starTeal = ref(null), starYellow = ref(null), plus = ref(null), burstRight = ref(null)
const dotted = ref(null), dottedPath = ref(null)
const glowLeft = ref(null), glowRight = ref(null)
const polaroidLeft = ref(null), polaroidRight = ref(null), pill = ref(null), scribble = ref(null)

let intro, loops = []

onMounted(async () => {
  if (typeof window === 'undefined') return
  const reduce = window.matchMedia?.('(prefers-reduced-motion: reduce)').matches
  await nextTick()

  gsap.set([title.value, subtitle.value, ctaLeft.value], { autoAlpha: 0, y: 18 })
  gsap.set([burstLeft.value, wave.value, starTeal.value, starYellow.value, plus.value, burstRight.value, dotted.value, scribble.value],
           { autoAlpha: 0, scale: 0.9, rotation: -4 })
  gsap.set([glowLeft.value, glowRight.value], { autoAlpha: 0 })
  gsap.set(polaroidRight.value, { autoAlpha: 0, y: -40, rotation: 20, transformOrigin: '30% 70%' })
  gsap.set(polaroidLeft.value,  { autoAlpha: 0, y:  40, rotation:-16, transformOrigin: '70% 30%' })
  gsap.set(pill.value,          { autoAlpha: 0, y: 16, rotation: -16 })

  /* підготовка пунктиру для анімації "по крапці" */
  const path = dottedPath.value
  const L = path.getTotalLength()
  const dot = Number(getComputedStyle(document.documentElement).getPropertyValue('--dot-len')) || Number(cssVars.value['--dot-len'])
  const gap = Number(getComputedStyle(document.documentElement).getPropertyValue('--dot-gap')) || Number(cssVars.value['--dot-gap'])
  const step = dot + gap
  const stepsCount = Math.max(1, Math.floor(L / step))
  path.style.strokeDasharray = `${dot} ${gap}`
  path.style.strokeDashoffset = L

  intro = gsap.timeline({ defaults:{ ease:'power2.out' }, paused: reduce })
    .to(title.value,    { autoAlpha:1, y:0, duration:.55 }, .1)
    .to(subtitle.value, { autoAlpha:1, y:0, duration:.55 }, .35)
    .to(ctaLeft.value,  { autoAlpha:1, y:0, duration:.45 }, .55)
    .to(burstLeft.value,{ autoAlpha:1, scale:1, rotation:0, duration:.35 }, .55)
    .to([wave.value, starTeal.value], { autoAlpha:1, scale:1, rotation:0, duration:.35, stagger:.08 }, .7)
    .to([plus.value, burstRight.value, starYellow.value], { autoAlpha:1, scale:1, rotation:0, duration:.35, stagger:.08 }, .85)
    /* показати SVG контейнер пунктиру */
    .to(dotted.value,   { autoAlpha:1, scale:1, rotation:0, duration:.2 },  .98)
    /* анімація “одна крапка за секунду” уздовж шляху */
    .to(path, { strokeDashoffset: 0, duration: stepsCount, ease: `steps(${stepsCount})` }, 1.0)
    .to([glowLeft.value, glowRight.value], { autoAlpha:1, duration:.2 }, 1.0)
    .to(polaroidRight.value, { autoAlpha:1, y:0, rotation:12,  duration:.55 }, 1.05)
    .to(polaroidLeft.value,  { autoAlpha:1, y:0, rotation:-10, duration:.55 }, 1.15)
    .to(pill.value,          { autoAlpha:1, y:0, rotation:-8,  duration:.45 }, 1.35)
    .to(scribble.value,      { autoAlpha:1, scale:1, rotation:0, duration:.35 }, 1.35)

  if (!reduce) intro.play()

  if (!reduce) {
    loops = [
      gsap.to(wave.value, { x:8, duration:3.2, ease:'sine.inOut', yoyo:true, repeat:-1 }),
      gsap.to(plus.value, { rotation:8, duration:4, ease:'sine.inOut', yoyo:true, repeat:-1 }),
      gsap.to(starTeal.value,   { y:-6, duration:2.4, ease:'sine.inOut', yoyo:true, repeat:-1, delay:.4 }),
      gsap.to(starYellow.value, { y: 6, duration:2.7, ease:'sine.inOut', yoyo:true, repeat:-1, delay:.6 }),
      gsap.to(burstLeft.value,  { rotation:10,  duration:5,   ease:'sine.inOut', yoyo:true, repeat:-1 }),
      gsap.to(burstRight.value, { rotation:-10, duration:5.2, ease:'sine.inOut', yoyo:true, repeat:-1 }),
      gsap.to(polaroidLeft.value,  { y:'+=6',  rotation:'-=1.5', duration:4.5, ease:'sine.inOut', yoyo:true, repeat:-1 }),
      gsap.to(polaroidRight.value, { y:'-=6',  rotation:'+=1.5', duration:4.8, ease:'sine.inOut', yoyo:true, repeat:-1 }),
      gsap.to(pill.value,          { y:'+=3',  rotation:'-=2',   duration:3.2, ease:'sine.inOut', yoyo:true, repeat:-1 }),
    ]
  }
})

onBeforeUnmount(() => {
  try { intro && intro.kill() } catch {}
  loops.forEach(a => { try { a.kill() } catch {} })
})
</script>

<style scoped>
.polaroid { @apply z-30 w-56 sm:w-64 rounded-2xl bg-white p-3; box-shadow: 0 28px 60px rgba(6,42,66,.16), 0 8px 18px rgba(6,42,66,.10); }
.polaroid__photo { @apply relative w-full overflow-hidden rounded-xl; padding-bottom: 80%; }
</style>
