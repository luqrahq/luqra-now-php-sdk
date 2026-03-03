# Contacts
(*contacts*)

## Overview

Contact management endpoints

### Available Operations

* [postV1Contacts](#postv1contacts) - Create contact
* [getV1Contacts](#getv1contacts) - List contacts

## postV1Contacts

Create contact

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

$request = new Operations\PostV1ContactsRequestBody(
    originatorId: '39430363-1129-4726-ab2f-656e7f7a63ac',
    firstName: 'Jarrod',
    lastName: 'Smith',
    email: 'Elyssa17@hotmail.com',
    address: new Operations\Address(
        addressLine1: '225 S Maple Street',
        city: 'West Chad',
        state: 'Pennsylvania',
        postalCode: '38469',
        countryCode: 'KR',
    ),
    bankAccount: new Operations\BankAccount(
        subType: Operations\SubType::Savings,
        achRoutingNumber: '<value>',
        achAccountNumber: '<value>',
    ),
);

$response = $sdk->contacts->postV1Contacts(
    request: $request
);

if ($response->object !== null) {
    // handle response
}
```

### Parameters

| Parameter                                                                                    | Type                                                                                         | Required                                                                                     | Description                                                                                  |
| -------------------------------------------------------------------------------------------- | -------------------------------------------------------------------------------------------- | -------------------------------------------------------------------------------------------- | -------------------------------------------------------------------------------------------- |
| `$request`                                                                                   | [Operations\PostV1ContactsRequestBody](../../Models/Operations/PostV1ContactsRequestBody.md) | :heavy_check_mark:                                                                           | The request object to use for the request.                                                   |

### Response

**[?Operations\PostV1ContactsResponse](../../Models/Operations/PostV1ContactsResponse.md)**

### Errors

| Error Type           | Status Code          | Content Type         |
| -------------------- | -------------------- | -------------------- |
| Errors\ErrorResponse | 400, 404, 409        | application/json     |
| Errors\ErrorResponse | 500                  | application/json     |
| Errors\APIException  | 4XX, 5XX             | \*/\*                |

## getV1Contacts

List contacts

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



$response = $sdk->contacts->getV1Contacts(
    originatorId: 'c3d44417-4992-4661-bbef-89efdfc752f5'
);

if ($response->object !== null) {
    // handle response
}
```

### Parameters

| Parameter          | Type               | Required           | Description        |
| ------------------ | ------------------ | ------------------ | ------------------ |
| `originatorId`     | *string*           | :heavy_check_mark: | N/A                |
| `search`           | *?string*          | :heavy_minus_sign: | N/A                |

### Response

**[?Operations\GetV1ContactsResponse](../../Models/Operations/GetV1ContactsResponse.md)**

### Errors

| Error Type           | Status Code          | Content Type         |
| -------------------- | -------------------- | -------------------- |
| Errors\ErrorResponse | 400                  | application/json     |
| Errors\ErrorResponse | 500                  | application/json     |
| Errors\APIException  | 4XX, 5XX             | \*/\*                |