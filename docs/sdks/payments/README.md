# Payments

## Overview

Payment intent endpoints

### Available Operations

* [list](#list) - List payments
* [create](#create) - Create payment
* [get](#get) - Get payment

## list

List payments

### Example Usage

<!-- UsageSnippet language="php" operationID="listPayments" method="get" path="/v0/payments/" -->
```php
declare(strict_types=1);

require 'vendor/autoload.php';

use Luqra\LuqraNowPhp;
use Luqra\LuqraNowPhp\Models\Operations;

$sdk = LuqraNowPhp\LuqraNow::builder()
    ->setSecurity(
        '<YOUR_BEARER_TOKEN_HERE>'
    )
    ->build();

$request = new Operations\ListPaymentsRequest();

$response = $sdk->payments->list(
    request: $request
);

if ($response->object !== null) {
    // handle response
}
```

### Parameters

| Parameter                                                                        | Type                                                                             | Required                                                                         | Description                                                                      |
| -------------------------------------------------------------------------------- | -------------------------------------------------------------------------------- | -------------------------------------------------------------------------------- | -------------------------------------------------------------------------------- |
| `$request`                                                                       | [Operations\ListPaymentsRequest](../../Models/Operations/ListPaymentsRequest.md) | :heavy_check_mark:                                                               | The request object to use for the request.                                       |

### Response

**[?Operations\ListPaymentsResponse](../../Models/Operations/ListPaymentsResponse.md)**

### Errors

| Error Type           | Status Code          | Content Type         |
| -------------------- | -------------------- | -------------------- |
| Errors\ErrorResponse | 400, 401             | application/json     |
| Errors\ErrorResponse | 500                  | application/json     |
| Errors\APIException  | 4XX, 5XX             | \*/\*                |

## create

Create payment

### Example Usage

<!-- UsageSnippet language="php" operationID="createPayment" method="post" path="/v0/payments/" -->
```php
declare(strict_types=1);

require 'vendor/autoload.php';

use Luqra\LuqraNowPhp;
use Luqra\LuqraNowPhp\Models\Operations;

$sdk = LuqraNowPhp\LuqraNow::builder()
    ->setSecurity(
        '<YOUR_BEARER_TOKEN_HERE>'
    )
    ->build();

$request = new Operations\CreatePaymentRequest(
    contactId: '<id>',
    direction: Operations\CreatePaymentDirection::Outbound,
    originatorId: '<id>',
    paymentAmount: 960074,
    paymentNote: '<value>',
);

$response = $sdk->payments->create(
    request: $request
);

if ($response->object !== null) {
    // handle response
}
```

### Parameters

| Parameter                                                                          | Type                                                                               | Required                                                                           | Description                                                                        |
| ---------------------------------------------------------------------------------- | ---------------------------------------------------------------------------------- | ---------------------------------------------------------------------------------- | ---------------------------------------------------------------------------------- |
| `$request`                                                                         | [Operations\CreatePaymentRequest](../../Models/Operations/CreatePaymentRequest.md) | :heavy_check_mark:                                                                 | The request object to use for the request.                                         |

### Response

**[?Operations\CreatePaymentResponse](../../Models/Operations/CreatePaymentResponse.md)**

### Errors

| Error Type           | Status Code          | Content Type         |
| -------------------- | -------------------- | -------------------- |
| Errors\ErrorResponse | 400, 404, 409        | application/json     |
| Errors\ErrorResponse | 500                  | application/json     |
| Errors\APIException  | 4XX, 5XX             | \*/\*                |

## get

Get payment

### Example Usage

<!-- UsageSnippet language="php" operationID="getPayment" method="get" path="/v0/payments/{id}" -->
```php
declare(strict_types=1);

require 'vendor/autoload.php';

use Luqra\LuqraNowPhp;

$sdk = LuqraNowPhp\LuqraNow::builder()
    ->setSecurity(
        '<YOUR_BEARER_TOKEN_HERE>'
    )
    ->build();



$response = $sdk->payments->get(
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

**[?Operations\GetPaymentResponse](../../Models/Operations/GetPaymentResponse.md)**

### Errors

| Error Type           | Status Code          | Content Type         |
| -------------------- | -------------------- | -------------------- |
| Errors\ErrorResponse | 400, 401, 404        | application/json     |
| Errors\ErrorResponse | 500                  | application/json     |
| Errors\APIException  | 4XX, 5XX             | \*/\*                |