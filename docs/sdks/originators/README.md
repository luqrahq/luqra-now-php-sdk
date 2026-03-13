# Originators
(*originators*)

## Overview

Originator endpoints

### Available Operations

* [getV0Originators](#getv0originators) - List originators

## getV0Originators

List originators

### Example Usage

```php
declare(strict_types=1);

require 'vendor/autoload.php';

use Luqra\Now;

$sdk = Now\LuqraNow::builder()
    ->setSecurity(
        '<YOUR_BEARER_TOKEN_HERE>'
    )
    ->build();



$response = $sdk->originators->getV0Originators(

);

if ($response->object !== null) {
    // handle response
}
```

### Parameters

| Parameter          | Type               | Required           | Description        |
| ------------------ | ------------------ | ------------------ | ------------------ |
| `search`           | *?string*          | :heavy_minus_sign: | N/A                |

### Response

**[?Operations\GetV0OriginatorsResponse](../../Models/Operations/GetV0OriginatorsResponse.md)**

### Errors

| Error Type           | Status Code          | Content Type         |
| -------------------- | -------------------- | -------------------- |
| Errors\ErrorResponse | 400, 401             | application/json     |
| Errors\ErrorResponse | 500                  | application/json     |
| Errors\APIException  | 4XX, 5XX             | \*/\*                |