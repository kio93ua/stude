<?php

namespace App\Filament\Admin\Resources\LessonChangeRequestResource\Pages;

use App\Filament\Admin\Resources\LessonChangeRequestResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListLessonChangeRequests extends ListRecords
{
    protected static string $resource = LessonChangeRequestResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
