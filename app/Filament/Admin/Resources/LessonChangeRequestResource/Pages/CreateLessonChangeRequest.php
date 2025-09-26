<?php

namespace App\Filament\Admin\Resources\LessonChangeRequestResource\Pages;

use App\Filament\Admin\Resources\LessonChangeRequestResource;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Carbon;

class CreateLessonChangeRequest extends CreateRecord
{
    protected static string $resource = LessonChangeRequestResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['requested_by_id'] = $data['requested_by_id'] ?? auth()->id();
        $data['status'] = $data['status'] ?? 'pending';

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
