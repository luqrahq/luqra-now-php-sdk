<?php

declare(strict_types=1);

// Run against this checkout, including generated changes, without contacting any API.
// composer install --no-dev && php tests/verify-error-resource-compatibility.php
require __DIR__ . '/../vendor/autoload.php';

use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;
use Luqra\LuqraNowPhp\Contacts;
use Luqra\LuqraNowPhp\Models\Components\Error as SdkError;
use Luqra\LuqraNowPhp\Models\Errors\ErrorResponse;
use Luqra\LuqraNowPhp\Models\Errors\ErrorResponseThrowable;
use Luqra\LuqraNowPhp\Models\Operations\UpdateContactRequestBody;
use Luqra\LuqraNowPhp\SDKConfiguration;

function check(bool $condition, string $message): void
{
    if (!$condition) {
        throw new RuntimeException($message);
    }
}

// Preserve existing caller source compatibility, including optional positional arguments.
$legacy = new ErrorResponse(new SdkError('CONFLICT', 'expected', 'trace', []), null);
check($legacy->error->correlationId === 'trace' && $legacy->error->details === [], 'Legacy constructor changed');
$named = new ErrorResponse(error: new SdkError(code: 'CONFLICT', message: 'expected', correlationId: 'trace', details: []), rawResponse: null);
check($named->error->correlationId === 'trace' && $named->error->details === [], 'Named constructor arguments changed');

$cases = 0;
foreach ([404 => 'NOT_FOUND', 409 => 'CONFLICT'] as $status => $code) {
    foreach (['old', 'references', 'future-reference-strings'] as $variant) {
        $error = ['code' => $code, 'message' => 'expected error', 'correlationId' => 'trace', 'details' => []];
        if ($variant !== 'old') {
            $error['resources'] = [[
                'type' => $variant === 'references' ? 'contact' : 'future_resource',
                'id' => 'opaque-resource-id',
                'relation' => $variant === 'references' ? 'conflicting' : 'future_relation',
            ]];
            $error['resourceCount'] = 12;
        }
        $body = json_encode(['error' => $error], JSON_THROW_ON_ERROR);
        $config = new SDKConfiguration();
        $config->serverUrl = 'https://sdk-compatibility.invalid';
        $config->client = new Client(['handler' => HandlerStack::create(new MockHandler([
            new Response($status, ['Content-Type' => 'application/json'], $body),
        ]))]);

        try {
            (new Contacts($config))->update(new UpdateContactRequestBody(firstName: 'Test'), '00000000-0000-4000-8000-000000000001');
            throw new RuntimeException('Expected the existing typed SDK exception');
        } catch (ErrorResponseThrowable $exception) {
            $container = $exception->container;
            check($container->error->code === $code, 'Error code changed');
            check($container->error->message === 'expected error', 'Message changed');
            check($container->error->correlationId === 'trace', 'Correlation ID changed');
            check($container->error->details === [], 'Details changed');
            check($container->rawResponse?->getStatusCode() === $status, 'HTTP status lost');
            check((string) $container->rawResponse->getBody() === $body, 'Raw response changed');
            $cases++;
        }
    }
}
echo "SDK {$config->sdkVersion}: {$cases} mocked HTTP error cases and legacy constructors passed\n";
