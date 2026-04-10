<?php

declare(strict_types=1);

namespace Tests\ThreeBRS\SyliusMailChimpPlugin\Unit\Service;

use PHPUnit\Framework\Attributes\AllowMockObjectsWithoutExpectations;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Sylius\Component\Channel\Context\ChannelContextInterface;
use Sylius\Component\Channel\Model\ChannelInterface;
use ThreeBRS\SyliusMailChimpPlugin\Entity\ChannelMailChimpSettingsInterface;
use ThreeBRS\SyliusMailChimpPlugin\Service\ChannelMailChimpSettingsProvider;

#[AllowMockObjectsWithoutExpectations]
final class ChannelMailChimpSettingsProviderTest extends TestCase
{
	#[Test]
	public function it_returns_list_id_when_channel_implements_settings_interface(): void
	{
		// Arrange
		$channel = $this->createMock(ChannelWithMailChimpSettings::class);
		$channel->method('getMailChimpListId')->willReturn('list-abc-123');

		$channelContext = $this->createMock(ChannelContextInterface::class);
		$channelContext->method('getChannel')->willReturn($channel);

		$provider = new ChannelMailChimpSettingsProvider($channelContext);

		// Act
		$listId = $provider->getListId();

		// Assert
		$this->assertSame('list-abc-123', $listId);
	}

	#[Test]
	public function it_returns_null_list_id_when_channel_does_not_implement_settings_interface(): void
	{
		// Arrange
		$channel = $this->createMock(ChannelInterface::class);

		$channelContext = $this->createMock(ChannelContextInterface::class);
		$channelContext->method('getChannel')->willReturn($channel);

		$provider = new ChannelMailChimpSettingsProvider($channelContext);

		// Act
		$listId = $provider->getListId();

		// Assert
		$this->assertNull($listId);
	}

	#[Test]
	public function it_returns_enabled_when_channel_has_mailchimp_enabled(): void
	{
		// Arrange
		$channel = $this->createMock(ChannelWithMailChimpSettings::class);
		$channel->method('isMailChimpEnabled')->willReturn(true);

		$channelContext = $this->createMock(ChannelContextInterface::class);
		$channelContext->method('getChannel')->willReturn($channel);

		$provider = new ChannelMailChimpSettingsProvider($channelContext);

		// Act & Assert
		$this->assertTrue($provider->isMailChimpEnabled());
	}

	#[Test]
	public function it_returns_disabled_when_channel_does_not_implement_settings_interface(): void
	{
		// Arrange
		$channel = $this->createMock(ChannelInterface::class);

		$channelContext = $this->createMock(ChannelContextInterface::class);
		$channelContext->method('getChannel')->willReturn($channel);

		$provider = new ChannelMailChimpSettingsProvider($channelContext);

		// Act & Assert
		$this->assertFalse($provider->isMailChimpEnabled());
	}

	#[Test]
	public function it_returns_double_opt_in_setting_from_channel(): void
	{
		// Arrange
		$channel = $this->createMock(ChannelWithMailChimpSettings::class);
		$channel->method('isMailChimpListDoubleOptInEnabled')->willReturn(true);

		$channelContext = $this->createMock(ChannelContextInterface::class);
		$channelContext->method('getChannel')->willReturn($channel);

		$provider = new ChannelMailChimpSettingsProvider($channelContext);

		// Act & Assert
		$this->assertTrue($provider->isDoubleOptInEnabled());
	}

	#[Test]
	public function it_returns_false_double_opt_in_when_channel_does_not_implement_settings_interface(): void
	{
		// Arrange
		$channel = $this->createMock(ChannelInterface::class);

		$channelContext = $this->createMock(ChannelContextInterface::class);
		$channelContext->method('getChannel')->willReturn($channel);

		$provider = new ChannelMailChimpSettingsProvider($channelContext);

		// Act & Assert
		$this->assertFalse($provider->isDoubleOptInEnabled());
	}
}

/**
 * Helper interface combining ChannelInterface with MailChimp settings for mocking.
 */
interface ChannelWithMailChimpSettings extends ChannelInterface, ChannelMailChimpSettingsInterface
{
}
