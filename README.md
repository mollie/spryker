# Mollie Spryker Payment Module (Standalone)
[![Static Code Analysis](https://github.com/mollie/spryker/actions/workflows/static-code-analysis.yml/badge.svg?branch=main)](https://github.com/mollie/spryker/actions/workflows/static-code-analysis.yml)
[![Codeception Tests](https://github.com/mollie/spryker/actions/workflows/codeception-tests.yml/badge.svg?branch=main)](https://github.com/mollie/spryker/actions/workflows/codeception-tests.yml)
A standalone Spryker-convention module under the `Mollie` namespace for integrating the [Mollie PHP API client](https://github.com/mollie/mollie-api-php). This repository provides an initial, convention-compliant scaffold for Business, Communication, Persistence, and Client layers and prepares you for further development inside a Spryker (SCOS) environment.

## Features
- Spryker-aligned folder structure under `Mollie\\Zed\\Mollie` (Zed layers) and a dedicated Client layer under `Mollie\\Client\\Mollie`.
- Composer setup including the official `mollie/mollie-api-php` dependency.
- Basic Client layer that demonstrates initializing the `MollieApiClient` with an API key.
- Skeleton classes for Business, Communication, and Persistence layers for future extensibility.
- Configuration placeholders and a minimal CI workflow.

## Requirements
- PHP >= 8.1
- Composer
- (For full integration) A Spryker SCOS project

## Installation
Add the package to your project (if hosted privately, adjust the VCS repository settings in your root composer.json) [TO BE ADJUSTED]: 

```bash
composer require mollie/spryker-payment
```

## Client Layer and Mollie API Integration
The Client layer is responsible for working with external clients. This module includes an example client:

```php
use Mollie\Client\Mollie\MollieFactory;

$factory = new MollieFactory();
$client = $factory->createMollieClient(); // resolves API key from env/config
$mollie = $client->getClient();

// Example usage (see Mollie API docs for details):
// $payment = $mollie->payments->create([...]);
```

In a full Spryker setup, you would typically provide the API key via configuration and wire the client into the container using the module's `DependencyProvider` and your project layer.

## Configuration
This repository includes a simple placeholder in `config/config_default.php`:

```php
return [
    'MOLLIE_API_KEY' => getenv('MOLLIE_API_KEY') ?: 'change-me',
];
```

In Spryker SCOS, prefer using your project-level config (config/Shared or config/Default) and environment variables to manage secrets.

## Dependency Provider
`MollieDependencyProvider` defines the constant key for wiring dependencies:

```php
public const CLIENT_MOLLIE = 'CLIENT_MOLLIE';
```

In a full SCOS environment, extend `AbstractDependencyProvider` and register services in a container. For this standalone scaffold, we only expose the constant for consumers.

## Coding Standards and CI
- All PHP files use `declare(strict_types=1);` and follow PSR-4 autoloading.
- A minimal GitHub Actions workflow is provided to run Composer validation and PHP linting.


## Contributing
1. Create a feature branch.
2. Follow Spryker architecture conventions (Business, Communication, Persistence, Client).
3. Add tests under the `tests/` directory.
4. Open a PR.

## License
MIT
