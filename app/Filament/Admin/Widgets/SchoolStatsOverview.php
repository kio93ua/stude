<?php

namespace App\Filament\Admin\Widgets;

use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Card;

class SchoolStatsOverview extends BaseWidget
{
    protected function getCards(): array
    {
        return [
            Card::make('Активні студенти', 132)
                ->description('За останні 30 днів')
                ->descriptionIcon('heroicon-m-arrow-trending-up')
                ->color('success'),
            Card::make('Дохід за місяць', '₴182 000')
                ->description('12% росту')
                ->descriptionIcon('heroicon-m-banknotes')
                ->color('primary'),
            Card::make('Ново записані учні', 18)
                ->description('Ціль: 24')
                ->descriptionIcon('heroicon-m-user-plus')
                ->color('warning'),
        ];
    }
}
