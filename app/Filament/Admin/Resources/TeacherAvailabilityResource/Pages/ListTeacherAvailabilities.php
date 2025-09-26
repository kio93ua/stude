<?php

namespace App\Filament\Admin\Resources\TeacherAvailabilityResource\Pages;

use App\Filament\Admin\Resources\TeacherAvailabilityResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListTeacherAvailabilities extends ListRecords
{
    protected static string $resource = TeacherAvailabilityResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()->label('Додати слот'),
        ];
    }
}
