# Statements

## Overview

Monthly statement endpoints

### Available Operations

* [list](#list) - List statements
* [getDownloadUrl](#getdownloadurl) - Get a short-lived signed URL for downloading a statement PDF

## list

List statements

### Example Usage

<!-- UsageSnippet language="php" operationID="listStatements" method="get" path="/v0/statements/" -->
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

$request = new Operations\ListStatementsRequest();

$response = $sdk->statements->list(
    request: $request
);

if ($response->object !== null) {
    // handle response
}
```

### Parameters

| Parameter                                                                            | Type                                                                                 | Required                                                                             | Description                                                                          |
| ------------------------------------------------------------------------------------ | ------------------------------------------------------------------------------------ | ------------------------------------------------------------------------------------ | ------------------------------------------------------------------------------------ |
| `$request`                                                                           | [Operations\ListStatementsRequest](../../Models/Operations/ListStatementsRequest.md) | :heavy_check_mark:                                                                   | The request object to use for the request.                                           |

### Response

**[?Operations\ListStatementsResponse](../../Models/Operations/ListStatementsResponse.md)**

### Errors

| Error Type           | Status Code          | Content Type         |
| -------------------- | -------------------- | -------------------- |
| Errors\ErrorResponse | 400, 401             | application/json     |
| Errors\ErrorResponse | 500                  | application/json     |
| Errors\APIException  | 4XX, 5XX             | \*/\*                |

## getDownloadUrl

Get a short-lived signed URL for downloading a statement PDF

### Example Usage

<!-- UsageSnippet language="php" operationID="getStatementDownloadUrl" method="get" path="/v0/statements/{id}/download" -->
```php
declare(strict_types=1);

require 'vendor/autoload.php';

use Luqra\LuqraNowPhp;

$sdk = LuqraNowPhp\LuqraNow::builder()
    ->setSecurity(
        '<YOUR_BEARER_TOKEN_HERE>'
    )
    ->build();



$response = $sdk->statements->getDownloadUrl(
    id: 'e31bfa3a-2398-4a8e-9c08-85bf0c1b0bf8'
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

**[?Operations\GetStatementDownloadUrlResponse](../../Models/Operations/GetStatementDownloadUrlResponse.md)**

### Errors

| Error Type           | Status Code          | Content Type         |
| -------------------- | -------------------- | -------------------- |
| Errors\ErrorResponse | 400, 401, 404, 409   | application/json     |
| Errors\ErrorResponse | 500                  | application/json     |
| Errors\APIException  | 4XX, 5XX             | \*/\*                |