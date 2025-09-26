<?php

namespace App\Enums;

enum HomeworkStatus: string
{
    case ASSIGNED = 'assigned';
    case VIEWED = 'viewed';
    case SUBMITTED = 'submitted';
    case COMPLETED = 'completed';
    case REDO = 'redo';

    public function label(): string
    {
        return match ($this) {
            self::ASSIGNED => 'Очікує виконання',
            self::VIEWED => 'Переглянуто',
            self::SUBMITTED => 'Надіслано на перевірку',
            self::COMPLETED => 'Зараховано',
            self::REDO => 'Потрібно переробити',
        };
    }

    public function badgeColor(): string
    {
        return match ($this) {
            self::ASSIGNED => 'gray',
            self::VIEWED => 'info',
            self::SUBMITTED => 'warning',
            self::COMPLETED => 'success',
            self::REDO => 'danger',
        };
    }

    public static function options(): array
    {
        return collect(self::cases())
            ->mapWithKeys(fn (self $status) => [$status->value => $status->label()])
            ->toArray();
    }
}
