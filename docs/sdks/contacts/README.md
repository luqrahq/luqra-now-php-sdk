# Contacts
(*contacts*)

## Overview

Contact management endpoints

### Available Operations

* [getV0Contacts](#getv0contacts) - List contacts
* [postV0Contacts](#postv0contacts) - Create contact

## getV0Contacts

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



$response = $sdk->contacts->getV0Contacts(
    originatorId: '025261e4-2e17-473c-bc9c-8a61654f55e9'
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

**[?Operations\GetV0ContactsResponse](../../Models/Operations/GetV0ContactsResponse.md)**

### Errors

| Error Type           | Status Code          | Content Type         |
| -------------------- | -------------------- | -------------------- |
| Errors\ErrorResponse | 400, 401             | application/json     |
| Errors\ErrorResponse | 500                  | application/json     |
| Errors\APIException  | 4XX, 5XX             | \*/\*                |

## postV0Contacts

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

$request = new Operations\PostV0ContactsRequestBody(
    bankAccount: new Operations\BankAccount(
        achAccountNumber: '<value>',
        achRoutingNumber: '<value>',
        subType: Operations\SubType::Savings,
    ),
    email: 'Madalyn_Franey32@hotmail.com',
    entityType: Operations\EntityType::Business,
    firstName: 'Bailey',
    lastName: 'Schamberger',
    legalAddress: new Operations\LegalAddress(
        addressLine1: '864 Gusikowski Club',
        city: 'East Osvaldo',
        countryCode: 'PL',
        postalCode: '02220',
        state: 'Virginia',
    ),
    originatorId: '6ddab750-fd0f-4109-a303-e68b411dbb07',
);

$response = $sdk->contacts->postV0Contacts(
    request: $request
);

if ($response->object !== null) {
    // handle response
}
```

### Parameters

| Parameter                                                                                    | Type                                                                                         | Required                                                                                     | Description                                                                                  |
| -------------------------------------------------------------------------------------------- | -------------------------------------------------------------------------------------------- | -------------------------------------------------------------------------------------------- | -------------------------------------------------------------------------------------------- |
| `$request`                                                                                   | [Operations\PostV0ContactsRequestBody](../../Models/Operations/PostV0ContactsRequestBody.md) | :heavy_check_mark:                                                                           | The request object to use for the request.                                                   |

### Response

**[?Operations\PostV0ContactsResponse](../../Models/Operations/PostV0ContactsResponse.md)**

### Errors

| Error Type           | Status Code          | Content Type         |
| -------------------- | -------------------- | -------------------- |
| Errors\ErrorResponse | 400, 404, 409        | application/json     |
| Errors\ErrorResponse | 500                  | application/json     |
| Errors\APIException  | 4XX, 5XX             | \*/\*                |