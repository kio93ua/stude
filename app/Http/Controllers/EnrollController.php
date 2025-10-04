<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Mail;

class EnrollController extends Controller
{
    public function store(Request $request)
    {
        // валідація: під XHR поверне 422 JSON з errors[]
        $data = $request->validate([
            'fullName'  => ['required','string','max:255'],
            'age'       => ['nullable','integer','min:5','max:100'],
            'level'     => ['required','string','in:A1,A2,B1,B2,C1,Не знаю'],
            'phone'     => ['required','string','max:50'],
            'email'     => ['required','email','max:255'],
            'intent'    => ['required', Rule::in(['consultation','first_lesson','info'])],
            'questions' => ['nullable','string','max:5000'],
        ]); // :contentReference[oaicite:2]{index=2}

        // (опц.) збереження у БД:
        // Lead::create([...]);

        // e-mail на адресу з .env або fallback на mail.from.address
        $to = env('ENROLL_ADMIN_EMAIL', config('mail.from.address'));

        Mail::raw(
            "Нова заявка:\n"
            ."Ім'я: {$data['fullName']}\n"
            ."Вік: ".($data['age'] ?? '—')."\n"
            ."Рівень: {$data['level']}\n"
            ."Телефон: {$data['phone']}\n"
            ."Email: {$data['email']}\n"
            ."Мета: {$data['intent']}\n"
            ."Питання: ".($data['questions'] ?? '—')."\n",
            fn ($m) => $m->to($to)->subject('Нова заявка з сайту')
        ); // базовий приклад; для продакшна краще Mailable / Markdown + queue :contentReference[oaicite:3]{index=3}

        return response()->json(['ok' => true]);
    }
}
