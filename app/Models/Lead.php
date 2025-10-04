<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
// use Illuminate\Database\Eloquent\SoftDeletes; // ⬅️ розкоментуй, якщо хочеш м’яке видалення

class Lead extends Model
{
    use HasFactory;
    // use SoftDeletes; // ⬅️ і це теж (не забудь колонку deleted_at у міграції)

    /**
     * Назва таблиці (можна опустити, якщо "leads" і так за конвенцією)
     */
    protected $table = 'leads';

    /**
     * Масово заповнювані поля (whitelist).
     * Рекомендовано використовувати $fillable, щоб уникнути уразливостей масового призначення. 
     * Альтернатива — $guarded, але whitelist зазвичай безпечніший. :contentReference[oaicite:1]{index=1}
     */
    protected $fillable = [
        'full_name',
        'age',
        'level',
        'phone',
        'email',
        'intent',     // consultation | first_lesson | info
        'questions',
        'meta',       // JSON (ip, ua, referer, тощо)
    ];

    /**
     * Касти атрибутів: JSON → array, числа → integer, дати → datetime/immutable тощо.
     * Завдяки кастам працювати з типами значно зручніше. :contentReference[oaicite:2]{index=2}
     */
    protected $casts = [
        'age'       => 'integer',
        'meta'      => 'array',   // альтернативно: \Illuminate\Database\Eloquent\Casts\AsArrayObject
        'created_at'=> 'datetime',
        'updated_at'=> 'datetime',
        // 'deleted_at'=> 'datetime', // якщо використовуєш SoftDeletes
    ];

    /**
     * Дефолтні значення (не обов’язково).
     */
    protected $attributes = [
        'meta' => '[]',
    ];

    /**
     * Accessor: людська назва мети звернення (для blade/API).
     */
    public function getIntentLabelAttribute(): string
    {
        return match ($this->intent) {
            'consultation' => 'Консультація',
            'first_lesson' => 'Перший урок',
            'info'         => 'Дізнатись інформацію',
            default        => '—',
        };
    }

    /**
     * Приклад простого scope-фільтра (за e-mail або телефоном).
     */
    public function scopeSearch($query, ?string $term)
    {
        if (!$term) return $query;

        return $query->where(function ($q) use ($term) {
            $q->where('full_name', 'like', "%{$term}%")
              ->orWhere('email', 'like', "%{$term}%")
              ->orWhere('phone', 'like', "%{$term}%");
        });
    }
}
