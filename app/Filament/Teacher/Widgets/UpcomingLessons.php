<?php

namespace App\Filament\Teacher\Widgets;

use Filament\Widgets\Widget;

class UpcomingLessons extends Widget
{
    protected static string $view = 'filament.teacher.widgets.upcoming-lessons';

    protected function getViewData(): array
    {
        return [
            'lessons' => [
                ['time' => 'Пн 10:00', 'title' => 'IELTS Speaking (група B2)', 'location' => 'Zoom'],
                ['time' => 'Вт 18:30', 'title' => 'Business English (інтенсив)', 'location' => 'Офіс, ауд. 2'],
                ['time' => 'Чт 16:00', 'title' => 'Teen club (B1)', 'location' => 'Zoom'],
                ['time' => 'Пт 11:30', 'title' => 'Exam clinic', 'location' => 'Офіс, ауд. 1'],
            ],
            'homeworks' => [
                ['group' => 'IELTS Speaking', 'status' => '6/10 перевірено'],
                ['group' => 'Business English', 'status' => '3/8 перевірено'],
                ['group' => 'Teen club', 'status' => 'Очікує'],
            ],
            'messages' => [
                ['name' => 'Олена К.', 'text' => 'Підтвердьте консультацію у п’ятницю'],
                ['name' => 'Антон Л.', 'text' => 'Відправив домашнє завдання за 18.09'],
                ['name' => 'Світлана П.', 'text' => 'Потрібно перенести урок на інший час'],
            ],
        ];
    }
}
