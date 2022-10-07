<?php

declare(strict_types=1);

namespace ThreeBRS\SyliusMailChimpPlugin\Service;

use Psr\Log\LoggerInterface;
use Sylius\Component\Channel\Context\ChannelContextInterface;
use Sylius\Component\Channel\Context\ChannelNotFoundException;
use ThreeBRS\SyliusMailChimpPlugin\Entity\ChannelMailChimpSettingsInterface;

class ChannelMailChimpSettingsProvider implements ChannelMailChimpSettingsProviderInterface
{
    /** @var ChannelMailChimpSettingsInterface|null */
    private $channel;

    public function __construct(
        ChannelContextInterface $channelContext,
        LoggerInterface $logger
    ) {
        try {
            $channel = $channelContext->getChannel();
            assert($channel instanceof ChannelMailChimpSettingsInterface);
            $this->channel = $channel;
        } catch (ChannelNotFoundException $e) {
            $logger->warning('ChannelMailchimpSettingsProvider did not get channel', ['exception' => $e]);
        }
    }

    public function getListId(): ?string
    {
        if ($this->channel) {
            return $this->channel->getMailchimpListId();
        }

        return null;
    }

    public function isDoubleOptInEnabled(): bool
    {
        if ($this->channel) {
            return $this->channel->isMailchimpListDoubleOptInEnabled();
        }

        return false;
    }

    public function isMailchimpEnabled(): bool
    {
        if ($this->channel) {
            return $this->channel->isMailchimpEnabled();
        }

        return false;
    }
}
