<?php

declare(strict_types=1);

namespace Tests\ThreeBRS\SyliusMailChimpPlugin\Unit\Service;

use PHPUnit\Framework\Attributes\AllowMockObjectsWithoutExpectations;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Sylius\Component\Core\Context\ShopperContextInterface;
use ThreeBRS\SyliusMailChimpPlugin\Service\ChannelMailChimpSettingsProviderInterface;
use ThreeBRS\SyliusMailChimpPlugin\Service\MailChimpChannelSubscriber;
use ThreeBRS\SyliusMailChimpPlugin\Service\MailChimpManagerInterface;

#[AllowMockObjectsWithoutExpectations]
final class MailChimpChannelSubscriberTest extends TestCase
{
	private MailChimpManagerInterface&MockObject $mailChimpManager;
	private ShopperContextInterface&MockObject $shopperContext;
	private ChannelMailChimpSettingsProviderInterface&MockObject $settingsProvider;
	private MailChimpChannelSubscriber $subscriber;

	protected function setUp(): void
	{
		$this->mailChimpManager = $this->createMock(MailChimpManagerInterface::class);
		$this->shopperContext = $this->createMock(ShopperContextInterface::class);
		$this->settingsProvider = $this->createMock(ChannelMailChimpSettingsProviderInterface::class);

		$this->settingsProvider->method('isMailChimpEnabled')->willReturn(true);
		$this->settingsProvider->method('getListId')->willReturn('list-123');

		$this->subscriber = new MailChimpChannelSubscriber(
			$this->mailChimpManager,
			$this->shopperContext,
			$this->settingsProvider,
		);
	}

	#[Test]
	public function it_delegates_get_contact_to_manager(): void
	{
		// Arrange
		$expectedContact = ['email_address' => 'test@example.com', 'status' => 'subscribed'];
		$this->mailChimpManager
			->expects($this->once())
			->method('getContact')
			->with('test@example.com', 'list-123')
			->willReturn($expectedContact);

		// Act
		$contact = $this->subscriber->getContact('test@example.com');

		// Assert
		$this->assertSame($expectedContact, $contact);
	}

	#[Test]
	public function it_delegates_is_subscribed_to_manager(): void
	{
		// Arrange
		$this->mailChimpManager
			->expects($this->once())
			->method('isEmailSubscribedToList')
			->with('test@example.com', 'list-123')
			->willReturn(true);

		// Act
		$result = $this->subscriber->isSubscribed('test@example.com');

		// Assert
		$this->assertTrue($result);
	}

	#[Test]
	public function it_subscribes_with_locale_and_double_opt_in(): void
	{
		// Arrange
		$this->settingsProvider->method('isDoubleOptInEnabled')->willReturn(true);
		$this->shopperContext->method('getLocaleCode')->willReturn('en_US');

		$this->mailChimpManager
			->expects($this->once())
			->method('subscribeToList')
			->with('test@example.com', 'list-123', 'en_US', true, []);

		// Act
		$this->subscriber->subscribe('test@example.com');
	}

	#[Test]
	public function it_subscribes_with_additional_data(): void
	{
		// Arrange
		$this->settingsProvider->method('isDoubleOptInEnabled')->willReturn(false);
		$this->shopperContext->method('getLocaleCode')->willReturn('cs_CZ');

		$mergeData = ['FNAME' => 'John', 'LNAME' => 'Doe'];
		$this->mailChimpManager
			->expects($this->once())
			->method('subscribeToList')
			->with('test@example.com', 'list-123', 'cs_CZ', false, $mergeData);

		// Act
		$this->subscriber->subscribe('test@example.com', $mergeData);
	}

	#[Test]
	public function it_delegates_unsubscribe_to_manager(): void
	{
		// Arrange
		$this->mailChimpManager
			->expects($this->once())
			->method('unsubscribeFromList')
			->with('test@example.com', 'list-123');

		// Act
		$this->subscriber->unsubscribe('test@example.com');
	}
}
