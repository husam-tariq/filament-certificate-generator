<?php

namespace Husamtariq\FilamentCertificateGenerator\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @see \Husamtariq\FilamentCertificateGenerator\FilamentCertificateGenerator
 */
class FilamentCertificateGenerator extends Facade
{
    protected static function getFacadeAccessor()
    {
        return \Husamtariq\FilamentCertificateGenerator\FilamentCertificateGenerator::class;
    }
}
