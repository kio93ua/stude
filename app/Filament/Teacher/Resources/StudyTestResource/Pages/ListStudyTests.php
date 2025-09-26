<?php

namespace App\Filament\Teacher\Resources\StudyTestResource\Pages;

use App\Filament\Teacher\Resources\StudyTestResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListStudyTests extends ListRecords
{
    protected static string $resource = StudyTestResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
