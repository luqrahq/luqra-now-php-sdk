# Originators

## Overview

Originator endpoints

### Available Operations

* [list](#list) - List originators

## list

List originators

### Example Usage

<!-- UsageSnippet language="php" operationID="listOriginators" method="get" path="/v1/originators/" -->
```php
declare(strict_types=1);

require 'vendor/autoload.php';

use Luqra\LuqraNowPhp;

$sdk = LuqraNowPhp\LuqraNow::builder()
    ->setSecurity(
        '<YOUR_BEARER_TOKEN_HERE>'
    )
    ->build();



$response = $sdk->originators->list(
    limit: 20
);

if ($response->object !== null) {
    // handle response
}
```

### Parameters

| Parameter          | Type               | Required           | Description        |
| ------------------ | ------------------ | ------------------ | ------------------ |
| `cursor`           | *?string*          | :heavy_minus_sign: | N/A                |
| `limit`            | *?int*             | :heavy_minus_sign: | N/A                |
| `search`           | *?string*          | :heavy_minus_sign: | N/A                |

### Response

**[?Operations\ListOriginatorsResponse](../../Models/Operations/ListOriginatorsResponse.md)**

### Errors

| Error Type           | Status Code          | Content Type         |
| -------------------- | -------------------- | -------------------- |
| Errors\ErrorResponse | 400, 401             | application/json     |
| Errors\ErrorResponse | 500                  | application/json     |
| Errors\APIException  | 4XX, 5XX             | \*/\*                |