<?php

namespace App\Filament\Teacher\Resources\StudyTestResource\Pages;

use App\Filament\Teacher\Resources\StudyTestResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditStudyTest extends EditRecord
{
    protected static string $resource = StudyTestResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
