<?php

namespace App\Filament\Admin\Resources\StudentTariffResource\Pages;

use App\Filament\Admin\Resources\StudentTariffResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditStudentTariff extends EditRecord
{
    protected static string $resource = StudentTariffResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
