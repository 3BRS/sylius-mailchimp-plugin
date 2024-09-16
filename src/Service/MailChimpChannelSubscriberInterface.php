<?php

declare(strict_types=1);

namespace ThreeBRS\SyliusMailChimpPlugin\Service;

interface MailChimpChannelSubscriberInterface
{
    public function getContact(string $email): ?array;

    public function isSubscribed(string $email): bool;

    public function subscribe(string $email, array $data = []): void;

    public function unsubscribe(string $email): void;
}
