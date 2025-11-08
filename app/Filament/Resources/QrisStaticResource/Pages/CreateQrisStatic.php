<?php

namespace App\Filament\Resources\QrisStaticResource\Pages;

use App\Filament\Resources\QrisStaticResource;
use Filament\Resources\Pages\CreateRecord;

class CreateQrisStatic extends CreateRecord
{
    protected static string $resource = QrisStaticResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
