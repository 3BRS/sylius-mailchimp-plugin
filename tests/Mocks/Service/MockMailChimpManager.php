<?php

declare(strict_types=1);

namespace Mocks\ThreeBRS\SyliusMailChimpPlugin\Service;

use ThreeBRS\SyliusMailChimpPlugin\Service\MailChimpManagerInterface;

final class MockMailChimpManager implements MailChimpManagerInterface
{
    private array $calledEmails = [];
    private array $subscribedEmails = [];

    public function getContact(string $email, string $listId): ?array
    {
        return ['email_address' => $email, 'status' => 'subscribed'];
    }

    public function isEmailSubscribedToList(string $email, string $listId): bool
    {
        $this->calledEmails[] = $email;
        return true;
    }

    public function subscribeToList(string $email, string $listId, string $localeCode, bool $doubleOptInEnabled, array $data = []): ?array
    {
        $this->subscribedEmails[] = $email;
        return ['status' => 'subscribed'];
    }

    public function unsubscribeFromList(string $email, string $listId): ?array
    {
        return ['status' => 'unsubscribed'];
    }

    public function getLists(): array
    {
        return ['eshop_en_list_id' => 'Eshop EN'];
    }

    public function assertCalledWithEmail(string $expectedEmail): void
    {
        if (!in_array($expectedEmail, $this->calledEmails, true)) {
            throw new \RuntimeException(sprintf('Expected Mailchimp to check subscription for "%s" but it was not called.', $expectedEmail));
        }
    }

    public function assertSubscribedToEmail(string $expectedEmail): void
    {
        if (!in_array($expectedEmail, $this->subscribedEmails, true)) {
            throw new \RuntimeException(sprintf('Expected Mailchimp to subscribe "%s" but it was not called.', $expectedEmail));
        }
    }

    public function getSubscribedEmails(): array
    {
        return $this->subscribedEmails;
    }
}
