<?php

declare(strict_types=1);

require __DIR__ . '/vendor/autoload.php';

use Dotenv\Dotenv;
use Luqra\LuqraNowPhp\LuqraNow;
use Luqra\LuqraNowPhp\Models\Operations;
use Luqra\LuqraNowPhp\Models\Errors;

// ── bootstrap ────────────────────────────────────────────────────────────────

Dotenv::createImmutable(__DIR__)->load();

$apiKey  = $_ENV['API_KEY']  ?? throw new \RuntimeException('API_KEY missing in .env');
$baseUrl = $_ENV['BASE_URL'] ?? 'https://staging.api.now.luqra.com';

$builder = LuqraNow::builder()->setSecurity($apiKey);
if ($baseUrl !== 'https://staging.api.now.luqra.com') {
    $builder->setServerURL($baseUrl);
}
$sdk = $builder->build();

// ── helpers ──────────────────────────────────────────────────────────────────

$passed = 0;
$failed = 0;

function check(string $label, bool $condition, string $detail = ''): void
{
    global $passed, $failed;
    if ($condition) {
        echo "\033[32m  ✓\033[0m $label\n";
        $passed++;
    } else {
        echo "\033[31m  ✗\033[0m $label" . ($detail ? " — $detail" : '') . "\n";
        $failed++;
    }
}

function section(string $title): void
{
    echo "\n\033[1m$title\033[0m\n";
}

// ── 1. List originators ───────────────────────────────────────────────────────

section('1. List originators');

try {
    $response = $sdk->originators->list();
    check('HTTP 200', $response->statusCode === 200);
    check('Response body present', $response->object !== null);
    check('data is array', is_array($response->object?->data));

    $originators = $response->object?->data ?? [];
    check('At least one originator exists', count($originators) > 0, 'need at least one originator to continue');

    if (count($originators) === 0) {
        echo "\nNo originators found — cannot continue tests that require an originatorId.\n";
        exit(1);
    }

    $originator   = $originators[0];
    $originatorId = $originator->originatorId;
    check('originatorId is a non-empty string', is_string($originatorId) && $originatorId !== '');
    echo "  → using originatorId: $originatorId\n";
} catch (Errors\APIException $e) {
    check('List originators succeeded', false, "HTTP {$e->statusCode}: {$e->message}");
    exit(1);
}

// ── 2. List contacts ──────────────────────────────────────────────────────────

section('2. List contacts');

try {
    $response = $sdk->contacts->list(originatorId: $originatorId);
    check('HTTP 200', $response->statusCode === 200);
    check('Response body present', $response->object !== null);
    check('data is array', is_array($response->object?->data));
    check('meta.timestamp present', isset($response->object?->meta->timestamp));
    $existingContacts = $response->object?->data ?? [];
    echo '  → ' . count($existingContacts) . " contact(s) found\n";
} catch (Errors\APIException $e) {
    check('List contacts succeeded', false, "HTTP {$e->statusCode}: {$e->message}");
}

// ── 3. Create contact ─────────────────────────────────────────────────────────

section('3. Create contact');

$testEmail = 'sdk-test-' . uniqid() . '@example.com';

try {
    $response = $sdk->contacts->create(
        new Operations\CreateContactRequest(
            bankAccount: new Operations\CreateContactBankAccount(
                achAccountNumber: '123456789',
                achRoutingNumber: '021000021',
                subType: Operations\CreateContactSubType::Checking,
            ),
            email: $testEmail,
            entityType: Operations\CreateContactEntityType::Individual,
            firstName: 'Test',
            lastName: 'User',
            legalAddress: new Operations\CreateContactLegalAddress(
                addressLine1: '123 Main St',
                city: 'New York',
                countryCode: 'US',
                postalCode: '10001',
                state: 'NY',
            ),
            originatorId: $originatorId,
        )
    );
    check('HTTP 201', $response->statusCode === 201);
    check('Response body present', $response->object !== null);

    $contactId = $response->object?->data?->contactId ?? null;
    check('contactId returned', $contactId !== null && $contactId !== '');
    check('createdAt returned', isset($response->object?->data?->createdAt));
    echo "  → created contactId: $contactId\n";
} catch (Errors\ErrorResponseThrowable $e) {
    check('Create contact succeeded', false, $e->getMessage());
    $contactId = null;
} catch (Errors\APIException $e) {
    check('Create contact succeeded', false, "HTTP {$e->statusCode}: {$e->message}");
    $contactId = null;
}

// ── 4. Update contact ─────────────────────────────────────────────────────────

section('4. Update contact');

if ($contactId === null) {
    echo "  Skipped — no contactId from step 3\n";
} else {
    try {
        $response = $sdk->contacts->update(
            body: new Operations\UpdateContactRequestBody(
                firstName: 'Updated',
                lastName: 'User',
            ),
            id: $contactId,
        );
        check('HTTP 200', $response->statusCode === 200);
        check('Response body present', $response->object !== null);
        check('contactId matches', $response->object?->data?->contactId === $contactId);
        check('firstName updated', $response->object?->data?->firstName === 'Updated');
        check('updatedAt present', isset($response->object?->data?->updatedAt));
    } catch (Errors\ErrorResponseThrowable $e) {
        check('Update contact succeeded', false, $e->getMessage());
    } catch (Errors\APIException $e) {
        check('Update contact succeeded', false, "HTTP {$e->statusCode}: {$e->message}");
    }
}

// ── 5. List payments ──────────────────────────────────────────────────────────

section('5. List payments');

try {
    $response = $sdk->payments->list(
        new Operations\ListPaymentsRequest(
            originatorId: $originatorId,
            limit: 10,
            page: 1,
        )
    );
    check('HTTP 200', $response->statusCode === 200);
    check('Response body present', $response->object !== null);
    check('data is array', is_array($response->object?->data));
    check('meta.pagination present', isset($response->object?->meta?->pagination));
    check('pagination.total is numeric', is_numeric($response->object?->meta?->pagination?->total ?? null));
    $existingPayments = $response->object?->data ?? [];
    echo '  → ' . count($existingPayments) . " payment(s) found\n";
} catch (Errors\APIException $e) {
    check('List payments succeeded', false, "HTTP {$e->statusCode}: {$e->message}");
}

// ── 6. Create payment ─────────────────────────────────────────────────────────

section('6. Create payment');

if ($contactId === null) {
    echo "  Skipped — no contactId from step 3\n";
    $paymentId = null;
} else {
    try {
        $response = $sdk->payments->create(
            new Operations\CreatePaymentRequest(
                contactId: $contactId,
                direction: Operations\CreatePaymentDirection::Outbound,
                originatorId: $originatorId,
                paymentAmount: 100,
                paymentNote: 'SDK test',
            )
        );
        check('HTTP 201', $response->statusCode === 201);
        check('Response body present', $response->object !== null);

        $paymentId = $response->object?->data?->paymentId ?? null;
        check('paymentId returned', $paymentId !== null && $paymentId !== '');
        echo "  → created paymentId: $paymentId\n";
    } catch (Errors\ErrorResponseThrowable $e) {
        check('Create payment succeeded', false, $e->getMessage());
        $paymentId = null;
    } catch (Errors\APIException $e) {
        check('Create payment succeeded', false, "HTTP {$e->statusCode}: {$e->message}");
        $paymentId = null;
    }
}

// ── 7. Get payment ────────────────────────────────────────────────────────────

section('7. Get payment');

if ($paymentId === null) {
    echo "  Skipped — no paymentId from step 6\n";
} else {
    try {
        $response = $sdk->payments->get(id: $paymentId);
        check('HTTP 200', $response->statusCode === 200);
        check('Response body present', $response->object !== null);
        check('paymentId matches', $response->object?->data?->paymentId === $paymentId);
        check('status present', isset($response->object?->data?->status));
        check('paymentAmount matches', $response->object?->data?->paymentAmount === 100);
        check('direction is OUTBOUND', $response->object?->data?->direction->value === 'OUTBOUND');
        echo "  → status: " . ($response->object?->data?->status?->value ?? 'n/a') . "\n";
    } catch (Errors\ErrorResponseThrowable $e) {
        check('Get payment succeeded', false, $e->getMessage());
    } catch (Errors\APIException $e) {
        check('Get payment succeeded', false, "HTTP {$e->statusCode}: {$e->message}");
    }
}

// ── summary ───────────────────────────────────────────────────────────────────

$total = $passed + $failed;
echo "\n────────────────────────────────────\n";
echo "Results: \033[32m$passed passed\033[0m, " . ($failed > 0 ? "\033[31m$failed failed\033[0m" : '0 failed') . " / $total total\n";

exit($failed > 0 ? 1 : 0);
