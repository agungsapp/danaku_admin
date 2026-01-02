<?php

namespace App\Filament\Resources\Dompets\Pages;

use App\Filament\Resources\Dompets\DompetResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ManageRecords;

class ManageDompets extends ManageRecords
{
    protected static string $resource = DompetResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
