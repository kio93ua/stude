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
@php
  $vacancyProps = [
    'mediaSrc' => '/images/teacher-apply.webp',
    'formUrl'  => 'https://docs.google.com/forms/d/e/1FAIpQLSew8oe-A0p3wS7omGT_u3h9ts04egW_Mr0SfIgYzXn8tQUekA/viewform?usp=header',
  ];
@endphp

<div data-vue="TeacherVacancy"
     data-props='@json($vacancyProps, JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES)'></div>

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
  {{-- === FOUNDER STORY (Vue) === --}}
@php
  $founder = [
    'name'   => 'Олена Коваль',
    'role'   => 'Засновниця школи англійської',
    'photo'  => [
      'src' => asset('images/founder.jpg'),
      'alt' => 'Портрет засновниці',
    ],
    'socials' => [
      'linkedin' => 'https://www.linkedin.com/',
      'instagram'=> 'https://www.instagram.com/',
      'site'     => 'https://example.com',
    ],
  ];

  $storyContent = [
    [
      'heading' => 'Початок шляху',
      'body'    => [
        'Усе почалося з індивідуальних занять удома: один стіл, ноутбук та велике бажання допомогти студентам заговорити впевнено.',
        'Поступово сформувалася методика, що поєднує комунікативний підхід та завдання з реальних ситуацій.',
      ],
      'milestones' => [
        ['year' => '2015', 'text' => 'Перші 10 учнів і відгуки, що надихнули рухатися далі.'],
        ['year' => '2016', 'text' => 'Перші групи вихідного дня: розмовні клуби та міні-проєкти.'],
      ],
    ],
    [
      'heading' => 'Розвиток і помилки',
      'body'    => [
        'Зростання — це експерименти. Частину форматів ми відкинули, залишивши тільки ті, що реально працюють.',
        'Фокус — на практиці й результаті: тестові розмови, мікроцілі та підсумки після кожного модуля.',
      ],
      'quote' => ['text' => 'Мова — інструмент. Коли ним користуєшся щодня, він залишається гострим.', 'author' => 'Олена'],
    ],
    [
      'heading' => 'Сьогодні',
      'body'    => [
        'Ми зібрали найкращий досвід у структуровані програми та оновлюємо матеріали щосеместру.',
        'Мета — дати інструменти й впевненість, щоб англійська працювала у реальному житті.',
      ],
    ],
  ];
@endphp

{{-- бажано прелоадити фото засновника як LCP/near-LCP, якщо поруч герой-блок --}}
<link rel="preload" as="image" href="{{ asset('images/founder.jpg') }}" imagesizes="(min-width:768px) 560px, 100vw">

<div
  id="founder"
  data-vue="FounderStory"
  data-props='@json(["founder"=>$founder,"content"=>$storyContent], JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES)'>
  {{-- за бажанням: server-skeleton тут --}}
</div>
{{-- === LESSONS (Vue) === --}}
@php
  // Пропси для LessonsBlock.vue
  $lessonsProps = [
    'intro' => [
      'badge'    => 'Програми навчання',
      'title'    => 'Наші уроки',
      'subtitle' => 'Баланс розмовної практики, граматики та лексики. Кожне заняття — ще один крок до вільної англійської.',
    ],
    // ЗАМІНИ YouTube ID нижче на свої; можна додавати більше карток
    'videos' => [
      ['id' => 'dQw4w9WgXcQ', 'title' => 'Intro: як ми вчимося', 'description' => 'Короткий огляд підходу та структури уроків.'],
      ['id' => 'ysz5S6PUM-U', 'title' => 'Розмовна практика: small talk', 'description' => 'Фрази для щоденного спілкування.'],
      ['id' => 'ScMzIvxBSi4', 'title' => 'Граматика без болю: часи', 'description' => 'Як швидко згадати й застосувати часи.'],
      ['id' => 'kXYiU_JCYtU', 'title' => 'Фразові дієслова', 'description' => 'Корисні конструкції для реальних ситуацій.'],
    ],
    // Увімкнути маркери ScrollTrigger (для дебагу): true/false
    'debugMarkers' => false,
  ];
@endphp

<div
  id="lessons"
  data-vue="LessonsBlock"
  data-props='@json($lessonsProps, JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES)'>
  {{-- optional: server-skeleton тут --}}
</div>
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


     {{-- === PRICING (Vue) === --}}
@php
  // Мінімальні пропси; якщо треба свої плани/ціни — передай їх тут:
  $pricingProps = [
    'currency' => '₴',
    // 'plans' => [...], // можеш передати масив своїх планів із адмінки/настроювань
  ];
@endphp

<div
  id="pricing"
  data-vue="PricingCards"
  data-props='@json($pricingProps, JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES)'>
</div>
@php
  $advantagesItems = [
    [
      'title'   => 'Ігрові методи',
      'desc'    => 'Інтерактиви, рольові сценарії та міні-ігри — мотивація росте, страх помилок зникає.',
      'bullets' => ['Щотижневі челенджі', 'Сценарії з реального життя', 'Веселі практики замість нудної теорії'],
      'image'   => ['src' => asset('images/adv/gamified.jpg'), 'alt' => 'Ігрові методи'],
      'icons'   => [],
    ],
    [
      'title'   => 'Сучасна програма вивчення',
      'desc'    => 'Комунікативний підхід, мікрозвички та трек прогресу — чіткий результат щотижня.',
      'bullets' => ['Модульна структура', 'Практика > теорія', 'Персональні рекомендації'],
      'image'   => ['src' => asset('images/adv/modern.jpg'), 'alt' => 'Сучасна програма'],
      'icons'   => [],
    ],
    [
      'title'   => 'Задоволені учні',
      'desc'    => 'Тепла дружня атмосфера й підтримка — легше говорити впевнено.',
      'bullets' => ['Малі групи або 1-на-1', 'Зворотний зв’язок щотижня', 'Клуби розмовної практики'],
      'image'   => ['src' => asset('images/adv/happy.jpg'), 'alt' => 'Задоволені учні'],
      'icons'   => [],
    ],
  ];
@endphp

<div
  data-vue="AdvantagesSplit"
  data-props='@json(["items" => $advantagesItems], JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES)'>
</div>
@php
  $reviewsProps = [
    'reviews' => [
      ["name"=>"Марія Коваль","avatar"=>"https://i.pravatar.cc/96?img=1","stars"=>5,"course"=>"IELTS","text"=>"..."],
      ["name"=>"Олег С.","avatar"=>"https://i.pravatar.cc/96?img=2","stars"=>5,"course"=>"Business English","text"=>"..."],
      ["name"=>"Ірина","avatar"=>"https://i.pravatar.cc/96?img=3","stars"=>5,"course"=>"General","text"=>"..."],
      ["name"=>"Андрій","avatar"=>"https://i.pravatar.cc/96?img=4","stars"=>5,"course"=>"Speaking","text"=>"..."],
    ],
  ];
@endphp

<div
  data-vue="ReviewsMarquee"
  data-props='@json($reviewsProps, JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES)'>
</div>

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
