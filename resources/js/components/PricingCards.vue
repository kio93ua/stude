<template>
  <section id="pricing" class="relative overflow-hidden bg-gradient-to-br from-brand-mint via-white to-brand-aqua/10 py-16 sm:py-24">
    <!-- м’які плями -->
    <div aria-hidden class="pointer-events-none absolute -top-28 -left-28 h-72 w-72 rounded-full bg-brand-mint/30 blur-3xl"></div>
    <div aria-hidden class="pointer-events-none absolute -bottom-40 -right-32 h-80 w-80 rounded-full bg-accent/25 blur-3xl"></div>

    <div class="mx-auto max-w-7xl px-6">
      <header class="mx-auto max-w-3xl text-center">
        <p class="badge-muted font-display mb-3">Пакети занять</p>
        <h2 class="heading-1 font-display tracking-tight text-secondary">Обери формат, що пасує саме тобі</h2>
        <p class="mt-3 text-lg text-secondary/85 font-sans">Три прозорі варіанти з чіткими перевагами.</p>
      </header>

      <!-- 3 колонки, висота — тільки від контенту -->
      <div ref="grid" class="mt-12 grid items-start gap-6 sm:grid-cols-3">
        <!-- === Групові === -->
        <article
          ref="cardGroup"
          data-type="group"
          class="price-card wave relative rounded-3xl bg-white p-6 shadow-xl ring-1 ring-black/10 transition hover:shadow-2xl"
        >
          <div class="sticker">
            <picture>
              <source type="image/webp" :srcset="group.webp" />
              <img :src="group.png" alt="" decoding="async" loading="lazy" />
            </picture>
          </div>

          <div class="card-inner flex h-full flex-col gap-5 pr-28 md:pr-36 lg:pr-40">
            <header class="space-y-1">
              <h3 class="text-2xl font-extrabold font-display text-secondary">Групові</h3>
              <p class="text-[11px] uppercase tracking-wider text-accent/90 font-semibold">mini-group</p>
            </header>

            <p class="text-sm text-secondary/85">Більше практики у малій групі — вигідно та драйвово.</p>

            <div>
              <div class="flex items-end gap-2">
                <span class="text-5xl leading-none font-black font-display text-secondary">{{ currency }}{{ group.price }}</span>
                <span class="pb-1 text-secondary/75 text-sm">за урок</span>
              </div>
              <p class="mt-1 text-xs text-secondary/75">{{ group.meta }}</p>
            </div>

            <ul class="divide-y divide-black/5 text-sm text-secondary/90">
              <li class="py-2">Міні-групи до 6 осіб</li>
              <li class="py-2">Ігрові активності</li>
              <li class="py-2">Speaking-клуб 2×/міс</li>
            </ul>

            <a :href="group.cta" class="btn-outline mt-auto w-full justify-center font-display">Записатися</a>
          </div>

          <div aria-hidden class="pointer-events-none absolute inset-0 rounded-3xl opacity-0 transition group-hover:opacity-100" :style="glowSoft"></div>
        </article>

        <!-- === Пари === -->
        <article
          ref="cardPair"
          data-type="pair"
          class="price-card wave relative rounded-3xl p-6 shadow-2xl ring-1 ring-black/10 transition hover:shadow-[0_20px_60px_-15px_rgba(0,0,0,0.35)]
                 bg-gradient-to-b from-brand-aqua via-brand-mint to-brand-aqua/30 text-white"
        >
          <div class="sticker">
            <picture>
              <source type="image/webp" :srcset="pair.webp" />
              <img :src="pair.png" alt="" decoding="async" loading="lazy" />
            </picture>
          </div>

          <div class="card-inner flex h-full flex-col gap-5 pr-28 md:pr-36 lg:pr-40">
            <header class="space-y-1">
              <h3 class="text-2xl font-extrabold font-display">Пари</h3>
              <p class="text-[11px] uppercase tracking-wider text-white/90 font-semibold">duo</p>
            </header>

            <p class="text-sm/6 text-white/90">Вчимося разом — баланс вартості та прогресу.</p>

            <div>
              <div class="flex items-end gap-2">
                <span class="text-5xl leading-none font-black font-display">{{ currency }}{{ pair.price }}</span>
                <span class="pb-1 text-white/85 text-sm">за урок</span>
              </div>
              <p class="mt-1 text-xs text-white/85">{{ pair.meta }}</p>
            </div>

            <ul class="divide-y divide-white/15 text-sm text-white/95">
              <li class="py-2">2× / тиждень · 60 хв</li>
              <li class="py-2">Speaking-клуб 1×/міс</li>
              <li class="py-2">Зворотний зв’язок</li>
            </ul>

            <a :href="pair.cta" class="btn-primary mt-auto w-full justify-center font-display relative overflow-hidden">
              Обрати пару
              <span aria-hidden class="sheen"></span>
            </a>
          </div>

          <div aria-hidden class="pointer-events-none absolute inset-0 rounded-3xl opacity-0 transition group-hover:opacity-100" :style="glowBright"></div>
        </article>

        <!-- === Індивідуальні === -->
        <article
          ref="cardInd"
          data-type="ind"
          class="price-card wave relative rounded-3xl bg-secondary/95 p-6 shadow-2xl ring-1 ring-black/20 transition hover:shadow-[0_24px_70px_-18px_rgba(0,0,0,0.45)]
                 text-white"
        >
          <div class="sticker">
            <picture>
              <source type="image/webp" :srcset="individual.webp" />
              <img :src="individual.png" alt="" decoding="async" loading="lazy" />
            </picture>
          </div>

          <div class="card-inner flex h-full flex-col gap-5 pr-28 md:pr-36 lg:pr-40">
            <header class="space-y-1">
              <h3 class="text-2xl font-extrabold font-display">Індивідуальні</h3>
              <p class="text-[11px] uppercase tracking-wider text-white/90 font-semibold">1-on-1</p>
            </header>

            <p class="text-sm/6 text-white/95">Персональний темп, фокус на ваших цілях та сильна підтримка.</p>

            <div>
              <div class="flex items-end gap-2">
                <span class="text-5xl leading-none font-black font-display">{{ currency }}{{ individual.price }}</span>
                <span class="pb-1 text-white/85 text-sm">за урок</span>
              </div>
              <p class="mt-1 text-xs text-white/85">{{ individual.meta }}</p>
            </div>

            <ul class="divide-y divide-white/15 text-sm text-white/95">
              <li class="py-2">1× / тиждень · 50 хв (або більше)</li>
              <li class="py-2">Персональний план</li>
              <li class="py-2">Домашні з перевіркою</li>
            </ul>

            <a :href="individual.cta" class="btn-outline mt-auto w-full justify-center font-display border-white/40 text-white hover:bg-white/10">
              Записатися
            </a>
          </div>

          <div aria-hidden class="pointer-events-none absolute inset-0 rounded-3xl opacity-0 transition group-hover:opacity-100" :style="glowDark"></div>
        </article>
      </div>
    </div>
  </section>
</template>

<script setup>
import { ref, onMounted, onBeforeUnmount, nextTick } from 'vue'
import gsap from 'gsap'
import { ScrollTrigger } from 'gsap/ScrollTrigger'
gsap.registerPlugin(ScrollTrigger)

defineOptions({ name: 'PricingCards' })

/* валюта + дані */
const currency = '₴'
const individual = {
  price: 600, meta: '1 урок / тиждень · 50 хв', cta: '#contact',
  webp: '/images/price/price1.webp', png: '/images/price/price1.png'
}
const pair = {
  price: 400, meta: '2 уроки / тиждень · 60 хв', cta: '#contact',
  webp: '/images/price/price2.webp', png: '/images/price/price2.png'
}
const group = {
  price: 250, meta: '3 уроки / тиждень · 60 хв', cta: '#contact',
  webp: '/images/price/price3.webp', png: '/images/price/price3.png'
}

/* refs */
const grid = ref(null)
const cardGroup = ref(null)
const cardPair  = ref(null)
const cardInd   = ref(null)

/* глоу */
const glowSoft   = 'background: radial-gradient(900px 200px at 50% 110%, rgba(16,185,129,.10), transparent 60%);'
const glowBright = 'background: radial-gradient(1200px 220px at 50% 115%, rgba(59,130,246,.22), transparent 60%), radial-gradient(800px 160px at 20% -10%, rgba(16,185,129,.18), transparent 60%);'
const glowDark   = 'background: radial-gradient(900px 240px at 50% 115%, rgba(255,255,255,.12), transparent 60%);'

/* ефект «дзвінка/вібрації» після розкриття */
function ring(el) {
  const tl = gsap.timeline()
  tl.to(el, { rotation: -2.2, x: -5, duration: 0.09, ease: 'power1.inOut' })
    .to(el, { rotation:  2.2, x:  5, duration: 0.09, ease: 'power1.inOut', repeat: 4, yoyo: true })
    .to(el, { rotation:  0,   x:  0, duration: 0.12, ease: 'power1.out' })
  return tl
}

let masterTl, cycleTimers = []
onMounted(async () => {
  await nextTick()

  const cards = [cardGroup.value, cardPair.value, cardInd.value]

  // стартові стани
  cards.forEach((c) => {
    gsap.set(c, { autoAlpha: 0, y: -28, scale: 0.985, willChange: 'transform, opacity' })
    const inner = c.querySelectorAll('.card-inner > *')
    gsap.set(inner, { autoAlpha: 0, y: 16, willChange: 'transform, opacity' })
  })

  masterTl = gsap.timeline({
    defaults: { ease: 'power4.out' },
    paused: true,
    onComplete: () => cards.forEach((c) => gsap.set(c, { clearProps: 'willChange' }))
  })

  cards.forEach((c) => {
    const inner = c.querySelectorAll('.card-inner > *')
    masterTl
      .to(c, { autoAlpha: 1, y: 0, scale: 1, duration: 1.05 })
      .to(inner, { autoAlpha: 1, y: 0, duration: 0.7, stagger: 0.18 }, '>-0.3')
      .add(() => ring(c), '>')
  })

  ScrollTrigger.create({
    trigger: grid.value,
    start: 'top 70%',
    once: true,
    onEnter: () => {
      masterTl.play()
      masterTl.then(() => {
        cards.forEach((c, idx) => {
          const t = setInterval(() => ring(c), 3000)
          cycleTimers.push(t)
          setTimeout(() => ring(c), idx * 1000)
        })
      })
    },
  })
})

onBeforeUnmount(() => {
  try { masterTl && masterTl.kill() } catch {}
  ScrollTrigger.getAll().forEach(t => t.kill())
  cycleTimers.forEach(t => clearInterval(t))
})
</script>

<style>
/* блиск кнопки */
.sheen{
  position:absolute; inset:0 auto 0 -30%;
  width:40%; transform:skewX(-20deg); opacity:.25;
  background:linear-gradient(90deg,rgba(255,255,255,0) 0%,rgba(255,255,255,.9) 50%,rgba(255,255,255,0) 100%);
  pointer-events:none;
}

/* стікер (правий верхній), збільшений і responsive */
.sticker{
  position:absolute; right:12px; top:12px;
  width:72px; height:72px;
  @apply md:w-[88px] md:h-[88px] lg:w-[96px] lg:h-[96px];
  z-index:1;
  filter: drop-shadow(0 8px 16px rgba(0,0,0,.18));
  transition: transform .35s ease, filter .35s ease, box-shadow .35s ease;
}
.sticker img{ width:100%; height:100%; object-fit:contain; display:block; }
.wave:hover .sticker{ transform: translateY(-2px) rotate(-1.5deg); }

/* підсвітка стікера при hover відповідно до типу картки */
[data-type="group"]:hover .sticker{
  box-shadow: 0 0 0 6px rgba(16,185,129,.18), 0 10px 28px rgba(16,185,129,.25);
}
[data-type="pair"]:hover .sticker{
  box-shadow: 0 0 0 6px rgba(59,130,246,.22), 0 10px 28px rgba(59,130,246,.28);
}
[data-type="ind"]:hover .sticker{
  box-shadow: 0 0 0 6px rgba(255,255,255,.22), 0 10px 28px rgba(255,255,255,.28);
}

/* хвилі з кутів при :hover — top-right + bottom-left */
.wave::before,
.wave::after{
  content:""; position:absolute; pointer-events:none; border-radius:50%;
  opacity:0; transform:scale(.6);
  transition:opacity .38s ease, transform .7s cubic-bezier(.2,.6,.2,1);
}
.wave::before{
  width:240px; height:240px; right:-48px; top:-48px;
  background:radial-gradient(closest-side, rgba(255,255,255,.55), rgba(255,255,255,0));
  filter:blur(6px);
}
.wave::after{
  width:280px; height:280px; left:-58px; bottom:-58px;
  background:radial-gradient(closest-side, rgba(16,185,129,.35), rgba(16,185,129,0));
  filter:blur(8px);
}
.wave:hover::before,
.wave:hover::after{ opacity:1; transform:scale(1); }

/* перформанс: анімуємо лише transform/opacity */
.price-card, .price-card .card-inner > * { will-change: transform, opacity; }
</style>
