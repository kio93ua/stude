<template>
  <section id="services" class="bg-slate-50 py-16">
    <div class="mx-auto max-w-6xl px-6">
      <div class="mb-10 text-center">
        <h2 class="heading-2">{{ heading }}</h2>
        <p class="mt-3 text-muted">{{ subheading }}</p>
      </div>

      <div class="grid gap-8 md:grid-cols-3">
        <article v-for="(card, i) in cards" :key="i" class="card flex h-full flex-col justify-between">
          <div class="space-y-4">
            <h3 class="text-xl font-semibold text-secondary">{{ card.title }}</h3>
            <p class="text-sm text-muted">{{ card.text }}</p>
            <ul class="space-y-2 text-sm text-muted">
              <li v-for="(li, j) in card.list" :key="j">{{ li }}</li>
            </ul>
          </div>
          <span class="mt-6 inline-flex w-max rounded-full bg-accent/15 px-4 py-2 text-xs font-semibold text-accent">
            {{ card.badge }}
          </span>
        </article>
      </div>
    </div>
  </section>
</template>

<script setup lang="ts">
withDefaults(defineProps<{
  heading?: string
  subheading?: string
  cards?: { title: string; text: string; list: string[]; badge: string }[]
}>(), {
  heading: 'Програми навчання',
  subheading: 'Обирайте напрям, який відповідає вашим цілям.',
  cards: () => ([
    { title: 'IELTS та міжнародні іспити', text: 'Комплексна підготовка...', list: ['12 тематичних модулів','Повні пробні тести','Аналіз типових помилок'], badge: 'від 450 грн/заняття' },
    { title: 'Розмовна та бізнес-англійська', text: 'Покращуємо словник...', list: ['1-на-1 або міні-групи','Реальні кейси','Speaking club'], badge: 'від 400 грн/заняття' },
    { title: 'Підтримка школярів', text: 'Допомога з Д/З...', list: ['Ігри та інтерактиви','Контроль успішності','Гнучкий графік'], badge: 'від 350 грн/заняття' },
  ]),
})
</script>
