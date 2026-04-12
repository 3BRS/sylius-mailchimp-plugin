<?php

declare(strict_types=1);

namespace Tests\ThreeBRS\SyliusMailChimpPlugin\Unit\Model;

use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use ThreeBRS\SyliusMailChimpPlugin\Model\MailChimpSubscriptionStatusEnum;

/**
 * Fetches the official Mailchimp API v3 JSON schema and validates
 * that our code constants and API usage match the upstream contract.
 *
 * Falls back to local schema copies if the remote is unreachable (with a PHPUnit warning).
 */
final class MailChimpApiSchemaComplianceTest extends TestCase
{
    private const MEMBER_SCHEMA_URL = 'https://api.mailchimp.com/schema/3.0/Lists/Members/Instance.json';
    private const LIST_SCHEMA_URL = 'https://api.mailchimp.com/schema/3.0/Lists/Instance.json';

    private const LOCAL_MEMBER_SCHEMA = __DIR__ . '/schemas/ListsMembersInstance.json';
    private const LOCAL_LIST_SCHEMA = __DIR__ . '/schemas/ListsInstance.json';

    private static ?array $memberSchema = null;
    private static ?array $listSchema = null;
    private static bool $usingOfflineMemberSchema = false;
    private static bool $usingOfflineListSchema = false;

    protected function setUp(): void
    {
        if (self::$memberSchema === null) {
            self::$memberSchema = $this->fetchSchemaWithFallback(
                self::MEMBER_SCHEMA_URL,
                self::LOCAL_MEMBER_SCHEMA,
                self::$usingOfflineMemberSchema,
            );
        }
        if (self::$listSchema === null) {
            self::$listSchema = $this->fetchSchemaWithFallback(
                self::LIST_SCHEMA_URL,
                self::LOCAL_LIST_SCHEMA,
                self::$usingOfflineListSchema,
            );
        }
    }

    protected function tearDown(): void
    {
        if (self::$usingOfflineMemberSchema || self::$usingOfflineListSchema) {
            // PHPUnit 12 converts E_USER_WARNING into a test warning
            trigger_error(
                'Using offline Mailchimp API schema — could not fetch live schema from api.mailchimp.com. Validation is against a local copy that may be outdated.',
                \E_USER_WARNING,
            );
        }
    }

    #[Test]
    public function ourStatusConstantsAreSubsetOfApiStatusEnum(): void
    {
        $apiStatuses = self::$memberSchema['properties']['status']['enum'] ?? null;
        self::assertNotNull($apiStatuses, 'Could not find status enum in Mailchimp member schema');

        $ourStatuses = [
            MailChimpSubscriptionStatusEnum::SUBSCRIBED,
            MailChimpSubscriptionStatusEnum::UNSUBSCRIBED,
            MailChimpSubscriptionStatusEnum::PENDING,
            MailChimpSubscriptionStatusEnum::CLEANED,
        ];

        foreach ($ourStatuses as $status) {
            self::assertContains(
                $status,
                $apiStatuses,
                sprintf('Our status "%s" is not in the Mailchimp API enum: [%s]', $status, implode(', ', $apiStatuses)),
            );
        }
    }

    #[Test]
    public function apiStillHasSubscribedAndPendingStatuses(): void
    {
        $apiStatuses = self::$memberSchema['properties']['status']['enum'] ?? [];

        self::assertContains('subscribed', $apiStatuses, 'Mailchimp API no longer has "subscribed" status');
        self::assertContains('pending', $apiStatuses, 'Mailchimp API no longer has "pending" status — double opt-in may be broken');
        self::assertContains('unsubscribed', $apiStatuses, 'Mailchimp API no longer has "unsubscribed" status');
    }

    #[Test]
    public function memberSchemaHasExpectedFieldsUsedByPlugin(): void
    {
        $properties = self::$memberSchema['properties'] ?? [];

        $fieldsUsedByPlugin = [
            'email_address',
            'status',
            'language',
            'merge_fields',
        ];

        foreach ($fieldsUsedByPlugin as $field) {
            self::assertArrayHasKey(
                $field,
                $properties,
                sprintf('Mailchimp member schema no longer has "%s" property — plugin sends this field in API calls', $field),
            );
        }
    }

    #[Test]
    public function statusIfNewFieldStillExistsForPutUpsert(): void
    {
        $properties = self::$memberSchema['properties'] ?? [];

        self::assertArrayHasKey(
            'status_if_new',
            $properties,
            'Mailchimp API removed "status_if_new" — PUT upsert behavior may have changed',
        );

        $statusIfNewEnums = $properties['status_if_new']['enum'] ?? [];
        self::assertContains('subscribed', $statusIfNewEnums);
        self::assertContains('pending', $statusIfNewEnums);
    }

    #[Test]
    public function listSchemaHasIdAndNameForGetLists(): void
    {
        $properties = self::$listSchema['properties'] ?? [];

        self::assertArrayHasKey('id', $properties, 'Mailchimp list schema no longer has "id" property');
        self::assertArrayHasKey('name', $properties, 'Mailchimp list schema no longer has "name" property');
    }

    #[Test]
    public function languageFieldIsStillStringType(): void
    {
        $languageProperty = self::$memberSchema['properties']['language'] ?? null;
        self::assertNotNull($languageProperty, 'Mailchimp member schema no longer has "language" property');
        self::assertSame('string', $languageProperty['type'], 'Language field is no longer a string — our substr($locale, 0, 2) may need updating');
    }

    /**
     * @return array<string, mixed>
     */
    private function fetchSchemaWithFallback(string $url, string $localPath, bool &$usingOffline): array
    {
        $context = stream_context_create([
            'http' => [
                'timeout' => 10,
                'ignore_errors' => false,
            ],
        ]);

        $body = @file_get_contents($url, false, $context);
        if ($body !== false) {
            $decoded = json_decode($body, true, 512, \JSON_THROW_ON_ERROR);
            self::assertIsArray($decoded);
            $usingOffline = false;

            return $decoded;
        }

        // Fallback to local copy
        self::assertFileExists($localPath, sprintf('Cannot reach %s and local fallback %s does not exist', $url, $localPath));

        $localBody = file_get_contents($localPath);
        self::assertNotFalse($localBody);

        $decoded = json_decode($localBody, true, 512, \JSON_THROW_ON_ERROR);
        self::assertIsArray($decoded);
        $usingOffline = true;

        return $decoded;
    }
}
