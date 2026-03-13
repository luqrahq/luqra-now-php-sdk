# Contacts
(*contacts*)

## Overview

Contact management endpoints

### Available Operations

* [list](#list) - List contacts
* [create](#create) - Create contact
* [update](#update) - Update contact

## list

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



$response = $sdk->contacts->list(
    originatorId: '1d7999d2-66f8-428f-af77-7a969541638f'
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

**[?Operations\ListContactsResponse](../../Models/Operations/ListContactsResponse.md)**

### Errors

| Error Type           | Status Code          | Content Type         |
| -------------------- | -------------------- | -------------------- |
| Errors\ErrorResponse | 400, 401             | application/json     |
| Errors\ErrorResponse | 500                  | application/json     |
| Errors\APIException  | 4XX, 5XX             | \*/\*                |

## create

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

$request = new Operations\CreateContactRequestBody(
    bankAccount: new Operations\BankAccount(
        achAccountNumber: '<value>',
        achRoutingNumber: '<value>',
        subType: Operations\SubType::Checking,
    ),
    email: 'Hyman_Krajcik-OHara@yahoo.com',
    entityType: Operations\EntityType::Business,
    firstName: 'Lila',
    lastName: 'Halvorson',
    legalAddress: new Operations\LegalAddress(
        addressLine1: '624 Turner View',
        city: 'Delray Beach',
        countryCode: 'GT',
        postalCode: '09260',
        state: 'Maryland',
    ),
    originatorId: '0a3f3b5a-5a52-41c3-b54f-8e133700579f',
);

$response = $sdk->contacts->create(
    request: $request
);

if ($response->object !== null) {
    // handle response
}
```

### Parameters

| Parameter                                                                                  | Type                                                                                       | Required                                                                                   | Description                                                                                |
| ------------------------------------------------------------------------------------------ | ------------------------------------------------------------------------------------------ | ------------------------------------------------------------------------------------------ | ------------------------------------------------------------------------------------------ |
| `$request`                                                                                 | [Operations\CreateContactRequestBody](../../Models/Operations/CreateContactRequestBody.md) | :heavy_check_mark:                                                                         | The request object to use for the request.                                                 |

### Response

**[?Operations\CreateContactResponse](../../Models/Operations/CreateContactResponse.md)**

### Errors

| Error Type           | Status Code          | Content Type         |
| -------------------- | -------------------- | -------------------- |
| Errors\ErrorResponse | 400, 404, 409        | application/json     |
| Errors\ErrorResponse | 500                  | application/json     |
| Errors\APIException  | 4XX, 5XX             | \*/\*                |

## update

Update contact

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

$requestBody = new Operations\UpdateContactRequestBody();

$response = $sdk->contacts->update(
    id: '<id>',
    requestBody: $requestBody

);

if ($response->object !== null) {
    // handle response
}
```

### Parameters

| Parameter                                                                                  | Type                                                                                       | Required                                                                                   | Description                                                                                |
| ------------------------------------------------------------------------------------------ | ------------------------------------------------------------------------------------------ | ------------------------------------------------------------------------------------------ | ------------------------------------------------------------------------------------------ |
| `id`                                                                                       | *string*                                                                                   | :heavy_check_mark:                                                                         | N/A                                                                                        |
| `requestBody`                                                                              | [Operations\UpdateContactRequestBody](../../Models/Operations/UpdateContactRequestBody.md) | :heavy_check_mark:                                                                         | N/A                                                                                        |

### Response

**[?Operations\UpdateContactResponse](../../Models/Operations/UpdateContactResponse.md)**

### Errors

| Error Type              | Status Code             | Content Type            |
| ----------------------- | ----------------------- | ----------------------- |
| Errors\ErrorResponse    | 400, 401, 403, 404, 409 | application/json        |
| Errors\ErrorResponse    | 500                     | application/json        |
| Errors\APIException     | 4XX, 5XX                | \*/\*                   |