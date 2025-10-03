import { createApp, h, markRaw } from 'vue'

import HeroSection from './components/HeroSection.vue'
import ProgramsSection from './components/ProgramsSection.vue'
import AboutSection from './components/AboutSection.vue'
import ApproachSection from './components/ApproachSection.vue'
import TestimonialsSection from './components/TestimonialsSection.vue'
import CtaSection from './components/CtaSection.vue'
import ContactSection from './components/ContactSection.vue'
import ValuesSection from './components/ValuesSection.vue'
import PricingCards from './components/PricingCards.vue'
// ⬇️ НОВЕ
import FeatureClubsSection from './components/FeatureClubsSection.vue'
import AdvantagesSplit from './components/AdvantagesSplit.vue'
import ReviewsMarquee from './components/ReviewsMarquee.vue'
import TeacherVacancy from './components/TeacherVacancy.vue'
import FounderStory from './components/FounderStory.vue'
import LessonsBlock from './components/LessonsBlock.vue' 



const registry = {
  HeroSection: markRaw(HeroSection),
  ProgramsSection: markRaw(ProgramsSection),
  AboutSection: markRaw(AboutSection),
  ApproachSection: markRaw(ApproachSection),
  TestimonialsSection: markRaw(TestimonialsSection),
  CtaSection: markRaw(CtaSection),
  ContactSection: markRaw(ContactSection),
  ValuesSection: markRaw(ValuesSection),
  PricingCards: markRaw(PricingCards), 
  AdvantagesSplit: markRaw(AdvantagesSplit),
  ReviewsMarquee: markRaw(ReviewsMarquee),
  TeacherVacancy: markRaw(TeacherVacancy),
  FounderStory: markRaw(FounderStory),
  LessonsBlock: markRaw(LessonsBlock),
  
  
  

  // ⬇️ НОВЕ
  FeatureClubsSection: markRaw(FeatureClubsSection),
}

function parseProps(el) {
  if (!el?.dataset?.props) return {}
  try { return JSON.parse(el.dataset.props) }
  catch { return {} }
}

function mountIsland(el) {
  const key = el?.dataset?.vue
  const Comp = key && registry[key]
  if (!Comp) return
  if (el.__vue_app__ || el.dataset.vueMounted === '1') return

  const props = parseProps(el)
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

if (document.readyState === 'loading') {
  document.addEventListener('DOMContentLoaded', () => scanAndMount())
} else {
  scanAndMount()
}

const mo = new MutationObserver((mutations) => {
  for (const m of mutations) {
    m.addedNodes?.forEach((n) => {
      if (n.nodeType !== 1) return
      const el = n
      if (el.matches?.('[data-vue]')) mountIsland(el)
      const nested = el.querySelectorAll?.('[data-vue]')
      nested && nested.forEach(mountIsland)
    })
  }
})
mo.observe(document.documentElement, { subtree: true, childList: true })

window.__mountIslands = scanAndMount
