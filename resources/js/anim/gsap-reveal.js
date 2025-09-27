import gsap from 'gsap'
import { ScrollTrigger } from 'gsap/ScrollTrigger'

gsap.registerPlugin(ScrollTrigger)

const reduceMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches

// Ініціалізація анімацій всередині root. Викликаємо в onMounted кожного компонента
export function gsapReveal({ root, once = true } = {}) {
  if (!root) root = document.body
  if (reduceMotion) return

  // up
  root.querySelectorAll('[data-reveal="up"]').forEach((el) => {
    gsap.fromTo(el, { y: 32, opacity: 0 }, {
      y: 0, opacity: 1, duration: 0.7, ease: 'power2.out',
      scrollTrigger: {
        trigger: el,
        start: 'top 85%',
        toggleActions: once ? 'play none none none' : 'play none none reverse',
      },
    })
  })

  // left
  root.querySelectorAll('[data-reveal="left"]').forEach((el) => {
    gsap.fromTo(el, { x: -40, opacity: 0 }, {
      x: 0, opacity: 1, duration: 0.7, ease: 'power2.out',
      scrollTrigger: { trigger: el, start: 'top 85%', toggleActions: once ? 'play none none none' : 'play none none reverse' },
    })
  })

  // right
  root.querySelectorAll('[data-reveal="right"]').forEach((el) => {
    gsap.fromTo(el, { x: 40, opacity: 0 }, {
      x: 0, opacity: 1, duration: 0.7, ease: 'power2.out',
      scrollTrigger: { trigger: el, start: 'top 85%', toggleActions: once ? 'play none none none' : 'play none none reverse' },
    })
  })

  // pop
  root.querySelectorAll('[data-reveal="pop"]').forEach((el) => {
    gsap.fromTo(el, { scale: 0.95, opacity: 0 }, {
      scale: 1, opacity: 1, duration: 0.6, ease: 'back.out(1.7)',
      scrollTrigger: { trigger: el, start: 'top 90%', toggleActions: once ? 'play none none none' : 'play none none reverse' },
    })
  })

  // stagger lists
  root.querySelectorAll('[data-stagger="up"]').forEach((list) => {
    const items = list.querySelectorAll('[data-item]')
    gsap.fromTo(items, { y: 20, opacity: 0 }, {
      y: 0, opacity: 1, duration: 0.5, ease: 'power2.out', stagger: 0.08,
      scrollTrigger: { trigger: list, start: 'top 85%', toggleActions: once ? 'play none none none' : 'play none none reverse' },
    })
  })
}

// Анімація входу героя
export function gsapHeroEnter(root) {
  if (!root || reduceMotion) return
  const tl = gsap.timeline()
  tl.fromTo(root.querySelector('[data-hero-badge]'), { y: 10, opacity: 0 }, { y: 0, opacity: 1, duration: 0.4, ease: 'power2.out' })
    .fromTo(root.querySelector('[data-hero-title]'), { y: 18, opacity: 0 }, { y: 0, opacity: 1, duration: 0.5 }, '-=0.1')
    .fromTo(root.querySelector('[data-hero-sub]'), { y: 18, opacity: 0 }, { y: 0, opacity: 1, duration: 0.5 }, '-=0.2')
    .fromTo(root.querySelectorAll('[data-hero-cta]'), { y: 16, opacity: 0 }, { y: 0, opacity: 1, duration: 0.45, stagger: 0.08 }, '-=0.2')
    .fromTo(root.querySelector('[data-hero-card]'), { y: 24, opacity: 0 }, { y: 0, opacity: 1, duration: 0.6, ease: 'power2.out' }, '-=0.2')
}
