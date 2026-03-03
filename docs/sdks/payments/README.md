# Payments
(*payments*)

## Overview

Payment intent endpoints

### Available Operations

* [getV1Payments](#getv1payments) - List payments
* [postV1Payments](#postv1payments) - Create payment
* [getV1PaymentsItemId](#getv1paymentsitemid) - Get payment

## getV1Payments

List payments

### Example Usage

```php
declare(strict_types=1);

require 'vendor/autoload.php';

use Luqra\NowPhp;
use Luqra\NowPhp\Models\Operations;

$sdk = NowPhp\LuqraNow::builder()
    ->setSecurity(
        '<YOUR_BEARER_TOKEN_HERE>'
    )
    ->build();

$request = new Operations\GetV1PaymentsRequest();

$response = $sdk->payments->getV1Payments(
    request: $request
);

if ($response->object !== null) {
    // handle response
}
```

### Parameters

| Parameter                                                                          | Type                                                                               | Required                                                                           | Description                                                                        |
| ---------------------------------------------------------------------------------- | ---------------------------------------------------------------------------------- | ---------------------------------------------------------------------------------- | ---------------------------------------------------------------------------------- |
| `$request`                                                                         | [Operations\GetV1PaymentsRequest](../../Models/Operations/GetV1PaymentsRequest.md) | :heavy_check_mark:                                                                 | The request object to use for the request.                                         |

### Response

**[?Operations\GetV1PaymentsResponse](../../Models/Operations/GetV1PaymentsResponse.md)**

### Errors

| Error Type           | Status Code          | Content Type         |
| -------------------- | -------------------- | -------------------- |
| Errors\ErrorResponse | 400                  | application/json     |
| Errors\ErrorResponse | 500                  | application/json     |
| Errors\APIException  | 4XX, 5XX             | \*/\*                |

## postV1Payments

Create payment

### Example Usage

```php
declare(strict_types=1);

require 'vendor/autoload.php';

use Luqra\NowPhp;
use Luqra\NowPhp\Models\Operations;

$sdk = NowPhp\LuqraNow::builder()
    ->setSecurity(
        '<YOUR_BEARER_TOKEN_HERE>'
    )
    ->build();

$request = new Operations\PostV1PaymentsRequestBody(
    originatorId: '<id>',
    contactId: '<id>',
    paymentAmount: 9075.22,
    direction: Operations\Direction::Inbound,
);

$response = $sdk->payments->postV1Payments(
    request: $request
);

if ($response->object !== null) {
    // handle response
}
```

### Parameters

| Parameter                                                                                    | Type                                                                                         | Required                                                                                     | Description                                                                                  |
| -------------------------------------------------------------------------------------------- | -------------------------------------------------------------------------------------------- | -------------------------------------------------------------------------------------------- | -------------------------------------------------------------------------------------------- |
| `$request`                                                                                   | [Operations\PostV1PaymentsRequestBody](../../Models/Operations/PostV1PaymentsRequestBody.md) | :heavy_check_mark:                                                                           | The request object to use for the request.                                                   |

### Response

**[?Operations\PostV1PaymentsResponse](../../Models/Operations/PostV1PaymentsResponse.md)**

### Errors

| Error Type           | Status Code          | Content Type         |
| -------------------- | -------------------- | -------------------- |
| Errors\ErrorResponse | 400, 404, 409        | application/json     |
| Errors\ErrorResponse | 500                  | application/json     |
| Errors\APIException  | 4XX, 5XX             | \*/\*                |

## getV1PaymentsItemId

Get payment

### Example Usage

```php
declare(strict_types=1);

require 'vendor/autoload.php';

use Luqra\NowPhp;

$sdk = NowPhp\LuqraNow::builder()
    ->setSecurity(
        '<YOUR_BEARER_TOKEN_HERE>'
    )
    ->build();



$response = $sdk->payments->getV1PaymentsItemId(
    id: '<id>'
);

if ($response->object !== null) {
    // handle response
}
```

### Parameters

| Parameter          | Type               | Required           | Description        |
| ------------------ | ------------------ | ------------------ | ------------------ |
| `id`               | *string*           | :heavy_check_mark: | N/A                |

### Response

**[?Operations\GetV1PaymentsItemIdResponse](../../Models/Operations/GetV1PaymentsItemIdResponse.md)**

### Errors

| Error Type           | Status Code          | Content Type         |
| -------------------- | -------------------- | -------------------- |
| Errors\ErrorResponse | 400, 404             | application/json     |
| Errors\ErrorResponse | 500                  | application/json     |
| Errors\APIException  | 4XX, 5XX             | \*/\*                |