<?php

declare(strict_types=1);

namespace Tests\ThreeBRS\SyliusMailChimpPlugin\Behat\Context\Setup;

use Behat\Behat\Context\Context;
use Behat\Gherkin\Node\TableNode;
use Doctrine\ORM\EntityManagerInterface;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Channel\Repository\ChannelRepositoryInterface;
use ThreeBRS\SyliusMailChimpPlugin\Entity\ChannelMailChimpSettingsInterface;

final class ChannelMailchimpContext implements Context
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private ChannelRepositoryInterface $channelRepository,
    ) {}

    /**
     * @Given channel :channelName has Mailchimp enabled with list :listId and double opt-in :doubleOptIn
     */
    public function preloadMailchimpSettings(string $channelName, string $listId, string $doubleOptIn): void
    {
        /** @var ChannelInterface|null $channel */
        $channel = $this->channelRepository->findOneBy(['name' => $channelName]);

        if (!$channel instanceof ChannelMailChimpSettingsInterface) {
            throw new \LogicException('Channel does not implement Mailchimp settings interface.');
        }

        $channel->setIsMailChimpEnabled(true);
        $channel->setMailChimpListId($listId);
        $channel->setIsMailChimpListDoubleOptInEnabled(filter_var($doubleOptIn, FILTER_VALIDATE_BOOLEAN));

        $this->entityManager->flush();
    }

    /**
     * @Given Mailchimp is enabled for the channel :channelName
     */
    public function enableMailchimpForChannel(string $channelName): void
    {
        $channel = $this->channelRepository->findOneBy(['name' => $channelName]);
        if (!$channel instanceof ChannelMailChimpSettingsInterface) {
            throw new \LogicException('Channel does not implement Mailchimp settings interface.');
        }
        $channel->setIsMailChimpEnabled(true);
        $this->entityManager->flush();
    }

    /**
     * @Then the Mailchimp settings for channel :channelName should be:
     */
    public function mailchimpSettingsShouldBe(string $channelName, TableNode $table): void
    {
        /** @var ChannelInterface|null $channel */
        $channel = $this->channelRepository->findOneBy(['name' => $channelName]);

        if (!$channel instanceof ChannelMailChimpSettingsInterface) {
            throw new \LogicException('Channel does not implement Mailchimp settings interface.');
        }

        $this->entityManager->refresh($channel);

        $expected = $table->getRowsHash();

        if ((bool) $expected['isMailChimpEnabled'] !== $channel->isMailChimpEnabled()) {
            throw new \RuntimeException('Mailchimp enabled does not match.');
        }

        if ((bool) $expected['isMailChimpListDoubleOptInEnabled'] !== $channel->isMailChimpListDoubleOptInEnabled()) {
            throw new \RuntimeException('Double opt-in does not match.');
        }

        if ($expected['mailChimpListId'] !== $channel->getMailChimpListId()) {
            throw new \RuntimeException('List ID does not match.');
        }
    }
}
