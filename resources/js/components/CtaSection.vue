<template>
  <section class="bg-secondary py-16 text-white">
    <div class="mx-auto flex max-w-6xl flex-col items-center gap-6 px-6 text-center">
      <h2 class="text-3xl font-semibold">{{ title }}</h2>
      <p class="max-w-2xl text-white/80">{{ text }}</p>
      <a :href="href" class="rounded-full bg-white px-6 py-3 text-sm font-semibold text-secondary shadow-md shadow-primary/20 transition hover:bg-muted">
        {{ button }}
      </a>
    </div>
  </section>
</template>

<script setup lang="ts">
withDefaults(defineProps<{
  title?: string
  text?: string
  button?: string
  href?: string
}>(), {
  title: 'Готові зробити наступний крок?',
  text: 'Залиште контакти — надішлю коротку анкету та запропоную час для безкоштовного ознайомчого заняття.',
  button: 'Заповнити форму',
  href: '#contact',
})
</script>
