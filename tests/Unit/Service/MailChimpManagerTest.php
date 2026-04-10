<?php

declare(strict_types=1);

namespace Tests\ThreeBRS\SyliusMailChimpPlugin\Unit\Service;

use DrewM\MailChimp\MailChimp;
use PHPUnit\Framework\Attributes\AllowMockObjectsWithoutExpectations;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Response;
use ThreeBRS\SyliusMailChimpPlugin\Exception\MailChimpException;
use ThreeBRS\SyliusMailChimpPlugin\Exception\MailChimpInvalidErrorResponseException;
use ThreeBRS\SyliusMailChimpPlugin\Model\MailChimpSubscriptionStatusEnum;
use ThreeBRS\SyliusMailChimpPlugin\Service\MailChimpApiClientProvider;
use ThreeBRS\SyliusMailChimpPlugin\Service\MailChimpManager;

#[AllowMockObjectsWithoutExpectations]
final class MailChimpManagerTest extends TestCase
{
	private MailChimp&MockObject $mailChimpClient;

	private function createManager(MailChimp|null|false $client = false): MailChimpManager
	{
		$clientProvider = $this->createMock(MailChimpApiClientProvider::class);
		$clientProvider->method('getClient')->willReturn($client === false ? $this->mailChimpClient : $client);

		return new MailChimpManager($clientProvider);
	}

	protected function setUp(): void
	{
		$this->mailChimpClient = $this->createMock(MailChimp::class);
	}

	#[Test]
	public function it_returns_contact_on_success(): void
	{
		// Arrange
		$expectedResult = ['email_address' => 'test@example.com', 'status' => 'subscribed'];
		$this->mailChimpClient->method('get')->willReturn($expectedResult);
		$this->mailChimpClient->method('success')->willReturn(true);

		$manager = $this->createManager();

		// Act
		$contact = $manager->getContact('test@example.com', 'list-123');

		// Assert
		$this->assertSame($expectedResult, $contact);
	}

	#[Test]
	public function it_returns_null_when_contact_not_found(): void
	{
		// Arrange
		$this->mailChimpClient->method('get')->willReturn(false);
		$this->mailChimpClient->method('success')->willReturn(false);
		$this->mailChimpClient->method('getLastResponse')->willReturn([
			'headers' => ['http_code' => Response::HTTP_NOT_FOUND],
			'body' => '',
		]);

		$manager = $this->createManager();

		// Act
		$contact = $manager->getContact('test@example.com', 'list-123');

		// Assert
		$this->assertNull($contact);
	}

	#[Test]
	public function it_throws_exception_on_api_error(): void
	{
		// Arrange
		$this->mailChimpClient->method('get')->willReturn(false);
		$this->mailChimpClient->method('success')->willReturn(false);
		$this->mailChimpClient->method('getLastResponse')->willReturn([
			'headers' => ['http_code' => Response::HTTP_INTERNAL_SERVER_ERROR],
			'body' => json_encode([
				'status' => 500,
				'detail' => 'Server error',
				'type' => 'https://mailchimp.com/error',
				'title' => 'Internal Server Error',
				'instance' => 'abc-123',
			]),
		]);

		$manager = $this->createManager();

		// Assert & Act
		$this->expectException(MailChimpException::class);
		$manager->getContact('test@example.com', 'list-123');
	}

	#[Test]
	public function it_throws_invalid_error_response_when_body_is_null(): void
	{
		// Arrange
		$this->mailChimpClient->method('get')->willReturn(false);
		$this->mailChimpClient->method('success')->willReturn(false);
		$this->mailChimpClient->method('getLastResponse')->willReturn([
			'headers' => ['http_code' => Response::HTTP_INTERNAL_SERVER_ERROR],
			'body' => null,
		]);

		$manager = $this->createManager();

		// Assert & Act
		$this->expectException(MailChimpInvalidErrorResponseException::class);
		$manager->getContact('test@example.com', 'list-123');
	}

	#[Test]
	public function it_detects_subscribed_email(): void
	{
		// Arrange
		$this->mailChimpClient->method('get')->willReturn([
			'email_address' => 'test@example.com',
			'status' => MailChimpSubscriptionStatusEnum::SUBSCRIBED,
		]);
		$this->mailChimpClient->method('success')->willReturn(true);

		$manager = $this->createManager();

		// Act & Assert
		$this->assertTrue($manager->isEmailSubscribedToList('test@example.com', 'list-123'));
	}

	#[Test]
	public function it_detects_unsubscribed_email(): void
	{
		// Arrange
		$this->mailChimpClient->method('get')->willReturn([
			'email_address' => 'test@example.com',
			'status' => MailChimpSubscriptionStatusEnum::UNSUBSCRIBED,
		]);
		$this->mailChimpClient->method('success')->willReturn(true);

		$manager = $this->createManager();

		// Act & Assert
		$this->assertFalse($manager->isEmailSubscribedToList('test@example.com', 'list-123'));
	}

	#[Test]
	public function it_returns_false_for_nonexistent_email(): void
	{
		// Arrange
		$this->mailChimpClient->method('get')->willReturn(false);
		$this->mailChimpClient->method('success')->willReturn(false);
		$this->mailChimpClient->method('getLastResponse')->willReturn([
			'headers' => ['http_code' => Response::HTTP_NOT_FOUND],
			'body' => '',
		]);

		$manager = $this->createManager();

		// Act & Assert
		$this->assertFalse($manager->isEmailSubscribedToList('test@example.com', 'list-123'));
	}

	#[Test]
	public function it_uses_put_for_new_subscriber(): void
	{
		// Arrange: getContact returns 404 (not found), so isEmailSubscribedToList returns false
		$this->mailChimpClient->method('get')->willReturn(false);
		$this->mailChimpClient->method('success')->willReturnOnConsecutiveCalls(
			false, // getContact in isEmailSubscribedToList fails (not found)
			true,  // put call succeeds
		);
		$this->mailChimpClient->method('getLastResponse')->willReturn([
			'headers' => ['http_code' => Response::HTTP_NOT_FOUND],
			'body' => '',
		]);

		$expectedResult = ['status' => 'subscribed'];
		$this->mailChimpClient
			->expects($this->once())
			->method('put')
			->willReturn($expectedResult);
		$this->mailChimpClient
			->expects($this->never())
			->method('patch');

		$manager = $this->createManager();

		// Act
		$result = $manager->subscribeToList('test@example.com', 'list-123', 'en_US', false);

		// Assert
		$this->assertSame($expectedResult, $result);
	}

	#[Test]
	public function it_uses_patch_for_existing_subscriber(): void
	{
		// Arrange: getContact returns subscribed contact
		$this->mailChimpClient->method('get')->willReturn([
			'email_address' => 'test@example.com',
			'status' => MailChimpSubscriptionStatusEnum::SUBSCRIBED,
		]);
		$this->mailChimpClient->method('success')->willReturnOnConsecutiveCalls(
			true, // getContact in isEmailSubscribedToList succeeds (subscribed)
			true, // patch succeeds
		);

		$expectedResult = ['status' => 'subscribed'];
		$this->mailChimpClient
			->expects($this->once())
			->method('patch')
			->willReturn($expectedResult);
		$this->mailChimpClient
			->expects($this->never())
			->method('put');

		$manager = $this->createManager();

		// Act
		$result = $manager->subscribeToList('test@example.com', 'list-123', 'en_US', false);

		// Assert
		$this->assertSame($expectedResult, $result);
	}

	#[Test]
	public function it_sets_pending_status_when_double_opt_in_enabled(): void
	{
		// Arrange
		$this->mailChimpClient->method('get')->willReturn(false);
		$this->mailChimpClient->method('success')->willReturnOnConsecutiveCalls(false, true);
		$this->mailChimpClient->method('getLastResponse')->willReturn([
			'headers' => ['http_code' => Response::HTTP_NOT_FOUND],
			'body' => '',
		]);

		$this->mailChimpClient
			->expects($this->once())
			->method('put')
			->with(
				$this->anything(),
				$this->callback(function (array $options): bool {
					return $options['status'] === MailChimpSubscriptionStatusEnum::PENDING;
				}),
			)
			->willReturn(['status' => 'pending']);

		$manager = $this->createManager();

		// Act
		$manager->subscribeToList('test@example.com', 'list-123', 'en_US', true);
	}

	#[Test]
	public function it_truncates_locale_code_to_two_characters(): void
	{
		// Arrange
		$this->mailChimpClient->method('get')->willReturn(false);
		$this->mailChimpClient->method('success')->willReturnOnConsecutiveCalls(false, true);
		$this->mailChimpClient->method('getLastResponse')->willReturn([
			'headers' => ['http_code' => Response::HTTP_NOT_FOUND],
			'body' => '',
		]);

		$this->mailChimpClient
			->expects($this->once())
			->method('put')
			->with(
				$this->anything(),
				$this->callback(function (array $options): bool {
					return $options['language'] === 'cs';
				}),
			)
			->willReturn(['status' => 'subscribed']);

		$manager = $this->createManager();

		// Act
		$manager->subscribeToList('test@example.com', 'list-123', 'cs_CZ', false);
	}

	#[Test]
	public function it_includes_merge_fields_when_data_provided(): void
	{
		// Arrange
		$this->mailChimpClient->method('get')->willReturn(false);
		$this->mailChimpClient->method('success')->willReturnOnConsecutiveCalls(false, true);
		$this->mailChimpClient->method('getLastResponse')->willReturn([
			'headers' => ['http_code' => Response::HTTP_NOT_FOUND],
			'body' => '',
		]);

		$this->mailChimpClient
			->expects($this->once())
			->method('put')
			->with(
				$this->anything(),
				$this->callback(function (array $options): bool {
					return isset($options['merge_fields'])
						&& $options['merge_fields']['FNAME'] === 'John';
				}),
			)
			->willReturn(['status' => 'subscribed']);

		$manager = $this->createManager();

		// Act
		$manager->subscribeToList('test@example.com', 'list-123', 'en_US', false, ['FNAME' => 'John']);
	}

	#[Test]
	public function it_unsubscribes_email(): void
	{
		// Arrange
		$this->mailChimpClient
			->expects($this->once())
			->method('patch')
			->with(
				$this->anything(),
				$this->callback(function (array $options): bool {
					return $options['status'] === MailChimpSubscriptionStatusEnum::UNSUBSCRIBED;
				}),
			)
			->willReturn(['status' => 'unsubscribed']);
		$this->mailChimpClient->method('success')->willReturn(true);

		$manager = $this->createManager();

		// Act
		$result = $manager->unsubscribeFromList('test@example.com', 'list-123');

		// Assert
		$this->assertSame(['status' => 'unsubscribed'], $result);
	}

	#[Test]
	public function it_returns_empty_lists_when_client_is_null(): void
	{
		// Arrange
		$manager = $this->createManager(client: null);

		// Act
		$lists = $manager->getLists();

		// Assert
		$this->assertSame([], $lists);
	}

	#[Test]
	public function it_returns_sorted_lists(): void
	{
		// Arrange
		$this->mailChimpClient->method('get')->willReturn([
			'lists' => [
				['id' => 'id-b', 'name' => 'Beta List'],
				['id' => 'id-a', 'name' => 'Alpha List'],
			],
			'total_items' => 2,
		]);
		$this->mailChimpClient->method('success')->willReturn(true);

		$manager = $this->createManager();

		// Act
		$lists = $manager->getLists();

		// Assert
		$this->assertSame([
			'id-a' => 'Alpha List',
			'id-b' => 'Beta List',
		], $lists);
	}

	#[Test]
	public function it_paginates_through_lists(): void
	{
		// Arrange: 12 items total, 10 per page = 2 pages
		$this->mailChimpClient->method('get')->willReturnOnConsecutiveCalls(
			[
				'lists' => array_map(
					fn (int $index) => ['id' => "id-$index", 'name' => "List $index"],
					range(1, 10),
				),
				'total_items' => 12,
			],
			[
				'lists' => [
					['id' => 'id-11', 'name' => 'List 11'],
					['id' => 'id-12', 'name' => 'List 12'],
				],
				'total_items' => 12,
			],
		);
		$this->mailChimpClient->method('success')->willReturn(true);

		$manager = $this->createManager();

		// Act
		$lists = $manager->getLists();

		// Assert
		$this->assertCount(12, $lists);
		$this->assertSame('List 1', $lists['id-1']);
		$this->assertSame('List 12', $lists['id-12']);
	}
}
