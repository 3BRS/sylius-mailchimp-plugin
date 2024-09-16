<?php

declare(strict_types=1);

namespace ThreeBRS\SyliusMailChimpPlugin\Service;

use Psr\Log\LoggerInterface;
use Sylius\Component\Channel\Context\ChannelContextInterface;
use Sylius\Component\Channel\Context\ChannelNotFoundException;
use ThreeBRS\SyliusMailChimpPlugin\Entity\ChannelMailChimpSettingsInterface;

class ChannelMailChimpSettingsProvider implements ChannelMailChimpSettingsProviderInterface
{
    public function __construct(
        private readonly ChannelContextInterface $channelContext,
    ) {

    }

    public function getListId(): ?string
    {
        $channel = $this->channelContext->getChannel();
        if ($channel instanceof ChannelMailChimpSettingsInterface) {
            return $channel->getMailchimpListId();
        }

        return null;
    }

    public function isDoubleOptInEnabled(): bool
    {
        $channel = $this->channelContext->getChannel();
        if ($channel instanceof ChannelMailChimpSettingsInterface) {
            return $channel->isMailchimpListDoubleOptInEnabled();
        }

        return false;
    }

    public function isMailchimpEnabled(): bool
    {
        $channel = $this->channelContext->getChannel();
        if ($channel instanceof ChannelMailChimpSettingsInterface) {
            return $channel->isMailchimpEnabled();
        }

        return false;
    }
}
