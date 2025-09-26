@extends('layouts.app')

@section('content')
<section class="bg-white py-20">
    <div class="mx-auto max-w-md px-6">
        <div class="mb-10 text-center">
            <h1 class="text-3xl font-semibold text-slate-900">Увійдіть до кабінету</h1>
            <p class="mt-3 text-slate-600">Вкажіть логін і пароль, щоб перейти до свого кабінету.</p>
        </div>

        <form method="POST" action="{{ route('login.store') }}" class="space-y-6 rounded-3xl border border-slate-200 bg-slate-50 p-8 shadow-sm">
            @csrf

            <div>
                <label for="username" class="block text-sm font-semibold text-slate-700">Логін</label>
                <input id="username" name="username" type="text" value="{{ old('username') }}" required autofocus
                    class="mt-2 w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm text-slate-700 shadow-inner focus:border-indigo-400 focus:outline-none">
                @error('username')
                    <p class="mt-2 text-xs text-rose-500">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="password" class="block text-sm font-semibold text-slate-700">Пароль</label>
                <input id="password" name="password" type="password" required
                    class="mt-2 w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm text-slate-700 shadow-inner focus:border-indigo-400 focus:outline-none">
                @error('password')
                    <p class="mt-2 text-xs text-rose-500">{{ $message }}</p>
                @enderror
            </div>

            <div class="flex items-center justify-between text-sm">
                <label class="inline-flex items-center gap-2 text-slate-600">
                    <input type="checkbox" name="remember" class="h-4 w-4 rounded border-slate-300 text-indigo-600 focus:ring-indigo-500">
                    Запам'ятати мене
                </label>
                <a href="#" class="text-indigo-500 hover:text-indigo-600">Забули пароль?</a>
            </div>

            @if ($errors->has('username') && ! $errors->has('password'))
                <p class="text-sm text-rose-500">Перевірте логін або пароль і спробуйте ще раз.</p>
            @endif

            <button type="submit"
                class="w-full rounded-full bg-indigo-600 px-6 py-3 text-sm font-semibold text-white shadow-lg shadow-indigo-200 transition hover:bg-indigo-500">
                Увійти
            </button>
        </form>
    </div>
</section>
@endsection
