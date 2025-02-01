<?php

namespace App\Filament\Widgets;

use Filament\Widgets\Widget;

class KataMutiaraWidget extends Widget
{
    protected static string $view = 'filament.widgets.kata-mutiara-widget';

    protected int $sortOrder = 1;

    protected string|int|array $columnSpan = 'full';

    public function getQuote()
    {
        return [
            'view' => $this->getViewData(),
        ];
    }
}
