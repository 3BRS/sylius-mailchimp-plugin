<?php

declare(strict_types=1);

namespace ThreeBRS\SyliusMailChimpPlugin\Service;

use Sylius\Component\Channel\Context\ChannelContextInterface;
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
            return $channel->getMailChimpListId();
        }

        return null;
    }

    public function isDoubleOptInEnabled(): bool
    {
        $channel = $this->channelContext->getChannel();
        if ($channel instanceof ChannelMailChimpSettingsInterface) {
            return $channel->isMailChimpListDoubleOptInEnabled();
        }

        return false;
    }

    public function isMailChimpEnabled(): bool
    {
        $channel = $this->channelContext->getChannel();
        if ($channel instanceof ChannelMailChimpSettingsInterface) {
            return $channel->isMailChimpEnabled();
        }

        return false;
    }
}
