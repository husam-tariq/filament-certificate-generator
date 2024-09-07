<?php

namespace HusamTariq\FilamentCertificateGenerator;

use Filament\Contracts\Plugin;
use Filament\Panel;
use HusamTariq\FilamentCertificateGenerator\Resources\CertificateTemplateResource;

class FilamentCertificateGeneratorPlugin implements Plugin
{
    private $enableAuthorScope = false;

    public function getId(): string
    {
        return 'filament-certificate-generator';
    }

    public function enableAuthorScope(Bool $enable): static
    {
        $this->enableAuthorScope = $enable;
        return $this;
    }

    public function hasAuthorScope(): Bool
    {
        return $this->enableAuthorScope;
    }


    public static function make(): static
    {
        return app(static::class);
    }

    public function register(Panel $panel): void
    {
        $panel
            ->resources([
                CertificateTemplateResource::class,
            ])
            ;
    }

    public function boot(Panel $panel): void
    {
        //
    }



    public static function get(): static
    {
        /** @var static $plugin */
        $plugin = filament(app(static::class)->getId());

        return $plugin;
    }
}
