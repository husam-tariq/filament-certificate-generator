<?php

namespace HusamTariq\FilamentCertificateGenerator;

use Filament\Support\Assets\AlpineComponent;
use Filament\Support\Assets\Asset;
use Filament\Support\Assets\Css;
use Filament\Support\Assets\Js;
use Filament\Support\Facades\FilamentAsset;
use Filament\Support\Facades\FilamentIcon;
use Illuminate\Filesystem\Filesystem;
use Livewire\Features\SupportTesting\Testable;
use Spatie\LaravelPackageTools\Commands\InstallCommand;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;
use HusamTariq\FilamentCertificateGenerator\Commands\FilamentCertificateGeneratorCommand;
use HusamTariq\FilamentCertificateGenerator\Testing\TestsFilamentCertificateGenerator;
use BladeUI\Icons\Factory;

class FilamentCertificateGeneratorServiceProvider extends PackageServiceProvider
{
    public static string $name = 'filament-certificate-generator';

    public static string $viewNamespace = 'filament-certificate-generator';

    public function configurePackage(Package $package): void
    {
        /*
         * This class is a Package Service Provider
         *
         * More info: https://github.com/spatie/laravel-package-tools
         */
        $package->name(static::$name)
            ->hasCommands($this->getCommands())
            ->hasInstallCommand(function (InstallCommand $command) {
                $command
                    ->publishConfigFile()
                    ->publishMigrations()
                    ->askToRunMigrations()
                    ->askToStarRepoOnGitHub('husam-tariq/filament-certificate-generator');
            });

        $configFileName = $package->shortName();

        if (file_exists($package->basePath("/../config/{$configFileName}.php"))) {
            $package->hasConfigFile();
        }

        if (file_exists($package->basePath('/../database/migrations'))) {
            $package->hasMigrations($this->getMigrations());
        }

        if (file_exists($package->basePath('/../resources/lang'))) {
            $package->hasTranslations();
        }

        if (file_exists($package->basePath('/../resources/views'))) {
            $package->hasViews(static::$viewNamespace);
        }
        $package->hasRoutes(['web']);
    }

    public function packageRegistered(): void {}

    public function packageBooted(): void
    {

     /*   if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__ . '/../resources/svg' => public_path('vendor/' . static::$name),
            ], static::$name);
        }*/
        // Asset Registration
        FilamentAsset::register(
            $this->getAssets(),
            $this->getAssetPackageName()
        );

        FilamentAsset::registerScriptData(
            $this->getScriptData(),
            $this->getAssetPackageName()
        );

        // Icon Registration
        FilamentIcon::register($this->getIcons());



        // Testing
        Testable::mixin(new TestsFilamentCertificateGenerator);
    }

    public function register()
    {
        $this->callAfterResolving(Factory::class, function (Factory $factory) {
            $factory->add(static::$name, [
                'path' => __DIR__ . '/../resources/svg',
                'prefix' => "certificate",
            ]);
        });
        parent::register();
    }

    protected function getAssetPackageName(): ?string
    {
        return 'husam-tariq/filament-certificate-generator';
    }

    /**
     * @return array<Asset>
     */
    protected function getAssets(): array
    {
        return [
            Css::make('filament-certificate-generator-styles', __DIR__ . '/../resources/dist/filament-certificate-generator.css'),
            Js::make('filament-certificate-generator-libs',  'https://cdn.jsdelivr.net/npm/fabric@5.4.0/dist/fabric.min.js'),
            AlpineComponent::make('certificate-editor', __DIR__ . '/../resources/dist/filament-certificate-generator.js'),
       ];
    }

    /**
     * @return array<class-string>
     */
    protected function getCommands(): array
    {
        return [
            FilamentCertificateGeneratorCommand::class,
        ];
    }

    /**
     * @return array<string>
     */
    protected function getIcons(): array
    {
        return [
           "filament-certificate-generator::certificate-icon"=>  '/../resources/svg/icon.svg'
        ];
    }

    /**
     * @return array<string>
     */
    protected function getRoutes(): array
    {
        return [];
    }

    /**
     * @return array<string, mixed>
     */
    protected function getScriptData(): array
    {
        return [];
    }

    /**
     * @return array<string>
     */
    protected function getMigrations(): array
    {
        return [
            'create_filament-certificate-generator_table',
        ];
    }
}
