<?php

namespace HusamTariq\FilamentCertificateGenerator\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @see \HusamTariq\FilamentCertificateGenerator\FilamentCertificateGenerator
 */
class FilamentCertificateGenerator extends Facade
{
    protected static function getFacadeAccessor()
    {
        return \HusamTariq\FilamentCertificateGenerator\FilamentCertificateGenerator::class;
    }
}
