<?php

namespace App\Filament\Teacher\Resources\HomeworkResource\Pages;

use App\Filament\Teacher\Resources\HomeworkResource;
use Filament\Resources\Pages\EditRecord;

class EditHomework extends EditRecord
{
    protected static string $resource = HomeworkResource::class;

    protected function mutateFormDataBeforeSave(array $data): array
    {
        $data['teacher_id'] = auth()->id();

        return $data;
    }
}
