<?php

declare(strict_types=1);

namespace ThreeBRS\SyliusMailChimpPlugin\Service;

use Sylius\Component\Core\Context\ShopperContextInterface;

class MailChimpChannelSubscriber implements MailChimpChannelSubscriberInterface
{
    public function __construct(
        private readonly MailChimpManagerInterface $mailChimpManager,
        private readonly ShopperContextInterface $shopperContext,
        private readonly ChannelMailChimpSettingsProviderInterface $channelMailChimpSettingsProvider,
    ) {
    }

    public function getContact(string $email): ?array
    {
        $listId = $this->channelMailChimpSettingsProvider->getListId();
        $isMailChimpEnabled = $this->channelMailChimpSettingsProvider->isMailChimpEnabled();

        assert($isMailChimpEnabled && $listId !== null);

        return $this->mailChimpManager->getContact($email, $listId);
    }

    public function isSubscribed(string $email): bool
    {
        $listId = $this->channelMailChimpSettingsProvider->getListId();
        $isMailChimpEnabled = $this->channelMailChimpSettingsProvider->isMailChimpEnabled();

        assert($isMailChimpEnabled && $listId !== null);

        return $this->mailChimpManager->isEmailSubscribedToList($email, $listId);
    }

    public function subscribe(string $email, array $data = []): void
    {
        $listId = $this->channelMailChimpSettingsProvider->getListId();
        $isDoubleOptInEnabled = $this->channelMailChimpSettingsProvider->isDoubleOptInEnabled();
        $isMailChimpEnabled = $this->channelMailChimpSettingsProvider->isMailChimpEnabled();

        assert($isMailChimpEnabled && $listId !== null);
        $this->mailChimpManager->subscribeToList($email, $listId, $this->shopperContext->getLocaleCode(), $isDoubleOptInEnabled, $data);
    }

    public function unsubscribe(string $email): void
    {
        $listId = $this->channelMailChimpSettingsProvider->getListId();
        $isMailChimpEnabled = $this->channelMailChimpSettingsProvider->isMailChimpEnabled();

        assert($isMailChimpEnabled && $listId !== null);
        $this->mailChimpManager->unsubscribeFromList($email, $listId);
    }
}
