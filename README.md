# is a PHP package designed to simplify the creation of customized PDF learning certificates within the FilamentPHP framework. It allows you to easily integrate custom background images, personalize certificate details, and generate professional-looking documents

[![Latest Version on Packagist](https://img.shields.io/packagist/v/husam-tariq/filament-certificate-generator.svg?style=flat-square)](https://packagist.org/packages/husam-tariq/filament-certificate-generator)
[![GitHub Tests Action Status](https://img.shields.io/github/actions/workflow/status/husam-tariq/filament-certificate-generator/run-tests.yml?branch=main&label=tests&style=flat-square)](https://github.com/husam-tariq/filament-certificate-generator/actions?query=workflow%3Arun-tests+branch%3Amain)
[![GitHub Code Style Action Status](https://img.shields.io/github/actions/workflow/status/husam-tariq/filament-certificate-generator/fix-php-code-styling.yml?branch=main&label=code%20style&style=flat-square)](https://github.com/husam-tariq/filament-certificate-generator/actions?query=workflow%3A"Fix+PHP+code+styling"+branch%3Amain)
[![Total Downloads](https://img.shields.io/packagist/dt/husam-tariq/filament-certificate-generator.svg?style=flat-square)](https://packagist.org/packages/husam-tariq/filament-certificate-generator)



This is where your description should go. Limit it to a paragraph or two. Consider adding a small example.

## Installation

You can install the package via composer:

```bash
composer require husam-tariq/filament-certificate-generator
```

You can publish and run the migrations with:

```bash
php artisan vendor:publish --tag="filament-certificate-generator-migrations"
php artisan migrate
```

You can publish the config file with:

```bash
php artisan vendor:publish --tag="filament-certificate-generator-config"
```

Optionally, you can publish the views using

```bash
php artisan vendor:publish --tag="filament-certificate-generator-views"
```

This is the contents of the published config file:

```php
return [
];
```

## Usage

```php
$filamentCertificateGenerator = new HusamTariq\FilamentCertificateGenerator();
echo $filamentCertificateGenerator->echoPhrase('Hello, HusamTariq!');
```

## Testing

```bash
composer test
```

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## Contributing

Please see [CONTRIBUTING](.github/CONTRIBUTING.md) for details.

## Security Vulnerabilities

Please review [our security policy](../../security/policy) on how to report security vulnerabilities.

## Credits

- [Hussam Tariq](https://github.com/husam-tariq)
- [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
