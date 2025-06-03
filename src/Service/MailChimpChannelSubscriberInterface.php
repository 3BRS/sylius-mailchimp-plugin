<?php

declare(strict_types=1);

namespace ThreeBRS\SyliusMailChimpPlugin\Service;

interface MailChimpChannelSubscriberInterface
{
    /**
     * @return array<mixed>|null
     */
    public function getContact(string $email): ?array;

    public function isSubscribed(string $email): bool;

    /**
     * @param array<mixed> $data
     */
    public function subscribe(string $email, array $data = []): void;

    public function unsubscribe(string $email): void;
}
