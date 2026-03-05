<!-- Start SDK Example Usage [usage] -->
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
<!-- End SDK Example Usage [usage] -->