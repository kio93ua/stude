<?php

namespace App\Filament\Teacher\Resources\HomeworkResource\Pages;

use App\Filament\Teacher\Resources\HomeworkResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListHomeworks extends ListRecords
{
    protected static string $resource = HomeworkResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
