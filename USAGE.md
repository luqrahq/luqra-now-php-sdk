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
<!-- End SDK Example Usage [usage] -->