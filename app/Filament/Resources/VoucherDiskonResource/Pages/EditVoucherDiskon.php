<?php

namespace App\Filament\Resources\VoucherDiskonResource\Pages;

use App\Filament\Resources\VoucherDiskonResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditVoucherDiskon extends EditRecord
{
    protected static string $resource = VoucherDiskonResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
