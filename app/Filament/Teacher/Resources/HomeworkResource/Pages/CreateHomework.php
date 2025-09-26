<?php

namespace App\Filament\Teacher\Resources\HomeworkResource\Pages;

use App\Filament\Teacher\Resources\HomeworkResource;
use Filament\Resources\Pages\CreateRecord;

class CreateHomework extends CreateRecord
{
    protected static string $resource = HomeworkResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['teacher_id'] = auth()->id();

        return $data;
    }
}
