import { createApp } from 'vue'

const registry = {
  HeroSection: () => import('./components/HeroSection.vue'),
  ProgramsSection: () => import('./components/ProgramsSection.vue'),
  AboutSection: () => import('./components/AboutSection.vue'),
  ApproachSection: () => import('./components/ApproachSection.vue'),
  TestimonialsSection: () => import('./components/TestimonialsSection.vue'),
  CtaSection: () => import('./components/CtaSection.vue'),
  ContactSection: () => import('./components/ContactSection.vue'),
};

document.querySelectorAll('[data-vue]').forEach(async (el) => {
  const name = el.dataset.vue;
  if (!name || !registry[name]) return;

  let props = {};
  const raw = el.dataset.props;
  if (raw) { try { props = JSON.parse(raw); } catch (e) { } }

  try {
    const mod = await registry[name]();
    const Comp = mod.default;
    const app = createApp(Comp, props);
    app.mount(el);
  } catch (e) {
    console.error(`[islands] Failed to mount ${name}`, e);
  }
});
