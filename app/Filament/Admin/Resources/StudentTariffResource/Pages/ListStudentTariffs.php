<?php

namespace App\Filament\Admin\Resources\StudentTariffResource\Pages;

use App\Filament\Admin\Resources\StudentTariffResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListStudentTariffs extends ListRecords
{
    protected static string $resource = StudentTariffResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()->label('Додати тариф'),
        ];
    }
}
