# LuqraNow SDK

## Overview

Luqra NOW API: External API for Luqra NOW

### Available Operations

* [patchV0ContactsId](#patchv0contactsid)
* [getV0Originators](#getv0originators)

## patchV0ContactsId

### Example Usage

```php
declare(strict_types=1);

require 'vendor/autoload.php';

use Luqra\Now;
use Luqra\Now\Models\Operations;

$sdk = Now\LuqraNow::builder()
    ->setSecurity(
        '<YOUR_BEARER_TOKEN_HERE>'
    )
    ->build();

$requestBody = new Operations\PatchV0ContactsIdRequestBody();

$response = $sdk->patchV0ContactsId(
    id: '<id>',
    requestBody: $requestBody

);

if ($response->object !== null) {
    // handle response
}
```

### Parameters

| Parameter                                                                                          | Type                                                                                               | Required                                                                                           | Description                                                                                        |
| -------------------------------------------------------------------------------------------------- | -------------------------------------------------------------------------------------------------- | -------------------------------------------------------------------------------------------------- | -------------------------------------------------------------------------------------------------- |
| `id`                                                                                               | *string*                                                                                           | :heavy_check_mark:                                                                                 | N/A                                                                                                |
| `requestBody`                                                                                      | [Operations\PatchV0ContactsIdRequestBody](../../Models/Operations/PatchV0ContactsIdRequestBody.md) | :heavy_check_mark:                                                                                 | N/A                                                                                                |

### Response

**[?Operations\PatchV0ContactsIdResponse](../../Models/Operations/PatchV0ContactsIdResponse.md)**

### Errors

| Error Type              | Status Code             | Content Type            |
| ----------------------- | ----------------------- | ----------------------- |
| Errors\ErrorResponse    | 400, 401, 403, 404, 409 | application/json        |
| Errors\ErrorResponse    | 500                     | application/json        |
| Errors\APIException     | 4XX, 5XX                | \*/\*                   |

## getV0Originators

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



$response = $sdk->getV0Originators(

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