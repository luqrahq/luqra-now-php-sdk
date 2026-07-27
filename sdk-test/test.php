<?php

declare(strict_types=1);

require __DIR__ . '/vendor/autoload.php';

use Dotenv\Dotenv;
use Luqra\LuqraNowPhp\LuqraNow;
use Luqra\LuqraNowPhp\Models\Operations;
use Luqra\LuqraNowPhp\Models\Errors;

// ── bootstrap ────────────────────────────────────────────────────────────────
//
// WARNING: run this script only with the dedicated SDK-test org API key.
// The org is baked into the key (luqra-now.org.{env}.*), so running with a
// customer's key will create real contacts and payments under their data.
//
// Locally we load API_KEY/BASE_URL from sdk-test/.env; in CI the workflow
// injects them via the `env:` block. safeLoad() tolerates a missing file
// so the same script works in both environments.

Dotenv::createImmutable(__DIR__)->safeLoad();

$apiKey  = $_ENV['API_KEY']  ?? getenv('API_KEY')  ?: throw new \RuntimeException('API_KEY not set (sdk-test/.env locally, env var in CI)');
$baseUrl = $_ENV['BASE_URL'] ?? getenv('BASE_URL') ?: 'https://staging.api.now.luqra.com';

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

// ── 0. Server selection ───────────────────────────────────────────────────────

section('0. Server selection');

try {
    $sdkByIndex = LuqraNow::builder()->setServerIndex(0)->setSecurity($apiKey)->build();
    $response   = $sdkByIndex->originators->list();
    check('setServerIndex(0) produces a working client', $response->statusCode === 200);
} catch (Errors\APIException $e) {
    check('setServerIndex(0) produces a working client', false, "HTTP " . $e->statusCode . ": " . $e->getMessage());
}

try {
    $sdkByUrl = LuqraNow::builder()->setServerURL('https://staging.api.now.luqra.com')->setSecurity($apiKey)->build();
    $response  = $sdkByUrl->originators->list();
    check('setServerURL(...) produces a working client', $response->statusCode === 200);
} catch (Errors\APIException $e) {
    check('setServerURL(...) produces a working client', false, "HTTP " . $e->statusCode . ": " . $e->getMessage());
}

// ── 1. List originators ───────────────────────────────────────────────────────

section('1. List originators');

try {
    $response = $sdk->originators->list();
    check('HTTP 200', $response->statusCode === 200);
    check('Response body present', $response->object !== null);
    check('data is array', is_array($response->object?->data));
    check('meta.pagination present', isset($response->object?->meta?->pagination));
    check('pagination.limit is numeric', is_numeric($response->object?->meta?->pagination?->limit ?? null));

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
    check('List originators succeeded', false, "HTTP " . $e->statusCode . ": " . $e->getMessage());
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
    check('meta.pagination present', isset($response->object?->meta?->pagination));
    check('pagination.limit is numeric', is_numeric($response->object?->meta?->pagination?->limit ?? null));
    $existingContacts = $response->object?->data ?? [];
    echo '  → ' . count($existingContacts) . " contact(s) found\n";
} catch (Errors\APIException $e) {
    check('List contacts succeeded', false, "HTTP " . $e->statusCode . ": " . $e->getMessage());
}

// ── 2b. Contacts cursor pagination ────────────────────────────────────────────
// The contacts list gained cursor/limit params in this release; verify the
// limit is honored, nextCursor deserializes, and following it yields a
// different page.

section('2b. Contacts cursor pagination');

try {
    $page1 = $sdk->contacts->list(originatorId: $originatorId, limit: 1);
    check('page 1 HTTP 200', $page1->statusCode === 200);
    check('limit respected on page 1', count($page1->object?->data ?? []) <= 1);

    $nextCursor = $page1->object?->meta?->pagination?->nextCursor ?? null;
    $id1        = $page1->object?->data[0]?->contactId ?? null;

    if ($nextCursor !== null && $nextCursor !== '' && $id1 !== null) {
        $page2 = $sdk->contacts->list(originatorId: $originatorId, cursor: $nextCursor, limit: 1);
        check('cursor page HTTP 200', $page2->statusCode === 200);
        check('cursor page limit respected', count($page2->object?->data ?? []) <= 1);

        $id2 = $page2->object?->data[0]?->contactId ?? null;
        if ($id2 !== null) {
            check('cursor page returns different contactId', $id1 !== $id2, "id1=$id1 id2=$id2");
        } else {
            echo "  → cursor page returned no items\n";
        }
    } else {
        echo "  → skipping cursor follow (fewer than 2 contacts or no nextCursor)\n";
    }
} catch (Errors\APIException $e) {
    check('Contacts cursor pagination succeeded', false, "HTTP " . $e->statusCode . ": " . $e->getMessage());
}

// ── 3. Create contact ─────────────────────────────────────────────────────────

section('3. Create contact');

$testEmail      = 'sdk-test-' . uniqid() . '@example.com';
$testAccountNum = (string) rand(100000000, 999999999);

try {
    $response = $sdk->contacts->create(
        new Operations\CreateContactRequest(
            bankAccount: new Operations\CreateContactBankAccount(
                achAccountNumber: $testAccountNum,
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
    check('Create contact succeeded', false, "HTTP " . $e->statusCode . ": " . $e->getMessage());
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
        check('Update contact succeeded', false, "HTTP " . $e->statusCode . ": " . $e->getMessage());
    }
}

// ── 5. List payments ──────────────────────────────────────────────────────────

section('5. List payments');

try {
    $response = $sdk->payments->list(
        new Operations\ListPaymentsRequest(
            originatorId: $originatorId,
            limit: 10,
        )
    );
    check('HTTP 200', $response->statusCode === 200);
    check('Response body present', $response->object !== null);
    check('data is array', is_array($response->object?->data));
    check('meta.pagination present', isset($response->object?->meta?->pagination));
    check('pagination.limit is numeric', is_numeric($response->object?->meta?->pagination?->limit ?? null));
    $existingPayments = $response->object?->data ?? [];
    echo '  → ' . count($existingPayments) . " payment(s) found\n";
} catch (Errors\APIException $e) {
    check('List payments succeeded', false, "HTTP " . $e->statusCode . ": " . $e->getMessage());
}

// ── 5b. Cursor pagination plumbing ────────────────────────────────────────────
// Verifies cursor-based pagination: limit param, nextCursor deserialization,
// and that following a cursor yields a different page. A regen can silently
// rename a param or break cursor encoding.

section('5b. Cursor pagination plumbing');

try {
    $page1 = $sdk->payments->list(new Operations\ListPaymentsRequest(
        originatorId: $originatorId, limit: 1,
    ));

    check('page 1 HTTP 200', $page1->statusCode === 200);
    check('limit respected on page 1', count($page1->object?->data ?? []) <= 1);
    check('meta.pagination.limit present', is_numeric($page1->object?->meta?->pagination?->limit ?? null));

    $nextCursor = $page1->object?->meta?->pagination?->nextCursor ?? null;
    $id1        = $page1->object?->data[0]?->paymentId ?? null;

    if ($nextCursor !== null && $id1 !== null) {
        $page2 = $sdk->payments->list(new Operations\ListPaymentsRequest(
            originatorId: $originatorId, limit: 1, cursor: $nextCursor,
        ));
        check('cursor page HTTP 200', $page2->statusCode === 200);
        check('cursor page limit respected', count($page2->object?->data ?? []) <= 1);

        $id2 = $page2->object?->data[0]?->paymentId ?? null;
        if ($id2 !== null) {
            check('cursor page returns different paymentId', $id1 !== $id2, "id1=$id1 id2=$id2");
        } else {
            echo "  → cursor page returned no items\n";
        }
    } else {
        echo "  → skipping cursor follow (fewer than 2 payments or no nextCursor)\n";
    }
} catch (Errors\APIException $e) {
    check('Cursor pagination plumbing succeeded', false, "HTTP " . $e->statusCode . ": " . $e->getMessage());
}

// ── 5c. Payment list filters ──────────────────────────────────────────────────
// Verifies direction and status query-param filters are wired through the SDK
// and that the API actually honours them.

section('5c. Payment list filters');

try {
    $outbound = $sdk->payments->list(new Operations\ListPaymentsRequest(
        originatorId: $originatorId,
        direction: Operations\QueryParamDirection::Outbound,
        limit: 5,
    ));
    check('direction=OUTBOUND filter HTTP 200', $outbound->statusCode === 200);
    check('direction filter data is array', is_array($outbound->object?->data));
    if (count($outbound->object?->data ?? []) > 0) {
        $allOutbound = true;
        foreach ($outbound->object?->data as $p) {
            if ($p->direction->value !== 'OUTBOUND') { $allOutbound = false; break; }
        }
        check('direction filter: all returned payments are OUTBOUND', $allOutbound);
    } else {
        echo "  → no OUTBOUND payments to verify direction filter\n";
    }

    $byStatus = $sdk->payments->list(new Operations\ListPaymentsRequest(
        originatorId: $originatorId,
        status: Operations\QueryParamStatus::Completed,
        limit: 5,
    ));
    check('status=COMPLETED filter HTTP 200', $byStatus->statusCode === 200);
    check('status filter data is array', is_array($byStatus->object?->data));
    if (count($byStatus->object?->data ?? []) > 0) {
        $allCompleted = true;
        foreach ($byStatus->object?->data as $p) {
            if ($p->status->value !== 'COMPLETED') { $allCompleted = false; break; }
        }
        check('status filter: all returned payments are COMPLETED', $allCompleted);
    } else {
        echo "  → no COMPLETED payments to verify status filter\n";
    }

    echo '  → ' . count($outbound->object?->data ?? []) . " OUTBOUND, ";
    echo count($byStatus->object?->data ?? []) . " COMPLETED payment(s)\n";
} catch (Errors\APIException $e) {
    check('Payment list filters succeeded', false, "HTTP " . $e->statusCode . ": " . $e->getMessage());
}

// ── 6. Create payment ─────────────────────────────────────────────────────────

section('6. Create payment');

if ($contactId === null) {
    echo "  Skipped — no contactId from step 3\n";
    $paymentId = null;
} else {
    try {
        $idempotencyKey = 'sdk-test-' . uniqid();
        $response = $sdk->payments->create(
            body: new Operations\CreatePaymentRequestBody(
                contactId: $contactId,
                direction: Operations\CreatePaymentDirection::Outbound,
                originatorId: $originatorId,
                paymentAmount: 100,
                paymentNote: 'SDK test',
            ),
            idempotencyKey: $idempotencyKey,
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
        check('Create payment succeeded', false, "HTTP " . $e->statusCode . ": " . $e->getMessage());
        $paymentId = null;
    }
}

// ── 6b. Idempotency key deduplication ─────────────────────────────────────────
// Submitting the same idempotencyKey twice must return the same paymentId.

section('6b. Idempotency key deduplication');

if ($contactId === null || $paymentId === null) {
    echo "  Skipped — no contactId/paymentId from earlier steps\n";
} else {
    try {
        $response2 = $sdk->payments->create(
            body: new Operations\CreatePaymentRequestBody(
                contactId: $contactId,
                direction: Operations\CreatePaymentDirection::Outbound,
                originatorId: $originatorId,
                paymentAmount: 100,
                paymentNote: 'SDK test',
            ),
            idempotencyKey: $idempotencyKey,
        );
        check('Duplicate idempotencyKey returns 2xx', in_array($response2->statusCode, [200, 201]));
        $dupePaymentId = $response2->object?->data?->paymentId ?? null;
        check('Duplicate idempotencyKey returns same paymentId', $dupePaymentId === $paymentId, "got $dupePaymentId expected $paymentId");
    } catch (Errors\ErrorResponseThrowable $e) {
        check('Duplicate idempotencyKey succeeded', false, $e->getMessage());
    } catch (Errors\APIException $e) {
        check('Duplicate idempotencyKey succeeded', false, "HTTP " . $e->statusCode . ": " . $e->getMessage());
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
        check('paymentRail present', isset($response->object?->data?->paymentRail));
        check('currencyCode present', isset($response->object?->data?->currencyCode));
        check('discountFee is int', is_int($response->object?->data?->discountFee));
        check('flatFee is int', is_int($response->object?->data?->flatFee));
        check('returnFee is int', is_int($response->object?->data?->returnFee));
        check('createdAt is DateTime', $response->object?->data?->createdAt instanceof \DateTime);
        check('contact object present', $response->object?->data?->contact !== null);
        check('contact.id is string', is_string($response->object?->data?->contact?->id ?? null) && ($response->object?->data?->contact?->id ?? '') !== '');
        check('originator object present', $response->object?->data?->originator !== null);
        check('originator.id is string', is_string($response->object?->data?->originator?->id ?? null) && ($response->object?->data?->originator?->id ?? '') !== '');
        check('failureReason is null or string', is_null($response->object?->data?->failureReason) || is_string($response->object?->data?->failureReason));
        check('returnReasonCode is null or string', is_null($response->object?->data?->returnReasonCode) || is_string($response->object?->data?->returnReasonCode));
        check('failureMessage is null or string', is_null($response->object?->data?->failureMessage) || is_string($response->object?->data?->failureMessage));
        echo "  → status: " . ($response->object?->data?->status?->value ?? 'n/a') . "\n";
        echo "  → paymentRail: " . ($response->object?->data?->paymentRail?->value ?? 'n/a') . "\n";
    } catch (Errors\ErrorResponseThrowable $e) {
        check('Get payment succeeded', false, $e->getMessage());
    } catch (Errors\APIException $e) {
        check('Get payment succeeded', false, "HTTP " . $e->statusCode . ": " . $e->getMessage());
    }
}

// ── 8. Error handling ─────────────────────────────────────────────────────────

section('8. Error handling');

// 8a. 401 — bad token (verifies setSecurity wiring)
try {
    $sdkBadAuth = LuqraNow::builder()->setSecurity('bad-token')->build();
    $sdkBadAuth->originators->list();
    check('401 on bad token throws', false, 'expected exception, got success');
} catch (Errors\ErrorResponseThrowable $e) {
    $status = $e->container->rawResponse?->getStatusCode();
    check('401 on bad token throws ErrorResponseThrowable', $status === 401, "got HTTP $status");
} catch (Errors\APIException $e) {
    check('401 on bad token throws', $e->statusCode === 401, "got HTTP {$e->statusCode}");
}

// 8b. 404 — non-existent payment ID
try {
    $sdk->payments->get(id: '00000000-0000-0000-0000-000000000000');
    check('404 on unknown paymentId throws', false, 'expected exception, got success');
} catch (Errors\ErrorResponseThrowable $e) {
    $status = $e->container->rawResponse?->getStatusCode();
    check('404 on unknown paymentId throws ErrorResponseThrowable', $status === 404, "got HTTP $status");
} catch (Errors\APIException $e) {
    check('404 on unknown paymentId throws', $e->statusCode === 404, "got HTTP {$e->statusCode}");
}

// 8c. 400 — invalid input (malformed routing number)
try {
    $sdk->contacts->create(
        new Operations\CreateContactRequest(
            bankAccount: new Operations\CreateContactBankAccount(
                achAccountNumber: '123456789',
                achRoutingNumber: 'not-a-routing-number',
                subType: Operations\CreateContactSubType::Checking,
            ),
            email: 'bad-email',
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
    check('400 on invalid input throws', false, 'expected exception, got success');
} catch (Errors\ErrorResponseThrowable $e) {
    $status = $e->container->rawResponse?->getStatusCode();
    check('400 on invalid input throws ErrorResponseThrowable', $status === 400, "got HTTP $status");
} catch (Errors\APIException $e) {
    check('400 on invalid input throws', $e->statusCode === 400, "got HTTP {$e->statusCode}");
}

// ── 9. Webhooks CRUD ──────────────────────────────────────────────────────────

section('9. Webhooks — create');

$webhookId = null;
$webhookUrl = 'https://httpbin.org/post';
$webhookLabel = 'sdk-test webhook';

try {
    $response = $sdk->webhooks->create(
        new Operations\CreateWebhookRequest(
            subscribedEvents: [
                Operations\CreateWebhookSubscribedEventRequestEnum::StatementGenerated,
                Operations\CreateWebhookSubscribedEventPaymentWildcardRequest::PaymentWildcard,
            ],
            url: $webhookUrl,
            label: $webhookLabel,
        )
    );
    check('HTTP 201', $response->statusCode === 201);
    check('Response body present', $response->object !== null);
    $webhookId = $response->object?->data?->id ?? null;
    check('webhook id returned', $webhookId !== null && $webhookId !== '');
    check('url matches', $response->object?->data?->url === $webhookUrl);
    check('label matches', $response->object?->data?->label === $webhookLabel);
    check('enabled is true by default', $response->object?->data?->enabled === true);
    check('subscribedEvents present', is_array($response->object?->data?->subscribedEvents));
    check('secret returned on creation', is_string($response->object?->data?->secret ?? null) && ($response->object?->data?->secret ?? '') !== '');
    check('createdAt present', isset($response->object?->data?->createdAt));

    $eventValues = array_map(
        fn ($e) => $e instanceof \BackedEnum ? $e->value : (string) $e,
        $response->object?->data?->subscribedEvents ?? []
    );
    check('payment.* wildcard round-trips', in_array('payment.*', $eventValues), 'got: ' . implode(', ', $eventValues));
    check('statement.generated round-trips', in_array('statement.generated', $eventValues), 'got: ' . implode(', ', $eventValues));
    echo "  → created webhookId: $webhookId\n";
} catch (Errors\ErrorResponseThrowable $e) {
    check('Create webhook succeeded', false, $e->getMessage());
} catch (Errors\APIException $e) {
    check('Create webhook succeeded', false, "HTTP " . $e->statusCode . ": " . $e->getMessage());
}

section('9b. Webhooks — get');

if ($webhookId === null) {
    echo "  Skipped — no webhookId from create step\n";
} else {
    try {
        $response = $sdk->webhooks->get(id: $webhookId);
        check('HTTP 200', $response->statusCode === 200);
        check('Response body present', $response->object !== null);
        check('id matches', $response->object?->data?->id === $webhookId);
        check('url present', isset($response->object?->data?->url));
        check('enabled present', isset($response->object?->data?->enabled));
        check('createdAt present', isset($response->object?->data?->createdAt));
        check('updatedAt present', isset($response->object?->data?->updatedAt));
    } catch (Errors\ErrorResponseThrowable $e) {
        check('Get webhook succeeded', false, $e->getMessage());
    } catch (Errors\APIException $e) {
        check('Get webhook succeeded', false, "HTTP " . $e->statusCode . ": " . $e->getMessage());
    }
}

section('9c. Webhooks — list');

try {
    $response = $sdk->webhooks->list();
    check('HTTP 200', $response->statusCode === 200);
    check('Response body present', $response->object !== null);
    check('data is array', is_array($response->object?->data));
    if ($webhookId !== null) {
        $ids = array_map(fn($w) => $w->id, $response->object?->data ?? []);
        check('created webhook appears in list', in_array($webhookId, $ids));
    }
    echo '  → ' . count($response->object?->data ?? []) . " webhook(s) found\n";
} catch (Errors\ErrorResponseThrowable $e) {
    check('List webhooks succeeded', false, $e->getMessage());
} catch (Errors\APIException $e) {
    check('List webhooks succeeded', false, "HTTP " . $e->statusCode . ": " . $e->getMessage());
}

section('9d. Webhooks — update');

if ($webhookId === null) {
    echo "  Skipped — no webhookId\n";
} else {
    try {
        $newUrl = 'https://httpbin.org/post?updated=1';
        $newLabel = 'sdk-test webhook (updated)';
        $response = $sdk->webhooks->update(
            body: new Operations\UpdateWebhookRequestBody(
                url: $newUrl,
                enabled: false,
                label: $newLabel,
                subscribedEvents: [Operations\UpdateWebhookSubscribedEventRequestEnum::PaymentCompleted],
            ),
            id: $webhookId,
        );
        check('HTTP 200', $response->statusCode === 200);
        check('Response body present', $response->object !== null);
        check('id matches', $response->object?->data?->id === $webhookId);
        check('url updated', $response->object?->data?->url === $newUrl);
        check('label updated', $response->object?->data?->label === $newLabel);
        check('enabled updated to false', $response->object?->data?->enabled === false);
    } catch (Errors\ErrorResponseThrowable $e) {
        check('Update webhook succeeded', false, $e->getMessage());
    } catch (Errors\APIException $e) {
        check('Update webhook succeeded', false, "HTTP " . $e->statusCode . ": " . $e->getMessage());
    }
}

section('9e. Webhooks — test');

if ($webhookId === null) {
    echo "  Skipped — no webhookId\n";
} else {
    try {
        $response = $sdk->webhooks->test(
            id: $webhookId,
            body: new Operations\TestWebhookRequestBody(eventType: Operations\EventType::PaymentCompleted),
        );
        check('HTTP 200', $response->statusCode === 200);
        check('Response body present', $response->object !== null);
        check('durationMs present', isset($response->object?->data?->durationMs));
        check('success field present', isset($response->object?->data?->success));
        check('statusCode is null or numeric', is_null($response->object?->data?->statusCode) || is_numeric($response->object?->data?->statusCode));
        check('error is null or string', is_null($response->object?->data?->error) || is_string($response->object?->data?->error));
        echo "  → test success: " . ($response->object?->data?->success ? 'true' : 'false') . "\n";
        echo "  → durationMs: " . ($response->object?->data?->durationMs ?? 'n/a') . "\n";
    } catch (Errors\ErrorResponseThrowable $e) {
        check('Test webhook succeeded', false, $e->getMessage());
    } catch (Errors\APIException $e) {
        check('Test webhook succeeded', false, "HTTP " . $e->statusCode . ": " . $e->getMessage());
    }
}

section('9f. Webhooks — delete');

if ($webhookId === null) {
    echo "  Skipped — no webhookId\n";
} else {
    try {
        $response = $sdk->webhooks->delete(id: $webhookId);
        check('HTTP 2xx on delete', $response->statusCode >= 200 && $response->statusCode < 300);

        // Verify it's gone
        try {
            $sdk->webhooks->get(id: $webhookId);
            check('Deleted webhook is no longer accessible', false, 'expected 404, got success');
        } catch (Errors\ErrorResponseThrowable $e) {
            $status = $e->container->rawResponse?->getStatusCode();
            check('Deleted webhook returns 404', $status === 404, "got HTTP $status");
        } catch (Errors\APIException $e) {
            check('Deleted webhook returns 404', $e->statusCode === 404, "got HTTP {$e->statusCode}");
        }
    } catch (Errors\ErrorResponseThrowable $e) {
        check('Delete webhook succeeded', false, $e->getMessage());
    } catch (Errors\APIException $e) {
        check('Delete webhook succeeded', false, "HTTP " . $e->statusCode . ": " . $e->getMessage());
    }
}

// ── 10. Statements ────────────────────────────────────────────────────────────

section('10. Statements — list');

$statementId = null;

try {
    $response = $sdk->statements->list(
        new Operations\ListStatementsRequest(
            originatorId: $originatorId,
            limit: 5,
        )
    );
    check('HTTP 200', $response->statusCode === 200);
    check('Response body present', $response->object !== null);
    check('data is array', is_array($response->object?->data));
    check('meta.pagination present', isset($response->object?->meta?->pagination));
    check('pagination.limit is numeric', is_numeric($response->object?->meta?->pagination?->limit ?? null));
    $statements = $response->object?->data ?? [];
    echo '  → ' . count($statements) . " statement(s) found\n";

    if (count($statements) > 0) {
        $stmt = $statements[0];
        check('statement id present', isset($stmt->id) && $stmt->id !== '');
        check('statement originatorId present', isset($stmt->originatorId));
        check('statement periodMonth present', isset($stmt->periodMonth));
        check('statement periodYear present', isset($stmt->periodYear));
        check('statement pdfReady present', isset($stmt->pdfReady));
        check('statement generatedAt is DateTime', $stmt->generatedAt instanceof \DateTime);
        check('statement timezone is string', is_string($stmt->timezone) && $stmt->timezone !== '');
        check('statement totalPaymentCount is int', is_int($stmt->totalPaymentCount));
        check('statement totalReturnCount is int', is_int($stmt->totalReturnCount));
        check('statement totalFeeCount is int', is_int($stmt->totalFeeCount));
        check('statement totalProcessingVolumeMinorUnits is numeric', is_numeric($stmt->totalProcessingVolumeMinorUnits));
        check('statement totalReturnVolumeMinorUnits is numeric', is_numeric($stmt->totalReturnVolumeMinorUnits));
        check('statement totalServiceFeesMinorUnits is numeric', is_numeric($stmt->totalServiceFeesMinorUnits));
        check('statement totalTransactionFeesMinorUnits is numeric', is_numeric($stmt->totalTransactionFeesMinorUnits));
        check('statement totalReturnFeesMinorUnits is numeric', is_numeric($stmt->totalReturnFeesMinorUnits));
        check('statement totalDiscountRateMinorUnits is numeric', is_numeric($stmt->totalDiscountRateMinorUnits));
        $statementId = $stmt->id;

        // find a pdfReady statement if available
        foreach ($statements as $s) {
            if ($s->pdfReady) {
                $statementId = $s->id;
                break;
            }
        }
        echo "  → using statementId: $statementId (pdfReady: " . ($stmt->pdfReady ? 'true' : 'false') . ")\n";
    }
} catch (Errors\ErrorResponseThrowable $e) {
    check('List statements succeeded', false, $e->getMessage());
} catch (Errors\APIException $e) {
    check('List statements succeeded', false, "HTTP " . $e->statusCode . ": " . $e->getMessage());
}

section('10b. Statements — get download URL');

if ($statementId === null) {
    echo "  Skipped — no statements found\n";
} else {
    try {
        $response = $sdk->statements->getDownloadUrl(id: $statementId);
        check('HTTP 200', $response->statusCode === 200);
        check('Response body present', $response->object !== null);
        check('download url present', isset($response->object?->data?->url) && $response->object?->data?->url !== '');
        check('expiresAt present', isset($response->object?->data?->expiresAt));
        echo "  → download url obtained (expires: " . ($response->object?->data?->expiresAt?->format('c') ?? 'n/a') . ")\n";
    } catch (Errors\ErrorResponseThrowable $e) {
        check('Get statement download URL succeeded', false, $e->getMessage());
    } catch (Errors\APIException $e) {
        check('Get statement download URL succeeded', false, "HTTP " . $e->statusCode . ": " . $e->getMessage());
    }
}

// ── summary ───────────────────────────────────────────────────────────────────

$total = $passed + $failed;
echo "\n────────────────────────────────────\n";
echo "Results: \033[32m$passed passed\033[0m, " . ($failed > 0 ? "\033[31m$failed failed\033[0m" : '0 failed') . " / $total total\n";

exit($failed > 0 ? 1 : 0);