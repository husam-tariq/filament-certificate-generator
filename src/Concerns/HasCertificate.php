<?php

namespace HusamTariq\FilamentCertificateGenerator\Concerns;
use HusamTariq\FilamentCertificateGenerator\Models\CertificateTemplate;

interface HasCertificate
{
    public function getCertificateValues(): array;

    public function getCertificateTemplate(): CertificateTemplate;

}
