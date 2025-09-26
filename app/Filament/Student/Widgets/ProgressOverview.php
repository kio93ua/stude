<?php

namespace App\Filament\Student\Widgets;

use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Card;

class ProgressOverview extends BaseWidget
{
    protected function getCards(): array
    {
        return [
            Card::make('Граматика', '72%')
                ->description('Покращення за 2 тижні')
                ->descriptionIcon('heroicon-m-chart-bar')
                ->color('primary'),
            Card::make('Говоріння', '78%')
                ->description('Наступна перевірка у пʼятницю')
                ->descriptionIcon('heroicon-m-microphone')
                ->color('success'),
            Card::make('Дедлайни', '2 активні')
                ->description('Переглянь домашнє завдання')
                ->descriptionIcon('heroicon-m-clock')
                ->color('warning'),
        ];
    }
}
