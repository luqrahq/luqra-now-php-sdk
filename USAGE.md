<!-- Start SDK Example Usage [usage] -->
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
<!-- End SDK Example Usage [usage] -->