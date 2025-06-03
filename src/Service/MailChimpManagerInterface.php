<?php

declare(strict_types=1);

namespace ThreeBRS\SyliusMailChimpPlugin\Service;

use ThreeBRS\SyliusMailChimpPlugin\Exception\MailChimpException;

interface MailChimpManagerInterface
{
    /**
     * @return array<mixed>|null
     */
    public function getContact(string $email, string $listId): ?array;

    public function isEmailSubscribedToList(string $email, string $listId): bool;

    /**
     * @param string $localeCode MailChimpLanguageEnum::SUPPORTED_LANGUAGES
     * @param array<mixed> $data
     *
     * @return array<mixed>|null
     *
     * @throws MailChimpException
     */
    public function subscribeToList(string $email, string $listId, string $localeCode, bool $doubleOptInEnabled, array $data = []): ?array;

    /**
     * @return array<mixed>|null
     */
    public function unsubscribeFromList(string $email, string $listId): ?array;

    /**
     * @return array<mixed>
     */
    public function getLists(): array;
}
