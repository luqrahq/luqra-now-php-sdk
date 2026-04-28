# luqra/now-php

Developer-friendly, idiomatic PHP SDK for the *luqra/now-php* API.

<div align="left">
    <a href="https://www.scalar.com/?utm_source=luqra/now-php&utm_campaign=php"><img src="https://custom-icon-badges.demolab.com/badge/-Built%20By%20scalar+speakeasy-212015?style=for-the-badge&logo=scalar&labelColor=252525" /></a>
    <a href="https://opensource.org/licenses/MIT">
        <img src="https://img.shields.io/badge/License-MIT-blue.svg" style="width: 100px; height: 28px;" />
    </a>
</div>

<br />

## Summary

Luqra Now API: External API for Luqra Now
<!-- End Summary [summary] -->

<!-- Start Summary [summary] -->
## Summary

Luqra Now API: External API for Luqra Now
<!-- End Summary [summary] -->

<!-- Start Table of Contents [toc] -->
## Table of Contents
<!-- $toc-max-depth=2 -->
* [luqra/now-php](#luqranow-php)
  * [SDK Installation](#sdk-installation)
  * [SDK Example Usage](#sdk-example-usage)
  * [Authentication](#authentication)
  * [Available Resources and Operations](#available-resources-and-operations)
  * [Error Handling](#error-handling)
  * [Server Selection](#server-selection)
  * [Development](#development)
  * [Contributions](#contributions)

<!-- End Table of Contents [toc] -->

<!-- Start SDK Installation [installation] -->
## SDK Installation

> [!TIP]
> To finish publishing your SDK you must [run your first generation action](https://www.speakeasy.com/docs/github-setup#step-by-step-guide).


The SDK relies on [Composer](https://getcomposer.org/) to manage its dependencies.

To install the SDK first add the below to your `composer.json` file:

```json
{
    "repositories": [
        {
            "type": "github",
            "url": "<UNSET>.git"
        }
    ],
    "require": {
        "luqra/now-php": "*"
    }
}
```

Then run the following command:

```bash
composer update
```
<!-- End SDK Installation [installation] -->

<!-- Start SDK Example Usage [usage] -->
## SDK Example Usage

### Example

```php
declare(strict_types=1);

require 'vendor/autoload.php';

use Luqra\LuqraNowPhp;

$sdk = LuqraNowPhp\LuqraNow::builder()
    ->setSecurity(
        '<YOUR_BEARER_TOKEN_HERE>'
    )
    ->build();



$response = $sdk->contacts->list(
    originatorId: '1d7999d2-66f8-428f-af77-7a969541638f'
);

if ($response->object !== null) {
    // handle response
}
```
<!-- End SDK Example Usage [usage] -->

<!-- Start Authentication [security] -->
## Authentication

### Per-Client Security Schemes

This SDK supports the following security scheme globally:

| Name         | Type | Scheme      |
| ------------ | ---- | ----------- |
| `bearerAuth` | http | HTTP Bearer |

To authenticate with the API the `bearerAuth` parameter must be set when initializing the SDK. For example:
```php
declare(strict_types=1);

require 'vendor/autoload.php';

use Luqra\LuqraNowPhp;

$sdk = LuqraNowPhp\LuqraNow::builder()
    ->setSecurity(
        '<YOUR_BEARER_TOKEN_HERE>'
    )
    ->build();



$response = $sdk->contacts->list(
    originatorId: '1d7999d2-66f8-428f-af77-7a969541638f'
);

if ($response->object !== null) {
    // handle response
}
```
<!-- End Authentication [security] -->

<!-- Start Available Resources and Operations [operations] -->
## Available Resources and Operations

<details open>
<summary>Available methods</summary>

### [Contacts](docs/sdks/contacts/README.md)

* [list](docs/sdks/contacts/README.md#list) - List contacts
* [create](docs/sdks/contacts/README.md#create) - Create contact
* [update](docs/sdks/contacts/README.md#update) - Update contact

### [Originators](docs/sdks/originators/README.md)

* [list](docs/sdks/originators/README.md#list) - List originators

### [Payments](docs/sdks/payments/README.md)

* [list](docs/sdks/payments/README.md#list) - List payments
* [create](docs/sdks/payments/README.md#create) - Create payment
* [get](docs/sdks/payments/README.md#get) - Get payment

</details>
<!-- End Available Resources and Operations [operations] -->

<!-- Start Error Handling [errors] -->
## Error Handling

Handling errors in this SDK should largely match your expectations. All operations return a response object or throw an exception.

By default an API error will raise a `Errors\APIException` exception, which has the following properties:

| Property       | Type                                    | Description           |
|----------------|-----------------------------------------|-----------------------|
| `$message`     | *string*                                | The error message     |
| `$statusCode`  | *int*                                   | The HTTP status code  |
| `$rawResponse` | *?\Psr\Http\Message\ResponseInterface*  | The raw HTTP response |
| `$body`        | *string*                                | The response content  |

When custom error responses are specified for an operation, the SDK may also throw their associated exception. You can refer to respective *Errors* tables in SDK docs for more details on possible exception types for each operation. For example, the `list` method throws the following exceptions:

| Error Type           | Status Code | Content Type     |
| -------------------- | ----------- | ---------------- |
| Errors\ErrorResponse | 400, 401    | application/json |
| Errors\ErrorResponse | 500         | application/json |
| Errors\APIException  | 4XX, 5XX    | \*/\*            |

### Example

```php
declare(strict_types=1);

require 'vendor/autoload.php';

use Luqra\LuqraNowPhp;
use Luqra\LuqraNowPhp\Models\Errors;

$sdk = LuqraNowPhp\LuqraNow::builder()
    ->setSecurity(
        '<YOUR_BEARER_TOKEN_HERE>'
    )
    ->build();

try {
    $response = $sdk->contacts->list(
        originatorId: '1d7999d2-66f8-428f-af77-7a969541638f'
    );

    if ($response->object !== null) {
        // handle response
    }
} catch (Errors\ErrorResponseThrowable $e) {
    // handle $e->$container data
    throw $e;
} catch (Errors\ErrorResponseThrowable $e) {
    // handle $e->$container data
    throw $e;
} catch (Errors\APIException $e) {
    // handle default exception
    throw $e;
}
```
<!-- End Error Handling [errors] -->

<!-- Start Server Selection [server] -->
## Server Selection

### Select Server by Index

You can override the default server globally using the `setServerIndex(int $serverIdx)` builder method when initializing the SDK client instance. The selected server will then be used as the default on the operations that use it. This table lists the indexes associated with the available servers:

| #   | Server                              | Description |
| --- | ----------------------------------- | ----------- |
| 0   | `https://staging.api.now.luqra.com` | Sandbox     |
| 1   | `https://api.now.luqra.com`         | Production  |

#### Example

```php
declare(strict_types=1);

require 'vendor/autoload.php';

use Luqra\LuqraNowPhp;

$sdk = LuqraNowPhp\LuqraNow::builder()
    ->setServerIndex(0)
    ->setSecurity(
        '<YOUR_BEARER_TOKEN_HERE>'
    )
    ->build();



$response = $sdk->contacts->list(
    originatorId: '1d7999d2-66f8-428f-af77-7a969541638f'
);

if ($response->object !== null) {
    // handle response
}
```

### Override Server URL Per-Client

The default server can also be overridden globally using the `setServerUrl(string $serverUrl)` builder method when initializing the SDK client instance. For example:
```php
declare(strict_types=1);

require 'vendor/autoload.php';

use Luqra\LuqraNowPhp;

$sdk = LuqraNowPhp\LuqraNow::builder()
    ->setServerURL('https://api.now.luqra.com')
    ->setSecurity(
        '<YOUR_BEARER_TOKEN_HERE>'
    )
    ->build();



$response = $sdk->contacts->list(
    originatorId: '1d7999d2-66f8-428f-af77-7a969541638f'
);

if ($response->object !== null) {
    // handle response
}
```
<!-- End Server Selection [server] -->

## Development

This SDK is generated by [Speakeasy](https://speakeasy.com). To regenerate it after API changes:

### Prerequisites

- [Speakeasy CLI](https://www.speakeasy.com/docs/speakeasy-cli/getting-started)
- [Composer](https://getcomposer.org/)
- `openapi.json` placed in the root of this project (see step 1)

### Steps

**1. Fetch the latest OpenAPI spec**

The `openapi.json` file must be present in the root project folder before regenerating the SDK. Fetch it with:

```bash
curl -o openapi.json https://staging.docs.now.luqra.com/api/openapi.json > ./openapi.json
```

**2. Install dependencies**

```bash
composer install
```

**3. Regenerate the SDK**

```bash
speakeasy run
```

The command validates the spec, regenerates all source files, and runs PHPStan to verify the output.

## Contributions

While we value open-source contributions to this SDK, this library is generated programmatically. Any manual changes added to internal files will be overwritten on the next generation. 
We look forward to hearing your feedback. Feel free to open a PR or an issue with a proof of concept and we'll do our best to include it in a future release.

### SDK Created by [Scalar](https://www.scalar.com/?utm_source=luqra/now-php&utm_campaign=php)
<!-- Placeholder for Future Speakeasy SDK Sections -->
