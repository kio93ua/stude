<?php

namespace App\Filament\Admin\Resources\LessonChangeRequestResource\Pages;

use App\Filament\Admin\Resources\LessonChangeRequestResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Support\Carbon;

class EditLessonChangeRequest extends EditRecord
{
    protected static string $resource = LessonChangeRequestResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        if (($data['status'] ?? 'pending') !== 'pending') {
            $data['handled_by_id'] = $data['handled_by_id'] ?? auth()->id();
            $data['handled_at'] = $data['handled_at'] ?? Carbon::now();
        }

        if (is_string($data['payload'])) {
            $decoded = json_decode($data['payload'], true);
            if (json_last_error() === JSON_ERROR_NONE) {
                $data['payload'] = $decoded;
            }
        }

        return $data;
    }
}
