<template>
  <section id="contact" class="bg-white py-16">
    <div class="mx-auto max-w-6xl px-6">
      <div class="grid gap-12 md:grid-cols-2">
        <div class="space-y-6">
          <h2 class="heading-2">{{ heading }}</h2>
          <p class="text-muted">{{ text }}</p>
          <div class="rounded-3xl border border-muted/40 bg-muted/20 p-6 text-sm text-secondary">
            <p class="font-semibold">Пн–Сб, 09:00–20:00</p>
            <p class="mt-2">Телефон: <a :href="`tel:${phoneHref}`" class="underline">{{ phone }}</a></p>
            <p>Email: <a :href="`mailto:${email}`" class="underline">{{ email }}</a></p>
            <p class="mt-2">Telegram: <a :href="telegram" target="_blank" rel="noopener" class="underline">{{ telegramLabel }}</a></p>
          </div>
        </div>

        <form class="card-soft grid gap-5" @submit.prevent="onSubmit">
          <div>
            <label class="block text-sm font-semibold text-secondary">Ім’я</label>
            <input v-model="form.name" type="text" placeholder="Ваше ім’я"
                   class="mt-2 w-full rounded-2xl border border-muted bg-white px-4 py-3 text-sm text-secondary shadow-inner focus:border-primary focus:outline-none" required>
          </div>
          <div>
            <label class="block text-sm font-semibold text-secondary">Email</label>
            <input v-model="form.email" type="email" placeholder="name@gmail.com"
                   class="mt-2 w-full rounded-2xl border border-muted bg-white px-4 py-3 text-sm text-secondary shadow-inner focus:border-primary focus:outline-none" required>
          </div>
          <div>
            <label class="block text-sm font-semibold text-secondary">Ваш рівень</label>
            <select v-model="form.level"
                    class="mt-2 w-full rounded-2xl border border-muted bg-white px-4 py-3 text-sm text-secondary shadow-inner focus:border-primary focus:outline-none">
              <option value="">Оберіть</option>
              <option value="beginner">Beginner (A1-A2)</option>
              <option value="intermediate">Intermediate (B1-B2)</option>
              <option value="advanced">Advanced (C1+)</option>
            </select>
          </div>
          <div>
            <label class="block text-sm font-semibold text-secondary">Мета навчання</label>
            <textarea v-model="form.goal" rows="4" placeholder="Напишіть, чого хочете досягти"
                      class="mt-2 w-full rounded-2xl border border-muted bg-white px-4 py-3 text-sm text-secondary shadow-inner focus:border-primary focus:outline-none"></textarea>
          </div>
          <button class="btn-primary" type="submit">Надіслати заявку</button>
          <p class="text-xs text-slate-500">Натискаючи «Надіслати», ви погоджуєтесь на обробку персональних даних.</p>
        </form>
      </div>
    </div>
  </section>
</template>

<script setup lang="ts">
import { reactive, computed } from 'vue'

const props = withDefaults(defineProps<{
  heading?: string
  text?: string
  phone?: string
  email?: string
  telegram?: string
  telegramLabel?: string
}>(), {
  heading: 'Залиште заявку на безкоштовну консультацію',
  text: 'Розкажіть про свої цілі — підберу програму та надішлю перші матеріали.',
  phone: '+380671234567',
  email: 'hello@studytutor.com',
  telegram: 'https://t.me/studytutor',
  telegramLabel: '@studytutor',
})

const form = reactive({ name: '', email: '', level: '', goal: '' })
const phoneHref = computed(() => props.phone.replace(/[^+\d]/g, ''))

function onSubmit() {
  // TODO: fetch('/contact', { method:'POST', body: JSON.stringify(form) })
  alert('Заявка відправлена (демо).')
}
</script>
