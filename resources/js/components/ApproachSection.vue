<template>
  <section id="approach" class="bg-slate-50 py-16">
    <div class="mx-auto max-w-6xl px-6">
      <div class="mb-10 text-center">
        <h2 class="heading-2">{{ heading }}</h2>
        <p class="mt-3 text-muted">{{ subheading }}</p>
      </div>

      <div class="grid gap-6 md:grid-cols-4">
        <div
          v-for="(step, i) in steps"
          :key="i"
          class="relative rounded-3xl border border-muted/40 bg-white p-6 shadow-sm">
          <span class="absolute -top-4 left-6 inline-flex h-10 w-10 items-center justify-center rounded-full bg-primary text-sm font-semibold text-white shadow-md">{{ i + 1 }}</span>
          <h3 class="mt-6 text-lg font-semibold text-secondary">{{ step.title }}</h3>
          <p class="mt-3 text-sm text-muted">{{ step.desc }}</p>
        </div>
      </div>
    </div>
  </section>
</template>

<script setup lang="ts">
withDefaults(defineProps<{
  heading?: string
  subheading?: string
  steps?: { title: string; desc: string }[]
}>(), {
  heading: 'Як проходить навчання',
  subheading: 'Прозора структура занять допомагає бачити прогрес кожного тижня.',
  steps: () => ([
    { title: 'Знайомство', desc: 'Безкоштовна діагностика рівня, визначення цілей та графіку.' },
    { title: 'Планування', desc: 'Індивідуальний силлабус, підбір матеріалів і завдань.' },
    { title: 'Практика', desc: 'Фокус на speaking, граматику, аудіювання та письмо.' },
    { title: 'Аналіз результатів', desc: 'Щомісячний прогрес-репорт і корекція плану.' },
  ]),
})
</script>
