<?php

namespace App\Filament\Resources\BannerIklanResource\Pages;

use App\Filament\Resources\BannerIklanResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditBannerIklan extends EditRecord
{
    protected static string $resource = BannerIklanResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
