<!-- resources/js/components/EnrollForm.vue -->
<template>
  <section
    ref="root"
    class="section-surface relative overflow-hidden py-16 md:py-20"
    aria-labelledby="enroll-heading"
  >
    <!-- делікатні плями, що не ламають фон -->
    <div
      ref="blobTL" data-blob="tl" aria-hidden="true"
      class="pointer-events-none absolute -top-24 -left-24 h-72 w-72 rounded-full blur-3xl opacity-10 mix-blend-multiply"
      style="background: radial-gradient(closest-side, rgba(15,166,160,.55), transparent 70%);"
    ></div>
    <div
      ref="blobBR" data-blob="br" aria-hidden="true"
      class="pointer-events-none absolute -bottom-28 -right-32 h-80 w-80 rounded-full blur-3xl opacity-10 mix-blend-multiply"
      style="background: radial-gradient(closest-side, rgba(15,166,160,.45), transparent 70%);"
    ></div>

    <div class="mx-auto max-w-6xl px-6">
      <header class="mx-auto mb-10 text-center max-w-3xl">
        <!-- за бажанням: бейдж у стилі інших секцій -->
        <!-- <p class="badge-muted font-display mb-3 inline-block">Залишити заявку</p> -->
        <h2 id="enroll-heading" class="heading-1 font-display tracking-tight text-secondary">
          Хочете почати навчання або просто дізнатися більше?
        </h2>
        <p class="mt-3 text-secondary/85">
          Заповніть коротку форму — ми повернемося до вас протягом дня. Підкажемо, з чого стартувати,
          порекомендуємо програму під ваш рівень і відповімо на всі запитання.
        </p>
      </header>

      <!-- live region (присутня в DOM постійно) -->
      <div
        v-show="messages.length"
        :class="toastKind === 'error'
          ? 'bg-red-50 text-red-800 ring-red-200'
          : 'bg-emerald-50 text-emerald-800 ring-emerald-200'"
        class="mb-5 rounded-xl ring-1 px-4 py-3"
        role="alert"
      >
        <ul class="list-disc pl-5 space-y-1">
          <li v-for="(m, i) in messages" :key="i">{{ m }}</li>
        </ul>
      </div>

      <form
        @submit.prevent="onSubmit"
        class="rounded-2xl bg-white/80 backdrop-blur ring-1 ring-slate-200 shadow-[0_8px_30px_rgba(0,0,0,0.06)] p-6 md:p-8"
        novalidate
      >
        <!-- широка, гнучка сітка -->
        <fieldset class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-4">
          <!-- ПІБ (ширше поле) -->
          <div class="sm:col-span-2">
            <label for="fullName" class="block text-sm font-medium text-slate-700">
              Кого записують (ПІБ) <span class="text-rose-600">*</span>
            </label>
            <input
              id="fullName" name="fullName" type="text" required
              v-model.trim="form.fullName"
              class="mt-1 block w-full rounded-xl border-slate-300 shadow-sm focus:border-teal-500 focus:ring-teал-500"
              aria-describedby="fullName_help"
            />
            <p id="fullName_help" class="mt-1 text-xs text-slate-500">Вкажіть прізвище та ім’я.</p>
          </div>

          <!-- Вік -->
          <div>
            <label for="age" class="block text-sm font-medium text-slate-700">
              Вік <span class="text-rose-600">*</span>
            </label>
            <input
              id="age" name="age" type="number" min="5" max="100" required
              v-model.number="form.age"
              class="mt-1 block w-full rounded-xl border-slate-300 shadow-sm focus:border-teал-500 focus:ring-teал-500"
              aria-describedby="age_help"
            />
            <p id="age_help" class="mt-1 text-xs text-slate-500">Мінімум 5 років.</p>
          </div>

          <!-- Рівень -->
          <div>
            <label for="level" class="block text-sm font-medium text-slate-700">
              Рівень <span class="text-rose-600">*</span>
            </label>
            <select
              id="level" name="level" required
              v-model="form.level"
              class="mt-1 block w-full rounded-xl border-slate-300 shadow-sm focus:border-teал-500 focus:ring-teал-500"
            >
              <option disabled value="">Оберіть рівень</option>
              <option>A1</option><option>A2</option><option>B1</option>
              <option>B2</option><option>C1</option><option>Не знаю</option>
            </select>
          </div>

          <!-- Телефон -->
          <div>
            <label for="phone" class="block text-sm font-medium text-slate-700">
              Телефон <span class="text-rose-600">*</span>
            </label>
            <input
              id="phone" name="phone" type="tel" required
              v-model.trim="form.phone"
              class="mt-1 block w-full rounded-xl border-slate-300 shadow-sm focus:border-teал-500 focus:ring-teал-500"
              placeholder="+380..."
            />
          </div>

          <!-- Email -->
          <div>
            <label for="email" class="block text-sm font-medium text-slate-700">
              Email <span class="text-rose-600">*</span>
            </label>
            <input
              id="email" name="email" type="email" required
              v-model.trim="form.email"
              class="mt-1 block w-full rounded-xl border-slate-300 shadow-sm focus:border-teал-500 focus:ring-teал-500"
              placeholder="you@example.com"
              inputmode="email" autocomplete="email"
            />
          </div>

          <!-- Мета звернення (радіо) -->
          <div class="sm:col-span-2 lg:col-span-4">
            <fieldset>
              <legend class="block text-sm font-medium text-slate-700">
                Мета звернення <span class="text-rose-600">*</span>
              </legend>
              <p id="intent_help" class="mt-1 text-xs text-slate-500">
                Оберіть, що саме потрібно: безкоштовна консультація, пробний урок чи просто інформація.
              </p>
              <div class="mt-3 grid grid-cols-1 gap-3 sm:grid-cols-3">
                <label class="flex items-center gap-3 rounded-xl border border-slate-200 bg-white/70 px-4 py-3 shadow-sm hover:border-teal-300 focus-within:ring-2 focus-within:ring-teал-500">
                  <input
                    type="radio" name="intent" value="consultation" required
                    v-model="form.intent"
                    class="h-5 w-5 rounded text-teal-600 focus:ring-teал-500"
                    aria-describedby="intent_help"
                  />
                  <span class="text-sm font-medium text-slate-700">Консультація</span>
                </label>
                <label class="flex items-center gap-3 rounded-xl border border-slate-200 bg-white/70 px-4 py-3 shadow-sm hover:border-teал-300 focus-within:ring-2 focus-within:ring-teал-500">
                  <input
                    type="radio" name="intent" value="first_lesson" required
                    v-model="form.intent"
                    class="h-5 w-5 rounded text-teal-600 focus:ring-teал-500"
                    aria-describedby="intent_help"
                  />
                  <span class="text-sm font-medium text-slate-700">Перший урок</span>
                </label>
                <label class="flex items-center gap-3 rounded-xl border border-slate-200 bg-white/70 px-4 py-3 shadow-sm hover:border-teал-300 focus-within:ring-2 focus-within:ring-teал-500">
                  <input
                    type="radio" name="intent" value="info" required
                    v-model="form.intent"
                    class="h-5 w-5 rounded text-teal-600 focus:ring-teал-500"
                    aria-describedby="intent_help"
                  />
                  <span class="text-sm font-medium text-slate-700">Дізнатись інформацію</span>
                </label>
              </div>
            </fieldset>
          </div>

          <!-- Додаткові питання -->
          <div class="sm:col-span-2 lg:col-span-4">
            <label for="questions" class="block text-sm font-medium text-slate-700">
              Додаткові питання
            </label>
            <textarea
              id="questions" name="questions" rows="4"
              v-model.trim="form.questions"
              class="mt-1 block w-full rounded-xl border-slate-300 shadow-sm focus:border-teал-500 focus:ring-teал-500"
              placeholder="Коротко опишіть цілі, побажання або запитайте, що цікавить."
            ></textarea>
          </div>

          <!-- Згода -->
          <div class="sm:col-span-2 lg:col-span-4">
            <div class="flex items-start gap-3">
              <input
                id="consent" name="consent" type="checkbox" required
                v-model="form.consent"
                class="mt-1 h-5 w-5 rounded border-slate-300 text-teal-600 focus:ring-teал-500"
                aria-describedby="consent_help"
              />
              <div>
                <label for="consent" class="text-sm font-medium text-slate-700">
                  Згода на обробку персональних даних <span class="text-rose-600">*</span>
                </label>
                <p id="consent_help" class="mt-1 text-xs text-slate-500">
                  Погоджуюсь на обробку моїх даних для зв’язку щодо навчання/консультації.
                </p>
              </div>
            </div>
          </div>

          <!-- Submit -->
          <div class="sm:col-span-2 lg:col-span-4 pt-2">
            <button
              type="submit"
              class="inline-flex w-full items-center justify-center rounded-xl bg-gradient-to-r from-[#0C7C78] to-[#0FA6A0]
                     px-5 py-3 font-semibold text-white shadow-sm ring-1 ring-transparent
                     transition will-change-transform
                     hover:-translate-y-0.5 hover:ring-teал-200
                     focus:outline-none focus:ring-2 focus:ring-teал-500 focus:ring-offset-2"
              :disabled="submitting"
            >
              <span v-if="!submitting">Надіслати заявку</span>
              <span v-else class="opacity-80">Надсилання…</span>
            </button>
          </div>
        </fieldset>
      </form>
    </div>
  </section>
</template>


<script setup lang="ts">
import { ref, onMounted, onBeforeUnmount, nextTick } from 'vue'

type Intent = 'consultation' | 'first_lesson' | 'info' | ''
type Payload = {
  fullName: string
  age: number | null
  level: string
  phone: string
  email: string
  intent: Intent
  questions: string
  consent: boolean
}

const emit = defineEmits<{ (e: 'submit', payload: Payload): void }>()

// refs
const root   = ref<HTMLElement | null>(null)
const blobTL = ref<HTMLElement | null>(null)
const blobBR = ref<HTMLElement | null>(null)

// form state
const form = ref<Payload>({
  fullName: '',
  age: null,
  level: '',
  phone: '',
  email: '',
  intent: '',
  questions: '',
  consent: false,
})

const messages   = ref<string[]>([])
const toastKind  = ref<'error' | 'success'>('error')
const submitting = ref(false)

// simple email pattern
const emailRe = /^[^\s@]+@[^\s@]+\.[^\s@]+$/

// gsap handles
let ctx: any = null
let mouseHandler: ((e: MouseEvent) => void) | null = null
let gsapMod: any | null = null

onMounted(async () => {
  // respect reduced motion
  if (window.matchMedia('(prefers-reduced-motion: reduce)').matches) return

  await nextTick()
  const scopeEl = root.value
  if (!scopeEl || !scopeEl.isConnected) return

  // dynamic import + plugin registration
  const { default: gsap } = await import('gsap')
  const { ScrollTrigger } = await import('gsap/ScrollTrigger')
  gsap.registerPlugin(ScrollTrigger)
  gsapMod = gsap

  // create animations strictly inside scoped context (prevents "Invalid scope")
  ctx = gsapMod.context(() => {
    const $ = gsapMod.utils.selector(scopeEl)
    const tl = $('[data-blob="tl"]')[0]
    const br = $('[data-blob="br"]')[0]

    if (tl) {
      gsapMod.to(tl, {
        yPercent: -8,
        ease: 'none',
        scrollTrigger: {
          trigger: scopeEl,
          start: 'top bottom',
          end: 'bottom top',
          scrub: 0.3,
        },
      })
    }
    if (br) {
      gsapMod.to(br, {
        yPercent: 10,
        ease: 'none',
        scrollTrigger: {
          trigger: scopeEl,
          start: 'top bottom',
          end: 'bottom top',
          scrub: 0.3,
        },
      })
    }
  }, scopeEl) // scope to our section

  // tiny mouse-parallax (no await inside)
  const safeTo = (el: HTMLElement | null, vars: Record<string, number>) => {
    if (!el || !gsapMod) return
    gsapMod.to(el, { ...vars, duration: 0.6, overwrite: true })
  }

  mouseHandler = (e: MouseEvent) => {
    const r = scopeEl.getBoundingClientRect()
    const cx = r.left + r.width / 2
    const cy = r.top + r.height / 2
    const dx = (e.clientX - cx) / r.width
    const dy = (e.clientY - cy) / r.height
    safeTo(blobTL.value, { x: dx * -15, y: dy * -15 })
    safeTo(blobBR.value, { x: dx *  20, y: dy *  18 })
  }
  scopeEl.addEventListener('mousemove', mouseHandler, { passive: true })
})

onBeforeUnmount(() => {
  try { ctx?.revert?.() } catch {}
  ctx = null

  const scopeEl = root.value
  if (scopeEl && mouseHandler) {
    scopeEl.removeEventListener('mousemove', mouseHandler)
  }
  mouseHandler = null
})

function validate(): string[] {
  const errs: string[] = []
  if (!form.value.fullName) errs.push('Вкажіть ПІБ.')
  const age = form.value.age ?? 0
  if (!age || age < 5 || age > 100) errs.push('Вік має бути від 5 до 100.')
  if (!form.value.level) errs.push('Оберіть рівень.')
  if (!form.value.phone) errs.push('Вкажіть телефон.')
  if (!form.value.email || !emailRe.test(form.value.email)) errs.push('Вкажіть коректний email.')
  if (!form.value.intent) errs.push('Оберіть мету звернення.')
  if (!form.value.consent) errs.push('Підтвердіть згоду на обробку персональних даних.')
  return errs
}

async function onSubmit() {
  messages.value = []
  toastKind.value = 'error'
  const errs = validate()
  if (errs.length) { messages.value = errs; return }

  submitting.value = true
  try {
    const payload: Payload = { ...form.value }

    // ---- REAL POST TO BACKEND ----
    const csrf = (document.querySelector('meta[name="csrf-token"]') as HTMLMetaElement)?.content ?? ''
    const res = await fetch('/enroll', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': csrf,
        'Accept': 'application/json',
      },
      credentials: 'same-origin',
      body: JSON.stringify(payload),
    })

    if (!res.ok) {
      // 422 з валідації або інша помилка
      let errMsg = 'Помилка під час надсилання форми'
      try {
        const data = await res.json()
        if (data?.message) errMsg = data.message
        if (data?.errors) {
          const flat = Object.values<string[]>(data.errors).flat()
          if (flat.length) {
            messages.value = flat
            return
          }
        }
      } catch { /* ignore parse */ }
      throw new Error(errMsg)
    }

    // опціонально: повідомити батьківський компонент
    emit('submit', payload)

    // успіх
    toastKind.value = 'success'
    messages.value = ['Дякуємо! Заявку надіслано — ми скоро зв’яжемося.']

    // очистити форму
    form.value = {
      fullName: '',
      age: null,
      level: '',
      phone: '',
      email: '',
      intent: '',
      questions: '',
      consent: false,
    }
  } catch (e: any) {
    messages.value = [e?.message || 'Сталася помилка. Спробуйте ще раз.']
  } finally {
    submitting.value = false
  }
}
</script>

