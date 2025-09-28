import { h, ref, computed, onMounted, onBeforeUnmount, nextTick } from 'vue'
import gsap from 'gsap'

export default {
  name: 'HeroSection',
  props: {
    headingTag: { type: String, default: 'h1' },
    badge: { type: String, default: 'Індивідуальні заняття з англійської' },
    title: { type: String, default: 'Допоможу заговорити англійською впевнено вже за 3 місяці' },
    subtitle: { type: String, default: 'Я — репетитор з 8-річним досвідом…' },
    listTitle: { type: String, default: 'Що ви отримаєте' },
    bullets: { type: Array, default: () => [] },
    primary: { type: Object, default: () => ({ text: 'Запис на пробний урок', href: '#contact' }) },
    secondary: { type: Object, default: () => ({ text: 'Дивитися програми', href: '#services' }) },
    imageUrl: { type: String, default: '' },
    imageAlt: { type: String, default: '' },
    imageWidth: { type: [Number, String], default: null },
    imageHeight: { type: [Number, String], default: null },
  },

  setup(props) {
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
      const w = Number(props.imageWidth), h = Number(props.imageHeight)
      return (w > 0 && h > 0)
        ? { paddingBottom: `${(h / w) * 100}%`, position: 'relative', width: '100%' }
        : { paddingBottom: '56.25%', position: 'relative', width: '100%' }
    })
    const imageAltComputed = computed(() => props.imageAlt || props.title)

    let tl // один локальний таймлайн

    onMounted(async () => {
      // ● без gsap.context — ніяких scope/cleanup від GSAP
      await nextTick()                            // дочекайся DOM
      if (typeof window === 'undefined') return
      if (window.matchMedia?.('(prefers-reduced-motion: reduce)').matches) return

      const L = leftCol.value, C = card.value, B = bulletsEl.value
      // якщо вузли ще не існують — нічого не робимо (уникнемо "Invalid scope")
      if (!L && !C) return

      if (L) gsap.set(L, { autoAlpha: 0, y: 12 })
      if (C) gsap.set(C, { autoAlpha: 0, y: 12 })

      tl = gsap.timeline({ defaults: { duration: 0.5, ease: 'power2.out' } })
      if (L) tl.to(L, { autoAlpha: 1, y: 0 })
      if (C) tl.to(C, { autoAlpha: 1, y: 0 }, '-=0.2')

      if (B) {
        const items = B.querySelectorAll('li')
        if (items?.length) tl.from(items, { opacity: 0, y: 8, stagger: 0.06, duration: 0.35 }, '-=0.2')
      }
    })

    onBeforeUnmount(() => {
      // явний cleanup — без context.revert()
      try { tl && tl.kill() } catch { }
      tl = null
      // на всякий випадок прибиваємо всі твіни по нашим елементам
      const nodes = [leftCol.value, card.value]
      nodes.forEach(n => n && gsap.killTweensOf(n))
    })

    // render-функція
    return () => h('section',
      { ref: root, id: 'hero', class: 'overflow-hidden bg-gradient-to-br from-brand-mint via-white to-brand-aqua/10' },
      [
        h('div', { class: 'mx-auto grid max-w-6xl gap-10 px-6 pb-16 pt-20 md:grid-cols-2 md:items-center' }, [
          h('div', { ref: leftCol, class: 'space-y-6' }, [
            props.badge ? h('span', { class: 'badge-muted' }, props.badge) : null,
            h(props.headingTag || 'h1', { class: 'heading-1' }, props.title),
            h('p', { class: 'text-lg text-muted' }, props.subtitle),
            h('div', { class: 'flex flex-col gap-3 sm:flex-row' }, [
              (props.primary?.href && props.primary?.text)
                ? h('a', { href: props.primary.href, class: 'btn-primary' }, props.primary.text) : null,
              (props.secondary?.href && props.secondary?.text)
                ? h('a', { href: props.secondary.href, class: 'btn-outline' }, props.secondary.text) : null,
            ]),
          ]),
          h('div', { class: 'relative' }, [
            h('div', { 'aria-hidden': 'true', class: 'absolute -left-10 top-10 hidden h-24 w-24 rounded-full bg-muted/60 blur-3xl md:block' }),
            h('div', { 'aria-hidden': 'true', class: 'absolute -right-8 bottom-4 hidden h-20 w-20 rounded-full bg-accent/30 blur-2xl md:block' }),
            h('div', { ref: card, class: 'relative rounded-3xl bg-white p-6 shadow-xl shadow-muted/40' }, [
              h('div', { class: 'space-y-4' }, [
                hasImage.value
                  ? h('div', { class: 'overflow-hidden rounded-2xl' }, [
                    h('div', { style: aspectBoxStyle.value }, [
                      h('img', {
                        class: 'absolute inset-0 h-full w-full object-cover',
                        src: props.imageUrl,
                        alt: imageAltComputed.value,
                        width: props.imageWidth || undefined,
                        height: props.imageHeight || undefined,
                        loading: 'lazy',
                        decoding: 'async',
                      }),
                    ]),
                  ])
                  : null,
                h('h2', { class: 'text-lg font-semibold text-secondary' }, props.listTitle),
                visibleBullets.value.length
                  ? h('ul', { ref: bulletsEl, class: 'space-y-3 text-sm text-muted' },
                    visibleBullets.value.map((item, i) =>
                      h('li', { key: i, class: 'flex items-start gap-3' }, [
                        h('span', { class: 'mt-1 h-2.5 w-2.5 shrink-0 rounded-full bg-primary' }),
                        h('span', null, item),
                      ]),
                    ))
                  : h('p', { class: 'text-sm text-muted/60' }, '(Налаштуйте «Список переваг» у адмінці)'),
              ]),
            ]),
          ]),
        ]),
      ],
    )
  },
}
