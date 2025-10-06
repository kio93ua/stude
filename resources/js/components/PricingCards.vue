<template>
  <section id="pricing" class="section-surface section-surface--fixedhack py-16 sm:py-24">
    <div class="mx-auto max-w-7xl px-6">
      <header class="mx-auto max-w-3xl text-center">
        <p v-if="badge" class="badge-muted font-display mb-3">{{ badge }}</p>
        <h2 class="heading-1 font-display tracking-tight text-secondary">{{ title }}</h2>
        <p v-if="subtitle" class="mt-3 text-lg text-secondary/85 font-sans">{{ subtitle }}</p>
      </header>

      <div class="mt-12 grid items-start gap-6 sm:grid-cols-3">
        <!-- === Групові === -->
        <article
          data-type="group"
          class="price-card wave relative rounded-3xl bg-white p-6 shadow-xl ring-1 ring-black/10 transition"
        >
          <div class="card-inner flex h-full flex-col items-center text-center gap-5">
            <header class="space-y-1">
              <h3 class="text-2xl font-extrabold font-display text-secondary">{{ group.title }}</h3>
              <p v-if="group.label" class="text-[11px] uppercase tracking-wider text-accent/90 font-semibold">{{ group.label }}</p>
            </header>

            <p v-if="group.description" class="mt-5 md:mt-7 text-[15px] leading-6 text-secondary/90">
              {{ group.description }}
            </p>

            <div>
              <div class="flex items-end justify-center gap-2">
                <span class="text-5xl leading-none font-black font-display text-secondary">{{ currency }}{{ group.price }}</span>
                <span class="pb-1 text-secondary/75 text-sm">за урок</span>
              </div>
              <p v-if="group.meta" class="mt-1 text-xs text-secondary/75">{{ group.meta }}</p>
            </div>

            <ul v-if="group.features.length" class="w-full divide-y divide-black/5 text-sm text-secondary/90">
              <li v-for="(item, idx) in group.features" :key="`group-${idx}`" class="py-2">{{ item }}</li>
            </ul>
            <p v-else class="text-sm text-secondary/60">
              Додайте переваги для цієї картки у налаштуваннях.
            </p>

            <!-- СТІКЕР -->
            <div class="sticker-bottom">
              <picture>
                <source type="image/webp" :srcset="group.webp" />
                <img :src="group.png" alt="" decoding="async" loading="lazy" />
              </picture>
            </div>

            <a :href="group.cta_href" class="btn-outline mt-auto w-full justify-center font-display">
              {{ group.cta_text }}
            </a>
          </div>

          <div aria-hidden class="pointer-events-none absolute inset-0 rounded-3xl opacity-0 transition group-hover:opacity-100" :style="glowSoft"></div>
        </article>

        <!-- === Пари === -->
        <article
          data-type="pair"
          class="price-card wave relative rounded-3xl p-6 shadow-2xl ring-1 ring-black/10 transition
                 bg-gradient-to-b from-brand-aqua via-brand-mint to-brand-aqua/30 text-white"
        >
          <div class="card-inner flex h-full flex-col items-center text-center gap-5">
            <header class="space-y-1">
              <h3 class="text-2xl font-extrabold font-display">{{ pair.title }}</h3>
              <p v-if="pair.label" class="text-[11px] uppercase tracking-wider text-white/90 font-semibold">{{ pair.label }}</p>
            </header>

            <p v-if="pair.description" class="mt-5 md:mt-7 text-[15px] leading-6 text-white/90">
              {{ pair.description }}
            </p>

            <div>
              <div class="flex items-end justify-center gap-2">
                <span class="text-5xl leading-none font-black font-display">{{ currency }}{{ pair.price }}</span>
                <span class="pb-1 text-white/85 text-sm">за урок</span>
              </div>
              <p v-if="pair.meta" class="mt-1 text-xs text-white/85">{{ pair.meta }}</p>
            </div>

            <ul v-if="pair.features.length" class="w-full divide-y divide-white/15 text-sm text-white/95">
              <li v-for="(item, idx) in pair.features" :key="`pair-${idx}`" class="py-2">{{ item }}</li>
            </ul>
            <p v-else class="text-sm text-white/70">
              Додайте переваги для цієї картки у налаштуваннях.
            </p>

            <div class="sticker-bottom">
              <picture>
                <source type="image/webp" :srcset="pair.webp" />
                <img :src="pair.png" alt="" decoding="async" loading="lazy" />
              </picture>
            </div>

            <a :href="pair.cta_href" class="btn-primary mt-auto w-full justify-center font-display relative overflow-hidden">
              {{ pair.cta_text }}
              <span aria-hidden class="sheen"></span>
            </a>
          </div>

          <div aria-hidden class="pointer-events-none absolute inset-0 rounded-3xl opacity-0 transition group-hover:opacity-100" :style="glowBright"></div>
        </article>

        <!-- === Індивідуальні === -->
        <article
          data-type="ind"
          class="price-card wave relative rounded-3xl bg-secondary/95 p-6 shadow-2xl ring-1 ring-black/20 transition text-white"
        >
          <div class="card-inner flex h-full flex-col items-center text-center gap-5">
            <header class="space-y-1">
              <h3 class="text-2xl font-extrabold font-display">{{ individual.title }}</h3>
              <p v-if="individual.label" class="text-[11px] uppercase tracking-wider text-white/90 font-semibold">{{ individual.label }}</p>
            </header>

            <p v-if="individual.description" class="mt-5 md:mt-7 text-[15px] leading-6 text-white/95">
              {{ individual.description }}
            </p>

            <div>
              <div class="flex items-end justify-center gap-2">
                <span class="text-5xl leading-none font-black font-display">{{ currency }}{{ individual.price }}</span>
                <span class="pb-1 text-white/85 text-sm">за урок</span>
              </div>
              <p v-if="individual.meta" class="mt-1 text-xs text-white/85">{{ individual.meta }}</p>
            </div>

            <ul v-if="individual.features.length" class="w-full divide-y divide-white/15 text-sm text-white/95">
              <li v-for="(item, idx) in individual.features" :key="`ind-${idx}`" class="py-2">{{ item }}</li>
            </ul>
            <p v-else class="text-sm text-white/70">
              Додайте переваги для цієї картки у налаштуваннях.
            </p>

            <div class="sticker-bottom">
              <picture>
                <source type="image/webp" :srcset="individual.webp" />
                <img :src="individual.png" alt="" decoding="async" loading="lazy" />
              </picture>
            </div>

            <a :href="individual.cta_href" class="btn-outline btn-on-dark mt-auto w-full justify-center font-display">
              {{ individual.cta_text }}
            </a>
          </div>

          <div aria-hidden class="pointer-events-none absolute inset-0 rounded-3xl opacity-0 transition group-hover:opacity-100" :style="glowDark"></div>
        </article>
      </div>
    </div>
  </section>
</template>


<script setup>
import { computed } from 'vue'

defineOptions({ name: 'PricingCards' })

const props = defineProps({
  badge: { type: String, default: 'Пакети занять' },
  title: { type: String, default: 'Обери формат, що пасує саме тобі' },
  subtitle: { type: String, default: 'Три прозорі варіанти з чіткими перевагами.' },
  currency: { type: String, default: '₴' },
  plans: {
    type: Object,
    default: () => ({}),
  },
})

const iconMap = {
  group: { webp: '/images/price/price3.webp', png: '/images/price/price3.png' },
  pair: { webp: '/images/price/price2.webp', png: '/images/price/price2.png' },
  individual: { webp: '/images/price/price1.webp', png: '/images/price/price1.png' },
}

const planDefaults = {
  group: {
    title: 'Групові',
    label: 'mini-group',
    description: 'Більше практики у малій групі — вигідно та драйвово.',
    price: '250',
    meta: '3 уроки / тиждень · 60 хв',
    features: ['Міні-групи до 6 осіб', 'Ігрові активності', 'Speaking-клуб 2×/міс'],
    cta_text: 'Записатися',
    cta_href: '#contact',
  },
  pair: {
    title: 'Пари',
    label: 'duo',
    description: 'Вчимося разом — баланс вартості та прогресу.',
    price: '400',
    meta: '2 уроки / тиждень · 60 хв',
    features: ['2× / тиждень · 60 хв', 'Speaking-клуб 1×/міс', 'Зворотний зв’язок'],
    cta_text: 'Обрати пару',
    cta_href: '#contact',
  },
  individual: {
    title: 'Індивідуальні',
    label: '1-on-1',
    description: 'Персональний темп, фокус на ваших цілях та сильна підтримка.',
    price: '600',
    meta: '1 урок / тиждень · 50 хв',
    features: ['Персональний план', 'Домашні з перевіркою', 'Гнучкий графік'],
    cta_text: 'Записатися',
    cta_href: '#contact',
  },
}

const ensureString = (value, fallback = '') => {
  if (typeof value === 'string') {
    const trimmed = value.trim()
    return trimmed !== '' ? trimmed : fallback
  }

  if (value === null || value === undefined) return fallback

  const str = String(value).trim()
  return str !== '' ? str : fallback
}

const ensureFeatures = (value, fallback = []) => {
  if (Array.isArray(value)) {
    const cleaned = value
      .map((item) => (typeof item === 'string' ? item.trim() : (item && typeof item === 'object' ? String(item.label ?? item.text ?? item.value ?? '').trim() : '')))
      .filter(Boolean)
    return cleaned.length ? cleaned : fallback
  }

  if (typeof value === 'string') {
    const cleaned = value.split(',').map(v => v.trim()).filter(Boolean)
    if (cleaned.length) return cleaned
  }

  return [...fallback]
}

const normalizePlan = (key) => {
  const base = planDefaults[key]
  const incoming = (props.plans && props.plans[key]) || {}

  return {
    title: ensureString(incoming.title, base.title),
    label: ensureString(incoming.label, base.label),
    description: ensureString(incoming.description, base.description),
    price: ensureString(incoming.price, base.price),
    meta: ensureString(incoming.meta, base.meta),
    features: ensureFeatures(incoming.features, base.features),
    cta_text: ensureString(incoming.cta_text, base.cta_text),
    cta_href: ensureString(incoming.cta_href, base.cta_href) || '#contact',
    ...iconMap[key],
  }
}

const normalizedPlans = computed(() => ({
  group: normalizePlan('group'),
  pair: normalizePlan('pair'),
  individual: normalizePlan('individual'),
}))

const currency = computed(() => ensureString(props.currency, '₴'))
const group = computed(() => normalizedPlans.value.group)
const pair = computed(() => normalizedPlans.value.pair)
const individual = computed(() => normalizedPlans.value.individual)

/* глоу для фонового підсвічування */
const glowSoft   = 'background: radial-gradient(900px 200px at 50% 110%, rgba(16,185,129,.10), transparent 60%);'
const glowBright = 'background: radial-gradient(1200px 220px at 50% 115%, rgba(59,130,246,.22), transparent 60%), radial-gradient(800px 160px at 20% -10%, rgba(16,185,129,.18), transparent 60%);'
const glowDark   = 'background: radial-gradient(900px 240px at 50% 115%, rgba(255,255,255,.12), transparent 60%);'
</script>

<style>
/* Кольори підсвічки через CSS-перемінні */
[data-type="group"]{ --hl: 16,185,129; }     /* teal */
[data-type="pair"] { --hl: 59,130,246; }     /* blue */
[data-type="ind"]  { --hl: 255,255,255; }    /* white на темній картці */

/* Плавний фільтр та обводка картки при hover */
.price-card{
  transition: filter .35s ease, box-shadow .35s ease, transform .25s ease;
}
.price-card:hover{
  filter: saturate(1.06) contrast(1.03);
}
/* Гарна “кільцева” обводка (адаптується під тип) */
.price-card::after{
  content:"";
  position:absolute; inset:0; border-radius:inherit; pointer-events:none;
  transition: opacity .35s ease, box-shadow .35s ease;
  opacity:0;
}
.price-card:hover::after{
  opacity:1;
  /* зовнішній акцент + м’яке світіння всередину */
  box-shadow:
    0 0 0 2px rgba(var(--hl), .55),
    0 0 0 10px rgba(var(--hl), .10) inset;
}

/* блиск центральної кнопки */
.sheen{
  position:absolute; inset:0 auto 0 -30%;
  width:40%; transform:skewX(-20deg); opacity:.25;
  background:linear-gradient(90deg,rgba(255,255,255,0) 0%,rgba(255,255,255,.9) 50%,rgba(255,255,255,0) 100%);
  pointer-events:none;
}

/* СТІКЕР ВНИЗУ — квадратна підсвітка + відгук на hover картки */
.sticker-bottom{
  position: relative;
  margin-top: .25rem; margin-bottom: .25rem;
  width: 96px; height: 96px;
  display: grid; place-items: center;
  filter: drop-shadow(0 8px 16px rgba(0,0,0,.18));
  transition: transform .25s ease;
}
.sticker-bottom picture,
.sticker-bottom img{ width:100%; height:100%; object-fit:contain; display:block; }

/* квадратний “глоу”-бокс під PNG */
.sticker-bottom::before{
  content:"";
  position:absolute; inset:-8px;
  border-radius: 14px;
  background:
    linear-gradient(180deg, rgba(var(--hl), .20), rgba(var(--hl), .08));
  box-shadow:
    0 8px 22px rgba(var(--hl), .25),
    0 0 0 1px rgba(var(--hl), .25) inset;
  filter: blur(4px);
  opacity: .0;
  transition: opacity .35s ease, filter .35s ease;
  z-index: -1;
}
/* при наведенні на картку — підсвітити квадрат і трохи підняти стікер */
.price-card:hover .sticker-bottom{ transform: translateY(-2px); }
.price-card:hover .sticker-bottom::before{ opacity: 1; filter: blur(6px); }

/* хвилі з кутів при :hover — декоративно */
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
  background:radial-gradient(closest-side, rgba(var(--hl), .35), rgba(var(--hl), 0));
  filter:blur(8px);
}
.wave:hover::before,
.wave:hover::after{ opacity:1; transform:scale(1); }

/* performance hint */
.price-card, .price-card .card-inner > * { will-change: transform, opacity; }
/* кнопка на темному фоні: контрастний hover/active/focus */
.btn-on-dark{
  /* базовий стан */
  border-color: rgba(255,255,255,.40);
  color: #fff;
  background: transparent;
  transition: background-color .2s ease, border-color .2s ease, box-shadow .2s ease;
}
.btn-on-dark:hover{
  background: rgba(255,255,255,.15);   
  border-color: rgba(255,255,255,.65);
  color: #fff;
}
.btn-on-dark:active{
  background: rgba(255,255,255,.25);
}
.btn-on-dark:focus-visible{
  outline: none;
  box-shadow: 0 0 0 2px rgba(255,255,255,.40); /* focus ring */
  border-color: rgba(255,255,255,.75);
}

</style>
