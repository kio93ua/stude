// resources/js/islands.js
import { createApp, h, markRaw } from 'vue'

// 1) Реєстр островів (синхронні імпорти)
import HeroSection from './components/HeroSection.vue'
import ProgramsSection from './components/ProgramsSection.vue'
import AboutSection from './components/AboutSection.vue'
import ApproachSection from './components/ApproachSection.vue'
import TestimonialsSection from './components/TestimonialsSection.vue'
import CtaSection from './components/CtaSection.vue'
import ContactSection from './components/ContactSection.vue'

// Не робимо їх реактивними — зайве і може шкодити
const registry = {
  HeroSection: markRaw(HeroSection),
  ProgramsSection: markRaw(ProgramsSection),
  AboutSection: markRaw(AboutSection),
  ApproachSection: markRaw(ApproachSection),
  TestimonialsSection: markRaw(TestimonialsSection),
  CtaSection: markRaw(CtaSection),
  ContactSection: markRaw(ContactSection),
}

function parseProps(el) {
  if (!el?.dataset?.props) return {}
  try { return JSON.parse(el.dataset.props) }
  catch {
    console.warn('[islands] Bad JSON in data-props:', el.dataset.props)
    return {}
  }
}

function mountIsland(el) {
  const key = el?.dataset?.vue
  const Comp = key && registry[key]
  if (!Comp) return
  if (el.__vue_app__ || el.dataset.vueMounted === '1') return // захист від дубля

  const props = parseProps(el)

  // Інлайн-хост без SFC => жодних scopeId
  const Host = {
    name: `IslandHost(${key})`,
    setup() {
      const C = Comp
      const p = props
      return () => h(C, p)
    },
  }

  const app = createApp(Host)
  el.__vue_app__ = app
  el.dataset.vueMounted = '1'
  app.mount(el)

  const unmount = () => {
    try { app.unmount() } catch { }
    delete el.__vue_app__
    delete el.dataset.vueMounted
    delete el.__vue_unmount__
  }
  el.__vue_unmount__ = unmount
  window.addEventListener('pagehide', unmount, { once: true })
}

function scanAndMount(root = document) {
  root.querySelectorAll('[data-vue]').forEach(mountIsland)
}

// Перший прохід
if (document.readyState === 'loading') {
  document.addEventListener('DOMContentLoaded', () => scanAndMount())
} else {
  scanAndMount()
}

// Монтуємо будь-які нові острови, що з’являться пізніше
const mo = new MutationObserver((mutations) => {
  for (const m of mutations) {
    m.addedNodes?.forEach((n) => {
      if (n.nodeType !== 1) return
      const el = /** @type {Element} */ (n)
      if (el.matches?.('[data-vue]')) mountIsland(el)
      const nested = el.querySelectorAll?.('[data-vue]')
      nested && nested.forEach(mountIsland)
    })
  }
})
mo.observe(document.documentElement, { subtree: true, childList: true })

// За потреби можна викликати вручну з інших скриптів
window.__mountIslands = scanAndMount
