# Contacts

## Overview

Contact management endpoints

### Available Operations

* [list](#list) - List contacts
* [create](#create) - Create contact
* [update](#update) - Update contact

## list

List contacts

### Example Usage

<!-- UsageSnippet language="php" operationID="listContacts" method="get" path="/v0/contacts/" -->
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

Creates a contact with address and bank account. Validates ACH routing number against the Fed directory before persistence. Returns 400 VALIDATION_ERROR if the routing number is not found.

### Example Usage

<!-- UsageSnippet language="php" operationID="createContact" method="post" path="/v0/contacts/" -->
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

$request = new Operations\CreateContactRequest(
    bankAccount: new Operations\CreateContactBankAccount(
        achAccountNumber: '<value>',
        achRoutingNumber: '<value>',
        subType: Operations\CreateContactSubType::Checking,
    ),
    email: 'Hyman_Krajcik-OHara@yahoo.com',
    entityType: Operations\CreateContactEntityType::Business,
    firstName: 'Lila',
    lastName: 'Halvorson',
    legalAddress: new Operations\CreateContactLegalAddress(
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

| Parameter                                                                          | Type                                                                               | Required                                                                           | Description                                                                        |
| ---------------------------------------------------------------------------------- | ---------------------------------------------------------------------------------- | ---------------------------------------------------------------------------------- | ---------------------------------------------------------------------------------- |
| `$request`                                                                         | [Operations\CreateContactRequest](../../Models/Operations/CreateContactRequest.md) | :heavy_check_mark:                                                                 | The request object to use for the request.                                         |

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

<!-- UsageSnippet language="php" operationID="updateContact" method="patch" path="/v0/contacts/{id}" -->
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

$body = new Operations\UpdateContactRequestBody();

$response = $sdk->contacts->update(
    id: '<id>',
    body: $body

);

if ($response->object !== null) {
    // handle response
}
```

### Parameters

| Parameter                                                                                  | Type                                                                                       | Required                                                                                   | Description                                                                                |
| ------------------------------------------------------------------------------------------ | ------------------------------------------------------------------------------------------ | ------------------------------------------------------------------------------------------ | ------------------------------------------------------------------------------------------ |
| `id`                                                                                       | *string*                                                                                   | :heavy_check_mark:                                                                         | N/A                                                                                        |
| `body`                                                                                     | [Operations\UpdateContactRequestBody](../../Models/Operations/UpdateContactRequestBody.md) | :heavy_check_mark:                                                                         | N/A                                                                                        |

### Response

**[?Operations\UpdateContactResponse](../../Models/Operations/UpdateContactResponse.md)**

### Errors

| Error Type              | Status Code             | Content Type            |
| ----------------------- | ----------------------- | ----------------------- |
| Errors\ErrorResponse    | 400, 401, 403, 404, 409 | application/json        |
| Errors\ErrorResponse    | 500                     | application/json        |
| Errors\APIException     | 4XX, 5XX                | \*/\*                   |