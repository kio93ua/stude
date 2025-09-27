<template>
  <section id="testimonials" class="bg-white py-16">
    <div class="mx-auto max-w-6xl px-6">
      <div class="mb-10 text-center">
        <h2 class="heading-2">{{ heading }}</h2>
        <p class="mt-3 text-muted">{{ subheading }}</p>
      </div>

      <div class="grid gap-8 md:grid-cols-3">
        <figure v-for="(t, i) in items" :key="i" class="flex h-full flex-col justify-between rounded-3xl border border-muted/40 bg-slate-50 p-8 shadow-sm">
          <blockquote class="text-sm text-muted">“{{ t.text }}”</blockquote>
          <figcaption class="mt-6 text-sm font-semibold text-primary">{{ t.name }}</figcaption>
        </figure>
      </div>
    </div>
  </section>
</template>

<script setup lang="ts">
withDefaults(defineProps<{
  heading?: string
  subheading?: string
  items?: { name: string; text: string }[]
}>(), {
  heading: 'Відгуки студентів',
  subheading: 'Реальні історії тих, хто вже досягнув своїх мовних цілей.',
  items: () => ([
    { name: 'Олена, 28 років', text: 'Готувалась до IELTS — отримала 7.5 і вже навчаюсь у Празі!' },
    { name: 'Антон, 34 роки', text: 'За 4 місяці провожу презентації англійською без страху.' },
    { name: 'Софія, 10 клас', text: 'Покращила оцінки та склала ЗНО на 189 балів.' },
  ]),
})
</script>
