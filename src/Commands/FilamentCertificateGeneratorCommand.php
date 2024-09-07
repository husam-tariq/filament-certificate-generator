<?php

namespace Husamtariq\FilamentCertificateGenerator\Commands;

use Illuminate\Console\Command;

class FilamentCertificateGeneratorCommand extends Command
{
    public $signature = 'filament-certificate-generator';

    public $description = 'My command';

    public function handle(): int
    {
        $this->comment('All done');

        return self::SUCCESS;
    }
}
