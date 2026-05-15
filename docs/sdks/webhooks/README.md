# Webhooks

## Overview

Webhook endpoint management

### Available Operations

* [list](#list) - List webhook endpoints
* [create](#create) - Create webhook endpoint
* [delete](#delete) - Delete webhook endpoint
* [get](#get) - Get webhook endpoint
* [update](#update) - Update webhook endpoint
* [test](#test) - Test webhook endpoint

## list

List webhook endpoints

### Example Usage

<!-- UsageSnippet language="php" operationID="listWebhooks" method="get" path="/v0/webhooks/" -->
```php
declare(strict_types=1);

require 'vendor/autoload.php';

use Luqra\LuqraNowPhp;

$sdk = LuqraNowPhp\LuqraNow::builder()
    ->setSecurity(
        '<YOUR_BEARER_TOKEN_HERE>'
    )
    ->build();



$response = $sdk->webhooks->list(

);

if ($response->object !== null) {
    // handle response
}
```

### Response

**[?Operations\ListWebhooksResponse](../../Models/Operations/ListWebhooksResponse.md)**

### Errors

| Error Type           | Status Code          | Content Type         |
| -------------------- | -------------------- | -------------------- |
| Errors\ErrorResponse | 401                  | application/json     |
| Errors\ErrorResponse | 500                  | application/json     |
| Errors\APIException  | 4XX, 5XX             | \*/\*                |

## create

Create webhook endpoint

### Example Usage

<!-- UsageSnippet language="php" operationID="createWebhook" method="post" path="/v0/webhooks/" -->
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

$request = new Operations\CreateWebhookRequest(
    subscribedEvents: [],
    url: 'https://sophisticated-exterior.org/',
);

$response = $sdk->webhooks->create(
    request: $request
);

if ($response->object !== null) {
    // handle response
}
```

### Parameters

| Parameter                                                                          | Type                                                                               | Required                                                                           | Description                                                                        |
| ---------------------------------------------------------------------------------- | ---------------------------------------------------------------------------------- | ---------------------------------------------------------------------------------- | ---------------------------------------------------------------------------------- |
| `$request`                                                                         | [Operations\CreateWebhookRequest](../../Models/Operations/CreateWebhookRequest.md) | :heavy_check_mark:                                                                 | The request object to use for the request.                                         |

### Response

**[?Operations\CreateWebhookResponse](../../Models/Operations/CreateWebhookResponse.md)**

### Errors

| Error Type           | Status Code          | Content Type         |
| -------------------- | -------------------- | -------------------- |
| Errors\ErrorResponse | 400, 401             | application/json     |
| Errors\ErrorResponse | 500                  | application/json     |
| Errors\APIException  | 4XX, 5XX             | \*/\*                |

## delete

Delete webhook endpoint

### Example Usage

<!-- UsageSnippet language="php" operationID="deleteWebhook" method="delete" path="/v0/webhooks/{id}" -->
```php
declare(strict_types=1);

require 'vendor/autoload.php';

use Luqra\LuqraNowPhp;

$sdk = LuqraNowPhp\LuqraNow::builder()
    ->setSecurity(
        '<YOUR_BEARER_TOKEN_HERE>'
    )
    ->build();



$response = $sdk->webhooks->delete(
    id: '2f4cf1de-535d-40b8-9860-de80b52e1022'
);

if ($response->any !== null) {
    // handle response
}
```

### Parameters

| Parameter          | Type               | Required           | Description        |
| ------------------ | ------------------ | ------------------ | ------------------ |
| `id`               | *string*           | :heavy_check_mark: | N/A                |

### Response

**[?Operations\DeleteWebhookResponse](../../Models/Operations/DeleteWebhookResponse.md)**

### Errors

| Error Type           | Status Code          | Content Type         |
| -------------------- | -------------------- | -------------------- |
| Errors\ErrorResponse | 400, 401, 404        | application/json     |
| Errors\ErrorResponse | 500                  | application/json     |
| Errors\APIException  | 4XX, 5XX             | \*/\*                |

## get

Get webhook endpoint

### Example Usage

<!-- UsageSnippet language="php" operationID="getWebhook" method="get" path="/v0/webhooks/{id}" -->
```php
declare(strict_types=1);

require 'vendor/autoload.php';

use Luqra\LuqraNowPhp;

$sdk = LuqraNowPhp\LuqraNow::builder()
    ->setSecurity(
        '<YOUR_BEARER_TOKEN_HERE>'
    )
    ->build();



$response = $sdk->webhooks->get(
    id: 'deeb5a05-74d4-40ad-b4be-a9265fd49428'
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

**[?Operations\GetWebhookResponse](../../Models/Operations/GetWebhookResponse.md)**

### Errors

| Error Type           | Status Code          | Content Type         |
| -------------------- | -------------------- | -------------------- |
| Errors\ErrorResponse | 400, 401, 404        | application/json     |
| Errors\ErrorResponse | 500                  | application/json     |
| Errors\APIException  | 4XX, 5XX             | \*/\*                |

## update

Update webhook endpoint

### Example Usage

<!-- UsageSnippet language="php" operationID="updateWebhook" method="patch" path="/v0/webhooks/{id}" -->
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

$body = new Operations\UpdateWebhookRequestBody();

$response = $sdk->webhooks->update(
    id: 'e80a2243-1644-46d7-8f13-7957345de978',
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
| `body`                                                                                     | [Operations\UpdateWebhookRequestBody](../../Models/Operations/UpdateWebhookRequestBody.md) | :heavy_check_mark:                                                                         | N/A                                                                                        |

### Response

**[?Operations\UpdateWebhookResponse](../../Models/Operations/UpdateWebhookResponse.md)**

### Errors

| Error Type           | Status Code          | Content Type         |
| -------------------- | -------------------- | -------------------- |
| Errors\ErrorResponse | 400, 401, 404        | application/json     |
| Errors\ErrorResponse | 500                  | application/json     |
| Errors\APIException  | 4XX, 5XX             | \*/\*                |

## test

Test webhook endpoint

### Example Usage

<!-- UsageSnippet language="php" operationID="testWebhook" method="post" path="/v0/webhooks/{id}/test" -->
```php
declare(strict_types=1);

require 'vendor/autoload.php';

use Luqra\LuqraNowPhp;

$sdk = LuqraNowPhp\LuqraNow::builder()
    ->setSecurity(
        '<YOUR_BEARER_TOKEN_HERE>'
    )
    ->build();



$response = $sdk->webhooks->test(
    id: '4e1ca4d4-efdb-41f2-a630-999c92178d10'
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

**[?Operations\TestWebhookResponse](../../Models/Operations/TestWebhookResponse.md)**

### Errors

| Error Type           | Status Code          | Content Type         |
| -------------------- | -------------------- | -------------------- |
| Errors\ErrorResponse | 400, 401, 404        | application/json     |
| Errors\ErrorResponse | 500                  | application/json     |
| Errors\APIException  | 4XX, 5XX             | \*/\*                |