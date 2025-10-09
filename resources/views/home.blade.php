@extends('layouts.app')

@section('title','Головна')

@section('content')

{{-- === HERO (Vue) === --}}
@php
    /** @var \App\Settings\HomePageSettings $s */
    $s = app(\App\Settings\HomePageSettings::class);

    $normalizeString = function ($value, string $fallback = ''): string {
        $trimmed = is_string($value) ? trim($value) : trim((string) ($value ?? ''));
        return $trimmed !== '' ? $trimmed : $fallback;
    };

  $normalizeUrl = function ($value, string $fallback = '#contact') use ($normalizeString): string {
      $url = $normalizeString($value, '');
      return $url !== '' ? $url : $fallback;
  };

  $normalizeOptionalUrl = function ($value): ?string {
      $trimmed = is_string($value) ? trim($value) : trim((string) ($value ?? ''));
      return $trimmed !== '' ? $trimmed : null;
  };

  $normalizeList = function ($value, array $fallback = []): array {
      if (is_string($value)) {
          $value = preg_split('/\s*,\s*/', $value, -1, PREG_SPLIT_NO_EMPTY);
      }

        if (! is_array($value)) {
            return $fallback;
        }

        $clean = array_values(array_filter(array_map(static fn($item) => is_string($item)
            ? trim($item)
            : (is_array($item)
                ? trim((string)($item['text'] ?? $item['label'] ?? $item['value'] ?? ($item[0] ?? '')))
                : ''
            ), $value), fn($v) => $v !== ''));

        return $clean ?: $fallback;
    };

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
  /** @var \App\Settings\HomePageSettings $s */
  $s = app(\App\Settings\HomePageSettings::class);

  // побудова URL зображення: пріоритет — зовнішній URL, інакше локальний storage
  $vacancyImageUrl = null;
  if (!empty($s->vacancy_media_url)) {
      $vacancyImageUrl = trim($s->vacancy_media_url);
  } elseif (!empty($s->vacancy_media_path)) {
      $vacancyImageUrl = \Illuminate\Support\Str::startsWith($s->vacancy_media_path, ['http://','https://','/'])
          ? $s->vacancy_media_path
          : \Illuminate\Support\Facades\Storage::disk('public')->url($s->vacancy_media_path);
  }

  $vacancyProps = [
      'badge'    => trim((string)$s->vacancy_badge),
      'title'    => trim((string)$s->vacancy_title),
      'subtitle' => trim((string)($s->vacancy_subtitle ?? '')) ?: null,
      'bullets'  => is_array($s->vacancy_bullets) ? array_values(array_filter(array_map('trim', $s->vacancy_bullets))) : [],
      'mediaSrc' => $vacancyImageUrl ?: '/images/teacher-apply.webp',
      'ctaText'  => trim((string)$s->vacancy_cta_text),
      'formUrl'  => trim((string)$s->vacancy_cta_url),
  ];
@endphp

<div data-vue="TeacherVacancy"
     data-props='@json($vacancyProps, JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES)'></div>


  
  {{-- === FOUNDER STORY (Vue) === --}}
@php
  $founderPhotoPath = $s->founder_photo_path ?? null;
  $founderPhotoUrl = null;
  if ($founderPhotoPath) {
      $founderPhotoUrl = \Illuminate\Support\Str::startsWith($founderPhotoPath, ['http://', 'https://'])
          ? $founderPhotoPath
          : \Illuminate\Support\Facades\Storage::disk('public')->url($founderPhotoPath);
  }
  $founderPhotoUrl = $founderPhotoUrl ?: asset('images/founder.png');

  $mapStorySections = function ($source, bool $withQuote = false) use ($normalizeString, $normalizeList) {
      if (! is_array($source)) {
          return [];
      }

      $result = [];
      foreach ($source as $entry) {
          if (! is_array($entry)) {
              continue;
          }

          $heading = $normalizeString($entry['heading'] ?? null, '');
          $body = $normalizeList($entry['body'] ?? null, []);

          if ($heading === '' || $body === []) {
              continue;
          }

          $record = [
              'heading' => $heading,
              'body' => $body,
          ];

          if ($withQuote) {
              $quoteText = $normalizeString($entry['quote_text'] ?? ($entry['quote']['text'] ?? null), '');
              if ($quoteText !== '') {
                  $record['quote'] = [
                      'text' => $quoteText,
                      'author' => $normalizeString($entry['quote_author'] ?? ($entry['quote']['author'] ?? null), ''),
                  ];
              }
          }

          $result[] = $record;
      }

      return $result;
  };

  $founderSections = $mapStorySections($s->founder_sections ?? null, true);
  $founderExtraSections = $mapStorySections($s->founder_extra_sections ?? null, false);

  $founderSocials = array_filter([
      'linkedin' => $normalizeOptionalUrl($s->founder_linkedin ?? null),
      'instagram'=> $normalizeOptionalUrl($s->founder_instagram ?? null),
      'site'     => $normalizeOptionalUrl($s->founder_site ?? null),
  ]);

  $founderProps = [
      'badge'    => $normalizeString($s->founder_badge ?? null, 'Історія засновника'),
      'subtitle' => $normalizeString($s->founder_intro ?? null, ''),
      'founder'  => [
          'name'   => $normalizeString($s->founder_name ?? null, 'Олена Коваль'),
          'role'   => $normalizeString($s->founder_role ?? null, 'Засновниця школи англійської'),
          'photo'  => [
              'src' => $founderPhotoUrl,
              'alt' => $normalizeString($s->founder_photo_alt ?? null, $normalizeString($s->founder_name ?? null, 'Олена Коваль')),
          ],
          'socials' => $founderSocials,
      ],
      'content' => $founderSections,
      'extra'   => $founderExtraSections,
  ];

  if ($founderProps['subtitle'] === '') {
      $founderProps['subtitle'] = null;
  }
@endphp

{{-- бажано прелоадити фото засновника як LCP/near-LCP, якщо поруч герой-блок --}}
<link rel="preload" as="image" href="{{ asset('images/founder.png') }}" imagesizes="(min-width:768px) 560px, 100vw">

<div
  id="founder"
  data-vue="FounderStory"
  data-props='@json($founderProps, JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES)'>
  {{-- за бажанням: server-skeleton тут --}}
</div>
{{-- === LESSONS (Vue) === --}}
@php
  // Пропси для LessonsBlock.vue
 $lessonsProps = [
    'intro' => [
      'badge'    => $normalizeString($s->lessons_badge ?? null, 'Програми навчання'),
      'title'    => $normalizeString($s->lessons_title ?? null, 'Наші уроки'),
      'subtitle' => $normalizeString($s->lessons_subtitle ?? null, 'Баланс розмовної практики, граматики та лексики. Кожне заняття — ще один крок до вільної англійської.'),
    ],
    'videos' => (function () use ($s) {
      $raw = is_array($s->lessons_videos ?? null) ? $s->lessons_videos : [];
      return array_values(array_filter(array_map(function ($item) {
        if (! is_array($item)) {
          return null;
        }
        $id = trim((string)($item['id'] ?? ''));
        $title = trim((string)($item['title'] ?? ''));
        if ($id === '' || $title === '') {
          return null;
        }
        $description = trim((string)($item['description'] ?? ''));
        return [
          'id' => $id,
          'title' => $title,
          'description' => $description !== '' ? $description : null,
        ];
      }, $raw)));
    })(),
    'autoplayOnView' => (bool)($s->lessons_autoplay_on_view ?? false),
  ];
@endphp

<div
  id="lessons"
  data-vue="LessonsBlock"
  data-props='@json($lessonsProps, JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES)'>
  {{-- optional: server-skeleton тут --}}
</div>
  

{{-- === ENROLL FORM (Vue) === --}}
<div
  data-vue="EnrollForm"
  data-props='{}'
>
  {{-- за бажанням: server-skeleton тут --}}
</div>

     {{-- === PRICING (Vue) === --}}
@php
  $planDefaults = app(\App\Settings\HomePageSettings::class)->pricing_plans;
  $savedPlans = is_array($s->pricing_plans ?? null) ? $s->pricing_plans : [];

  $buildPlan = function (string $key) use ($planDefaults, $savedPlans, $normalizeString, $normalizeUrl, $normalizeList): array {
      $defaults = $planDefaults[$key] ?? [];
      $plan = is_array($savedPlans[$key] ?? null) ? $savedPlans[$key] : [];

      return [
          'title'       => $normalizeString($plan['title'] ?? null, $defaults['title'] ?? ''),
          'label'       => $normalizeString($plan['label'] ?? null, $defaults['label'] ?? ''),
          'description' => $normalizeString($plan['description'] ?? null, $defaults['description'] ?? ''),
          'price'       => $normalizeString($plan['price'] ?? null, $defaults['price'] ?? ''),
          'meta'        => $normalizeString($plan['meta'] ?? null, $defaults['meta'] ?? ''),
          'features'    => $normalizeList($plan['features'] ?? null, (array)($defaults['features'] ?? [])),
          'cta_text'    => $normalizeString($plan['cta_text'] ?? null, $defaults['cta_text'] ?? ''),
          'cta_href'    => $normalizeUrl($plan['cta_href'] ?? null, $defaults['cta_href'] ?? '#contact'),
      ];
  };

  $pricingPlans = [
      'group' => $buildPlan('group'),
      'pair' => $buildPlan('pair'),
      'individual' => $buildPlan('individual'),
  ];

  $pricingProps = [
      'badge'     => $normalizeString($s->pricing_badge ?? null, 'Пакети занять'),
      'title'     => $normalizeString($s->pricing_title ?? null, 'Обери формат, що пасує саме тобі'),
      'subtitle'  => $normalizeString($s->pricing_subtitle ?? null, 'Три прозорі варіанти з чіткими перевагами.'),
      'currency'  => $normalizeString($s->pricing_currency ?? null, '₴'),
      'plans'     => $pricingPlans,
  ];
@endphp

<div
  id="pricing"
  data-vue="PricingCards"
  data-props='@json($pricingProps, JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES)'>
</div>
@php
  $rawAdvantages = is_array($s->advantages_items ?? null) ? $s->advantages_items : [];
  $toUrl = function (?string $path) {
      $trimmed = is_string($path) ? trim($path) : '';
      if ($trimmed === '') {
          return null;
      }
      if (Str::startsWith($trimmed, ['http://', 'https://', '/'])) {
          return $trimmed;
      }
      return Storage::disk('public')->url($trimmed);
  };

  $advantagesItems = [];
  foreach ($rawAdvantages as $entry) {
      if (! is_array($entry)) {
          continue;
      }

      $title = $normalizeString($entry['title'] ?? null, '');
      $imageUrl = $toUrl($entry['image_path'] ?? null);
      if ($title === '' || ! $imageUrl) {
          continue;
      }

      $desc = $normalizeString($entry['desc'] ?? null, '');
      $desc = $desc !== '' ? $desc : null;
      $bullets = $normalizeList($entry['bullets'] ?? null, []);

      $icons = [];
      if (is_array($entry['icons'] ?? null)) {
          foreach ($entry['icons'] as $iconPath) {
              $iconUrl = $toUrl(is_string($iconPath) ? $iconPath : null);
              if ($iconUrl) {
                  $icons[] = $iconUrl;
              }
          }
      }

      $advantagesItems[] = [
          'title'   => $title,
          'desc'    => $desc,
          'bullets' => $bullets,
          'image'   => [
              'src' => $imageUrl,
              'alt' => $normalizeString($entry['image_alt'] ?? null, $title),
          ],
          'icons'   => $icons,
      ];
  }

  if ($advantagesItems === []) {
      $defaults = app(\App\Settings\HomePageSettings::class)->advantages_items;
      foreach ($defaults as $entry) {
          $imageUrl = $toUrl($entry['image_path'] ?? null);
          if (! $imageUrl) {
              continue;
          }
          $desc = $normalizeString($entry['desc'] ?? null, '');
          $desc = $desc !== '' ? $desc : null;
          $advantagesItems[] = [
              'title'   => $normalizeString($entry['title'] ?? null, ''),
              'desc'    => $desc,
              'bullets' => $normalizeList($entry['bullets'] ?? null, []),
              'image'   => [
                  'src' => $imageUrl,
                  'alt' => $normalizeString($entry['image_alt'] ?? null, ''),
              ],
              'icons'   => [],
          ];
      }
  }

  $advSubtitle = $normalizeString($s->advantages_subtitle ?? null, '');
  $advantagesProps = [
      'badge'    => $normalizeString($s->advantages_badge ?? null, 'Переваги навчання'),
      'title'    => $normalizeString($s->advantages_title ?? null, 'Вчися ефективно — у зручному для тебе форматі'),
      'subtitle' => $advSubtitle !== '' ? $advSubtitle : null,
      'cta'      => [
          'text' => $normalizeString($s->advantages_cta_text ?? null, 'Записатися'),
          'href' => $normalizeUrl($s->advantages_cta_href ?? null, '#contact'),
      ],
      'items'    => $advantagesItems,
  ];
@endphp

<div
  data-vue="AdvantagesSplit"
  data-props='@json($advantagesProps, JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES)'>
</div>
@php
  $reviewsRaw = is_array($s->reviews_items ?? null) ? $s->reviews_items : [];
  $reviewsList = [];
  foreach ($reviewsRaw as $entry) {
      if (! is_array($entry)) {
          continue;
      }

      $name = $normalizeString($entry['name'] ?? null, '');
      $text = $normalizeString($entry['text'] ?? null, '');

      if ($name === '' || $text === '') {
          continue;
      }

      $course = $normalizeString($entry['course'] ?? null, '');
      $stars = $entry['stars'] ?? 5;
      $stars = is_numeric($stars) ? max(0, min(5, (int) $stars)) : 5;

      $avatarPath = $entry['avatar_path'] ?? null;
      $avatarUrl = $normalizeOptionalUrl($entry['avatar_url'] ?? null);

      $avatar = null;
      if (is_string($avatarPath) && $avatarPath !== '') {
          if (\Illuminate\Support\Str::startsWith($avatarPath, ['http://', 'https://', '/'])) {
              $avatar = $avatarPath;
          } else {
              $avatar = \Illuminate\Support\Facades\Storage::disk('public')->url($avatarPath);
          }
      }

      if (! $avatar) {
          $avatar = $avatarUrl ?: 'https://i.pravatar.cc/96?img=15';
      }

      $reviewsList[] = [
          'name' => $name,
          'text' => $text,
          'course' => $course !== '' ? $course : null,
          'stars' => $stars,
          'avatar' => $avatar,
      ];
  }

  $fallbackReviews = [
      ["name"=>"Марія Коваль","avatar"=>"https://i.pravatar.cc/96?img=1","stars"=>5,"course"=>"IELTS","text"=>'Класні уроки, багато розмовної практики і чіткий план підготовки. За місяць стала впевненіше говорити, рекомендую!'],
      ["name"=>"Олег С.","avatar"=>"https://i.pravatar.cc/96?img=2","stars"=>5,"course"=>"Business English","text"=>'Сучасні завдання, реальні кейси з роботи. Дуже подобається формат — завжди тримає фокус і дає результат.'],
      ["name"=>"Ірина Ч.","avatar"=>"https://i.pravatar.cc/96?img=3","stars"=>5,"course"=>"General","text"=>'Дуже комфортно й ефективно. Індивідуальний підхід, помітний прогрес вже за кілька тижнів.'],
      ["name"=>"Андрій П.","avatar"=>"https://i.pravatar.cc/96?img=4","stars"=>5,"course"=>"Speaking Club","text"=>'Динамічні зустрічі, багато говоріння, виправлення помилок у реальному часі — супер!'],
  ];

  $reviewsProps = [
      'badge' => $normalizeString($s->reviews_badge ?? null, 'Наші відгуки'),
      'title' => $normalizeString($s->reviews_title ?? null, 'Нам довіряють — результати студентів це наша перевага'),
      'buttonText' => $normalizeString($s->reviews_button_text ?? null, 'Більше відгуків в Instagram'),
      'buttonUrl' => $normalizeUrl($s->reviews_button_url ?? null, 'https://instagram.com/your.profile'),
      'reviews' => $reviewsList !== [] ? $reviewsList : $fallbackReviews,
  ];
@endphp

<div
  data-vue="ReviewsMarquee"
  data-props='@json($reviewsProps, JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES)'>
</div>
{{-- === FAQ (Vue) === --}}
@php
  $rawFaq = is_array($s->faq_items ?? null) ? $s->faq_items : [];
  $faqItems = [];

  foreach ($rawFaq as $entry) {
      if (! is_array($entry)) {
          continue;
      }

      $question = $normalizeString($entry['q'] ?? null, '');
      $answer = trim((string) ($entry['a'] ?? ''));

      if ($question === '' || $answer === '') {
          continue;
      }

      $faqItems[] = [
          'q' => $question,
          'a' => $answer,
      ];
  }

  if ($faqItems === []) {
      $faqItems = app(\App\Settings\HomePageSettings::class)->faq_items;
  }

  $faqSubtitle = $normalizeString($s->faq_subtitle ?? null, '');
  $faqProps = [
      'title'    => $normalizeString($s->faq_title ?? null, 'FAQ (коротко)'),
      'subtitle' => $faqSubtitle !== '' ? $faqSubtitle : null,
      'items'    => $faqItems,
  ];
@endphp

<div
  data-vue="FaqSection"
  data-props='@json($faqProps, JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES)'>
  {{-- Опційний слот #intro, якщо захочеш додати HTML-вступ:
  <div data-slot="intro">
    <p>Зібрали відповіді на часті питання про формат занять, терміни та методику.</p>
    <p>Не знайшли своє? Напишіть нам — підкажемо.</p>
  </div>
  --}}
</div>

 
@endsection
