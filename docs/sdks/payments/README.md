# Payments
(*payments*)

## Overview

Payment intent endpoints

### Available Operations

* [postV0Payments](#postv0payments) - Create payment

## postV0Payments

Create payment

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

$request = new Operations\PostV0PaymentsRequestBody(
    contactId: '<id>',
    direction: Operations\Direction::Outbound,
    originatorId: '<id>',
    paymentAmount: 155444,
);

$response = $sdk->payments->postV0Payments(
    request: $request
);

if ($response->object !== null) {
    // handle response
}
```

### Parameters

| Parameter                                                                                    | Type                                                                                         | Required                                                                                     | Description                                                                                  |
| -------------------------------------------------------------------------------------------- | -------------------------------------------------------------------------------------------- | -------------------------------------------------------------------------------------------- | -------------------------------------------------------------------------------------------- |
| `$request`                                                                                   | [Operations\PostV0PaymentsRequestBody](../../Models/Operations/PostV0PaymentsRequestBody.md) | :heavy_check_mark:                                                                           | The request object to use for the request.                                                   |

### Response

**[?Operations\PostV0PaymentsResponse](../../Models/Operations/PostV0PaymentsResponse.md)**

### Errors

| Error Type           | Status Code          | Content Type         |
| -------------------- | -------------------- | -------------------- |
| Errors\ErrorResponse | 400, 404, 409        | application/json     |
| Errors\ErrorResponse | 500                  | application/json     |
| Errors\APIException  | 4XX, 5XX             | \*/\*                |