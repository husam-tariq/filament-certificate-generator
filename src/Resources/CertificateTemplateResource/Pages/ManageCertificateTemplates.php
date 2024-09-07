<?php

namespace HusamTariq\FilamentCertificateGenerator\Resources\CertificateTemplateResource\Pages;

use Filament\Actions;
use Filament\Facades\Filament;
use Filament\Resources\Pages\ManageRecords;
use HusamTariq\FilamentCertificateGenerator\Resources\CertificateTemplateResource;

class ManageCertificateTemplates extends ManageRecords
{
    protected static string $resource = CertificateTemplateResource::class;



    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()->mutateFormDataUsing(function (array $data): array {
                $user = Filament::getCurrentPanel()->auth()->user();
                $data['author_type'] = get_class($user);
                $data['author_id'] = $user->id;
                return $data;
            }),
        ];
    }
}
