@extends('layouts.app')

@section('content')
<section id="hero" class="overflow-hidden bg-gradient-to-br from-indigo-50 via-white to-slate-100">
    <div class="mx-auto grid max-w-6xl gap-10 px-6 pb-16 pt-20 md:grid-cols-2 md:items-center">
        <div class="space-y-6">
            <span class="inline-flex items-center gap-2 rounded-full bg-indigo-100 px-4 py-1 text-sm font-semibold text-indigo-700">Індивідуальні заняття з англійської</span>
            <h1 class="text-4xl font-bold tracking-tight text-slate-900 md:text-5xl">Допоможу заговорити англійською впевнено вже за 3 місяці</h1>
            <p class="text-lg text-slate-600">Я — репетитор з 8-річним досвідом підготовки до IELTS, розмовної практики та бізнес-англійської. Працюю з підлітками та дорослими, комбіную сучасні матеріали та живе спілкування.</p>
            <div class="flex flex-col gap-3 sm:flex-row">
                <a href="#contact" class="inline-flex items-center justify-center rounded-full bg-indigo-600 px-6 py-3 text-sm font-semibold text-white shadow-lg shadow-indigo-200 transition hover:bg-indigo-500">Запис на пробний урок</a>
                <a href="#services" class="inline-flex items-center justify-center rounded-full border border-indigo-200 px-6 py-3 text-sm font-semibold text-indigo-600 transition hover:border-indigo-400">Дивитися програми</a>
            </div>
        </div>
        <div class="relative">
            <div class="absolute -left-10 top-10 hidden h-24 w-24 rounded-full bg-indigo-200/60 blur-3xl md:block"></div>
            <div class="absolute -right-8 bottom-4 hidden h-20 w-20 rounded-full bg-fuchsia-200/50 blur-2xl md:block"></div>
            <div class="relative rounded-3xl bg-white p-6 shadow-xl shadow-indigo-100">
                <div class="space-y-4">
                    <h2 class="text-lg font-semibold text-slate-800">Що ви отримаєте</h2>
                    <ul class="space-y-3 text-sm text-slate-600">
                        <li class="flex items-start gap-3"><span class="mt-1 h-2.5 w-2.5 rounded-full bg-indigo-500"></span>Онлайн та офлайн заняття у зручному графіку</li>
                        <li class="flex items-start gap-3"><span class="mt-1 h-2.5 w-2.5 rounded-full bg-indigo-500"></span>Персональний план під ваш рівень та цілі</li>
                        <li class="flex items-start gap-3"><span class="mt-1 h-2.5 w-2.5 rounded-full bg-indigo-500"></span>Цифрові матеріали, домашні завдання та регулярний зворотний зв’язок</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</section>

<section class="bg-white py-12" aria-label="Ключові показники">
    <div class="mx-auto grid max-w-5xl gap-8 px-6 text-center sm:grid-cols-3">
        <div class="rounded-2xl border border-slate-200 bg-slate-50 px-6 py-8 shadow-sm">
            <p class="text-4xl font-bold text-indigo-600">150+</p>
            <p class="mt-2 text-sm font-medium text-slate-600">задоволених студентів</p>
        </div>
        <div class="rounded-2xl border border-slate-200 bg-slate-50 px-6 py-8 shadow-sm">
            <p class="text-4xl font-bold text-indigo-600">8 років</p>
            <p class="mt-2 text-sm font-medium text-slate-600">викладання англійської</p>
        </div>
        <div class="rounded-2xl border border-slate-200 bg-slate-50 px-6 py-8 shadow-sm">
            <p class="text-4xl font-bold text-indigo-600">97%</p>
            <p class="mt-2 text-sm font-medium text-slate-600">успішних складань IELTS</p>
        </div>
    </div>
</section>

<section id="services" class="bg-slate-50 py-16">
    <div class="mx-auto max-w-6xl px-6">
        <div class="mb-10 text-center">
            <h2 class="text-3xl font-semibold text-slate-900">Програми навчання</h2>
            <p class="mt-3 text-slate-600">Обирайте напрям, який відповідає вашим цілям. Кожен курс адаптується за рівнем володіння мовою.</p>
        </div>
        <div class="grid gap-8 md:grid-cols-3">
            <article class="flex h-full flex-col justify-between rounded-3xl border border-slate-200 bg-white p-8 shadow-md shadow-indigo-50">
                <div class="space-y-4">
                    <h3 class="text-xl font-semibold text-indigo-700">IELTS та міжнародні іспити</h3>
                    <p class="text-sm text-slate-600">Комплексна підготовка до IELTS, TOEFL, Cambridge. Робимо акцент на стратегії, відпрацюванні письма та спікіґу.</p>
                    <ul class="space-y-2 text-sm text-slate-600">
                        <li>12 тематичних модулів</li>
                        <li>Повні пробні тести</li>
                        <li>Аналіз типових помилок</li>
                    </ul>
                </div>
                <span class="mt-6 inline-flex w-max rounded-full bg-indigo-100 px-4 py-2 text-xs font-semibold text-indigo-700">від 450 грн/заняття</span>
            </article>
            <article class="flex h-full flex-col justify-between rounded-3xl border border-slate-200 bg-white p-8 shadow-md shadow-indigo-50">
                <div class="space-y-4">
                    <h3 class="text-xl font-semibold text-indigo-700">Розмовна та бізнес-англійська</h3>
                    <p class="text-sm text-slate-600">Покращуємо словниковий запас, вимову й навички комунікації для подорожей, роботи та презентацій.</p>
                    <ul class="space-y-2 text-sm text-slate-600">
                        <li>Заняття 1-на-1 або міні-групи</li>
                        <li>Реальні кейси та рольові ігри</li>
                        <li>Щотижневий speaking club</li>
                    </ul>
                </div>
                <span class="mt-6 inline-flex w-max rounded-full bg-indigo-100 px-4 py-2 text-xs font-semibold text-indigo-700">від 400 грн/заняття</span>
            </article>
            <article class="flex h-full flex-col justify-between rounded-3xl border border-slate-200 bg-white p-8 shadow-md shadow-indigo-50">
                <div class="space-y-4">
                    <h3 class="text-xl font-semibold text-indigo-700">Підтримка школярів</h3>
                    <p class="text-sm text-slate-600">Допомога з домашніми завданнями, підготовка до ДПА та ЗНО. Пояснюю граматику просто й на прикладах.</p>
                    <ul class="space-y-2 text-sm text-slate-600">
                        <li>Навчальні ігри та інтерактиви</li>
                        <li>Контроль успішності батькам</li>
                        <li>Гнучкий графік занять</li>
                    </ul>
                </div>
                <span class="mt-6 inline-flex w-max rounded-full bg-indigo-100 px-4 py-2 text-xs font-semibold text-indigo-700">від 350 грн/заняття</span>
            </article>
        </div>
    </div>
</section>

<section id="about" class="bg-white py-16">
    <div class="mx-auto max-w-6xl grid gap-12 px-6 md:grid-cols-[1.1fr_0.9fr] md:items-center">
        <div class="space-y-6">
            <h2 class="text-3xl font-semibold text-slate-900">Привіт! Я Марія Коваль</h2>
            <p class="text-slate-600">Закінчила КНУ ім. Шевченка, факультет іноземних мов. Сертифікована викладачка CELTA. Працювала в мовних школах Києва та онлайнових платформах, нині веду власну студію.</p>
            <p class="text-slate-600">Мій підхід — це баланс структурованої граматики та інтерактивної практики. Використовую автентичні матеріали, подкасти, відео та проводжу регулярні speaking сесії.</p>
            <dl class="grid gap-4 text-sm text-slate-600 sm:grid-cols-2">
                <div>
                    <dt class="font-semibold text-slate-800">Сертифікати</dt>
                    <dd>CELTA, IELTS Academic 8.5</dd>
                </div>
                <div>
                    <dt class="font-semibold text-slate-800">Формат роботи</dt>
                    <dd>Zoom, Google Meet, офлайн у Києві</dd>
                </div>
                <div>
                    <dt class="font-semibold text-slate-800">Додатково</dt>
                    <dd>Навчальні платформи, чат підтримка в Telegram</dd>
                </div>
                <div>
                    <dt class="font-semibold text-slate-800">Мова викладання</dt>
                    <dd>Українська / English only</dd>
                </div>
            </dl>
        </div>
        <div class="relative">
            <div class="absolute inset-0 -z-10 rounded-3xl bg-gradient-to-br from-indigo-200 via-purple-100 to-slate-100 blur-2xl"></div>
            <div class="overflow-hidden rounded-3xl border border-white/40 bg-white/80 p-8 shadow-xl shadow-indigo-100">
                <blockquote class="space-y-4">
                    <p class="text-lg font-medium text-slate-700">«Я вірю, що кожен може опанувати англійську, якщо навчання побудовано з любов’ю до мови та увагою до особистості студента».</p>
                    <footer class="text-sm font-semibold text-indigo-700">Марія Коваль</footer>
                </blockquote>
            </div>
        </div>
    </div>
</section>

<section id="approach" class="bg-slate-50 py-16">
    <div class="mx-auto max-w-6xl px-6">
        <div class="mb-10 text-center">
            <h2 class="text-3xl font-semibold text-slate-900">Як проходить навчання</h2>
            <p class="mt-3 text-slate-600">Прозора структура занять допомагає бачити прогрес кожного тижня.</p>
        </div>
        <div class="grid gap-6 md:grid-cols-4">
            @foreach ([
                ['title' => 'Знайомство', 'desc' => 'Безкоштовна діагностика рівня, визначення цілей та графіку занять.'],
                ['title' => 'Планування', 'desc' => 'Створюю індивідуальний силлабус, підбираю матеріали й завдання.'],
                ['title' => 'Практика', 'desc' => 'Заняття з фокусом на говоріння, граматику, аудіювання та письмо.'],
                ['title' => 'Аналіз результатів', 'desc' => 'Щомісяця — прогрес-репорт, корекція плану та поради для самоосвіти.'],
            ] as $index => $step)
            <div class="relative rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
                <span class="absolute -top-4 left-6 inline-flex h-10 w-10 items-center justify-center rounded-full bg-indigo-600 text-sm font-semibold text-white shadow-md">{{ $index + 1 }}</span>
                <h3 class="mt-6 text-lg font-semibold text-slate-900">{{ $step['title'] }}</h3>
                <p class="mt-3 text-sm text-slate-600">{{ $step['desc'] }}</p>
            </div>
            @endforeach
        </div>
    </div>
</section>

<section id="testimonials" class="bg-white py-16">
    <div class="mx-auto max-w-6xl px-6">
        <div class="mb-10 text-center">
            <h2 class="text-3xl font-semibold text-slate-900">Відгуки студентів</h2>
            <p class="mt-3 text-slate-600">Реальні історії тих, хто вже досягнув своїх мовних цілей.</p>
        </div>
        <div class="grid gap-8 md:grid-cols-3">
            @foreach ([
                ['name' => 'Олена, 28 років', 'text' => 'Готувалась до IELTS для навчання за кордоном. Марія дала чітку структуру та купу практики — отримала 7.5 і вже вчусь у Празі!'],
                ['name' => 'Антон, 34 роки', 'text' => 'Потрібна була бізнес-англійська для переговорів. Після 4 місяців занять проводжу презентації англійською без страху.'],
                ['name' => 'Софія, 10 клас', 'text' => 'Завдяки урокам покращила оцінки та склала ЗНО на 189 балів. Дуже подобаються інтерактивні вправи та підтримка.'],
            ] as $testimonial)
            <figure class="flex h-full flex-col justify-between rounded-3xl border border-slate-200 bg-slate-50 p-8 shadow-sm">
                <blockquote class="text-sm text-slate-600">“{{ $testimonial['text'] }}”</blockquote>
                <figcaption class="mt-6 text-sm font-semibold text-indigo-700">{{ $testimonial['name'] }}</figcaption>
            </figure>
            @endforeach
        </div>
    </div>
</section>

<section class="bg-indigo-600 py-16 text-white">
    <div class="mx-auto flex max-w-6xl flex-col items-center gap-6 px-6 text-center">
        <h2 class="text-3xl font-semibold">Готові зробити наступний крок?</h2>
        <p class="max-w-2xl text-indigo-100">Залишайте свої контакти — я надішлю коротку анкету та запропоную час для безкоштовного ознайомчого заняття.</p>
        <a href="#contact" class="rounded-full bg-white px-6 py-3 text-sm font-semibold text-indigo-600 shadow-md shadow-indigo-900/20 transition hover:bg-indigo-100">Заповнити форму</a>
    </div>
</section>

<section id="contact" class="bg-white py-16">
    <div class="mx-auto max-w-6xl px-6">
        <div class="grid gap-12 md:grid-cols-2">
            <div class="space-y-6">
                <h2 class="text-3xl font-semibold text-slate-900">Залиште заявку на безкоштовну консультацію</h2>
                <p class="text-slate-600">Розкажіть про свої цілі — я підберу програму та надішлю перші матеріали. Зв’язуюсь протягом робочого дня.</p>
                <div class="rounded-3xl border border-indigo-100 bg-indigo-50 p-6 text-sm text-indigo-800">
                    <p class="font-semibold">Працюємо Пн–Сб, 09:00–20:00</p>
                    <p class="mt-2">Телефон: <a href="tel:+380671234567" class="underline decoration-indigo-500">+38 (067) 123 45 67</a></p>
                    <p>Email: <a href="mailto:hello@studytutor.com" class="underline decoration-indigo-500">hello@studytutor.com</a></p>
                    <p class="mt-2">Telegram: <a href="https://t.me/studytutor" class="underline decoration-indigo-500" target="_blank" rel="noopener">@studytutor</a></p>
                </div>
            </div>
            <form action="#" method="post" class="rounded-3xl border border-slate-200 bg-slate-50 p-8 shadow-sm">
                <div class="grid gap-5">
                    <div>
                        <label for="name" class="block text-sm font-semibold text-slate-700">Ім’я</label>
                        <input id="name" name="name" type="text" placeholder="Ваше ім’я" class="mt-2 w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm text-slate-700 shadow-inner focus:border-indigo-400 focus:outline-none" required>
                    </div>
                    <div>
                        <label for="email" class="block text-sm font-semibold text-slate-700">Email</label>
                        <input id="email" name="email" type="email" placeholder="name@gmail.com" class="mt-2 w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm text-slate-700 shadow-inner focus:border-indigo-400 focus:outline-none" required>
                    </div>
                    <div>
                        <label for="level" class="block text-sm font-semibold text-slate-700">Ваш рівень</label>
                        <select id="level" name="level" class="mt-2 w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm text-slate-700 shadow-inner focus:border-indigo-400 focus:outline-none">
                            <option value="">Оберіть</option>
                            <option value="beginner">Beginner (A1-A2)</option>
                            <option value="intermediate">Intermediate (B1-B2)</option>
                            <option value="advanced">Advanced (C1+)</option>
                        </select>
                    </div>
                    <div>
                        <label for="goal" class="block text-sm font-semibold text-slate-700">Мета навчання</label>
                        <textarea id="goal" name="goal" rows="4" placeholder="Напишіть, чого хочете досягти" class="mt-2 w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm text-slate-700 shadow-inner focus:border-indigo-400 focus:outline-none"></textarea>
                    </div>
                    <button type="submit" class="rounded-full bg-indigo-600 px-6 py-3 text-sm font-semibold text-white shadow-lg shadow-indigo-200 transition hover:bg-indigo-500">Надіслати заявку</button>
                </div>
                <p class="mt-4 text-xs text-slate-500">Натискаючи «Надіслати», ви погоджуєтесь на обробку персональних даних.</p>
            </form>
        </div>
    </div>
</section>
@endsection
