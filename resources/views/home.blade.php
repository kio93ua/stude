@extends('layouts.app')

@section('title','Головна')

@section('content')

{{-- === HERO (Vue) === --}}
@php
    /** @var \App\Settings\HomePageSettings $s */
    $s = app(\App\Settings\HomePageSettings::class);

    // URL картинки: http(s) або файл у "public"
    $heroImageUrl = null;
    if (!empty($s->hero_image_path)) {
        $heroImageUrl = \Illuminate\Support\Str::startsWith($s->hero_image_path, ['http://','https://'])
            ? $s->hero_image_path
            : \Illuminate\Support\Facades\Storage::disk('public')->url($s->hero_image_path);
    }

    // Нормалізуємо bullets ДО масиву рядків
    $rawBullets = $s->hero_bullets ?? [];
    $bullets = array_values(array_filter(
        is_array($rawBullets)
            ? array_map(fn ($v) => is_string($v) ? trim($v) : (is_array($v) && isset($v[0]) ? trim((string)$v[0]) : ''), $rawBullets)
            : (is_string($rawBullets) ? preg_split('/\s*,\s*/', $rawBullets, -1, PREG_SPLIT_NO_EMPTY) : []),
        fn ($v) => $v !== ''
    ));

    $heroProps = [
        'headingTag' => 'h1',
        'badge'      => $s->hero_badge,
        'title'      => $s->hero_title,
        'subtitle'   => $s->hero_subtitle,
        'listTitle'  => 'Що ви отримаєте',
        // 👇 ВАЖЛИВО: Подаємо вже-нормалізований масив, а не $s->hero_bullets
        'bullets'    => $bullets,
        'primary'    => ['text' => $s->hero_primary_text,   'href' => $s->hero_primary_href],
        'secondary'  => ['text' => $s->hero_secondary_text, 'href' => $s->hero_secondary_href],
        'imageUrl'   => $heroImageUrl,
         'topIcon1xUrl'    => asset('images/hero/top-icon@1x.webp'),
  'topIcon2xUrl'    => asset('images/hero/top-icon@2x.webp'),
  // BOTTOM icon (1x/2x)
  'bottomIcon1xUrl' => asset('images/hero/bottom-icon@1x.webp'),
  'bottomIcon2xUrl' => asset('images/hero/bottom-icon@2x.webp'),
    ];
@endphp

{{-- бажано прелоадити LCP-фото --}}
@if($heroImageUrl)
  <link rel="preload" as="image" href="{{ $heroImageUrl }}" imagesizes="(min-width:768px) 560px, 100vw">
@endif

<div
  data-vue="HeroSection"
  data-props='@json($heroProps, JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES)'
>
  {{-- ⬇️ серверний skeleton ПРЯМО всередині острова --}}
  @include('partials.hero-skeleton')
</div>

  {{-- === KPI (Blade) === --}}
  <section class="bg-white py-12" aria-label="Ключові показники">
    <div class="mx-auto grid max-w-5xl gap-8 px-6 text-center sm:grid-cols-3">
      <div class="card-soft">
        <p class="text-4xl font-bold text-primary">150+</p>
        <p class="mt-2 text-sm font-medium text-muted">задоволених студентів</p>
      </div>
      <div class="card-soft">
        <p class="text-4xl font-bold text-primary">8 років</p>
        <p class="mt-2 text-sm font-medium text-muted">викладання англійської</p>
      </div>
      <div class="card-soft">
        <p class="text-4xl font-bold text-primary">97%</p>
        <p class="mt-2 text-sm font-medium text-muted">успішних складань IELTS</p>
      </div>
    </div>
  </section>

  {{-- === PROGRAMS (Vue) === --}}
  @php
    $programsProps = [
      'heading'    => 'Програми навчання',
      'subheading' => 'Обирайте напрям, який відповідає вашим цілям. Кожен курс адаптується за рівнем.',
      'cards' => [
        [
          'title' => 'IELTS та міжнародні іспити',
          'text'  => 'Комплексна підготовка до IELTS, TOEFL, Cambridge. Фокус на стратегії, письмі та speaking.',
          'list'  => ['12 тематичних модулів','Повні пробні тести','Аналіз типових помилок'],
          'badge' => 'від 450 грн/заняття',
        ],
        [
          'title' => 'Розмовна та бізнес-англійська',
          'text'  => 'Словник, вимова, комунікація для роботи, подорожей, презентацій.',
          'list'  => ['1-на-1 або міні-групи','Реальні кейси та рольові ігри','Щотижневий speaking club'],
          'badge' => 'від 400 грн/заняття',
        ],
        [
          'title' => 'Підтримка школярів',
          'text'  => 'Домашні, ДПА/ЗНО, граматика на простих прикладах.',
          'list'  => ['Ігри та інтерактиви','Контроль успішності для батьків','Гнучкий графік'],
          'badge' => 'від 350 грн/заняття',
        ],
      ],
    ];
  @endphp
  <div
    id="services"
    data-vue="ProgramsSection"
    data-props='@json($programsProps, JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES)'>
  </div>
{{-- === VALUES (Vue) === --}}
@php
  $valuesProps = [
    'heading' => 'Наші цінності',
    'subheading' => 'Що відрізняє наш підхід до навчання',
    'sections' => [
      [
        'title' => 'Індивідуальний шлях',
        'text'  => 'Програма під ваш рівень та цілі. Плавно, структуровано, з вимірюваними результатами.',
        'tone'  => 'light',
        'accent'=> true,
        // можна передати SVG або URL (необов’язково)
        'icon'  => null,
      ],
      [
        'title' => 'Дружнє середовище',
        'text'  => 'Комфортні заняття й підтримка — легше говорити та не боятися помилок.',
        'tone'  => 'dark',
        'accent'=> false,
        'icon'  => null,
      ],
      [
        'title' => 'Внутрішня мотивація',
        'text'  => 'Маленькі кроки щотижня, трекінг прогресу та заохочення.',
        'tone'  => 'soft',
        'accent'=> false,
        'icon'  => null,
      ],
      [
        'title' => 'Ігровий формат',
        'text'  => 'Інтерактиви, рольові ситуації й сценарії з реального життя.',
        'tone'  => 'brand',
        'accent'=> true,
        'icon'  => null,
      ],
      [
        'title' => 'Мова як хобі',
        'text'  => 'Вбудовуємо англійську у ваш день: міні-звички, контент, челенджі.',
        'tone'  => 'pastel',
        'accent'=> false,
        'icon'  => null,
      ],
    ],
  ];
@endphp

<div
  id="values"
  data-vue="ValuesSection"
  data-props='@json($valuesProps, JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES)'>
</div>
@php
  $featureProps = [
    'heading'       => 'Розмовні клуби —',
    'headingAccent' => 'більше практики, більше впевненості!',
    'subheading'    => 'Наші розмовні клуби — це чудова можливість покращити свою англійську чи німецьку, спілкуючись у невимушеній атмосфері.',
    'leftCta'       => ['text' => 'Для наших студентів',       'href' => '#contact'],
    'rightCta'      => ['text' => 'Для тих, хто вивчає інше',  'href' => '#contact'],
    // залишаємо пустими — з’являться плейсхолдери
    'leftImageUrl'  => $leftClubImg ?? '',
    'rightImageUrl' => $rightClubImg ?? '',
  ];
@endphp

<div data-vue="FeatureClubsSection"
     data-props='@json($featureProps, JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES)'></div>
  {{-- === ABOUT (Vue) === --}}
  {{-- Використовує дефолтні пропси компонента. За потреби — передай свій data-props як вище --}}
  <div id="about" data-vue="AboutSection"></div>

  {{-- === APPROACH (Vue) === --}}
  <div id="approach" data-vue="ApproachSection"></div>

  {{-- === TESTIMONIALS (Vue) === --}}
  <div id="testimonials" data-vue="TestimonialsSection"></div>

  {{-- === CTA (Vue) === --}}
  <div data-vue="CtaSection"></div>

  {{-- === CONTACT (Vue) === --}}
  <div id="contact" data-vue="ContactSection"></div>
@endsection
