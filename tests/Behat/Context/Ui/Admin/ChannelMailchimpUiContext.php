<?php

declare(strict_types=1);

namespace Tests\ThreeBRS\SyliusMailChimpPlugin\Behat\Context\Ui\Admin;

use Behat\Behat\Context\Context;
use Tests\ThreeBRS\SyliusMailChimpPlugin\Behat\Page\Admin\Channel\UpdatePageInterface;
use Sylius\Component\Channel\Repository\ChannelRepositoryInterface;
use Webmozart\Assert\Assert;

final class ChannelMailchimpUiContext implements Context
{
    public function __construct(
        private UpdatePageInterface $updatePage,
        private ChannelRepositoryInterface $channelRepository,


    ) {}

    /**
     * @When I go to the :channelName channel edit page
     */
    public function iGoToChannelEditPage(string $channelName): void
    {
        $code = strtolower(str_replace(' ', '_', $channelName));

        /** @var ChannelInterface|null $channel */
        $channel = $this->channelRepository->findOneBy(['code' => $code]);

        Assert::notNull($channel, sprintf('Channel with code "%s" was not found.', $code));

        $this->updatePage->open(['id' => $channel->getId()]);
    }

    /**
     * @When I enable Mailchimp for the channel
     */
    public function iEnableMailchimp(): void
    {
        $this->updatePage->enableMailchimp();
    }

    /**
     * @When I enable double opt-in for the channel
     */
    public function iEnableDoubleOptIn(): void
    {
        $this->updatePage->enableDoubleOptIn();
    }

    /**
     * @When I select :listId as the Mailchimp list for the channel
     */
    public function iSelectMailchimpList(string $listId): void
    {
        $this->updatePage->specifyMailchimpListId($listId);
    }

    /**
     * @When I save the channel
     */
    public function iSaveTheChannel(): void
    {
        $this->updatePage->saveChanges();
    }
}
