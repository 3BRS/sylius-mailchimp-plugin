<?php

declare(strict_types=1);

namespace ThreeBRS\SyliusMailChimpPlugin\Service;

interface ChannelMailChimpSettingsProviderInterface
{
    public function isMailChimpEnabled(): bool;

    public function getListId(): ?string;

    public function isDoubleOptInEnabled(): bool;
}
