<section class="overflow-hidden bg-gradient-to-br from-brand-mint via-white to-brand-aqua/10">
  <div class="mx-auto grid max-w-6xl gap-10 px-6 pb-16 pt-20 md:grid-cols-2 md:items-center">
    {{-- Ліва колонка (резерв висоти під заголовок/підзаголовок/кнопки) --}}
    <div class="space-y-6">
      <div class="h-6 w-56 rounded-full bg-black/10"></div>
      <div class="h-16 w-5/6 rounded-lg bg-black/10"></div>
      <div class="h-5 w-4/6 rounded bg-black/10"></div>
      <div class="mt-4 flex gap-3">
        <div class="h-11 w-48 rounded-full bg-black/10"></div>
        <div class="h-11 w-48 rounded-full border border-black/10"></div>
      </div>
    </div>

    {{-- Права колонка: фіксуємо висоту/позиції --}}
    <div class="relative md:min-h-[520px]">
      {{-- плями як були --}}
      <div aria-hidden="true" class="absolute -left-10 top-10 hidden h-24 w-24 rounded-full bg-black/5 blur-3xl md:block"></div>
      <div aria-hidden="true" class="absolute -right-8 bottom-4 hidden h-20 w-20 rounded-full bg-black/5 blur-2xl md:block"></div>

      {{-- місце під верхню іконку 230x230 --}}
      <div class="pointer-events-none absolute -top-14 -left-16 z-10" style="width:230px;height:230px"></div>

      {{-- карта з фото (aspect 4:3) --}}
      <div class="relative rounded-3xl bg-white p-6 shadow-xl shadow-black/5">
        <div class="space-y-4">
          <div class="overflow-hidden rounded-2xl">
            <div class="relative w-full aspect-[4/3] bg-black/10"></div>
          </div>

          <div class="h-5 w-40 rounded bg-black/10"></div>

          <ul class="space-y-3">
            <li class="h-4 w-3/4 rounded bg-black/10"></li>
            <li class="h-4 w-2/3 rounded bg-black/10"></li>
            <li class="h-4 w-1/2 rounded bg-black/10"></li>
          </ul>
        </div>
      </div>

      {{-- місце під нижню іконку 230x230 --}}
      <div class="pointer-events-none absolute -bottom-16 -right-20 z-10" style="width:230px;height:230px"></div>
    </div>
  </div>
</section>
