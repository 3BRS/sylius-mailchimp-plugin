<?php

declare(strict_types=1);

namespace ThreeBRS\SyliusMailChimpPlugin\Service\mocks;

use ThreeBRS\SyliusMailChimpPlugin\Service\ChannelMailChimpSettingsProviderInterface;

final class TestMailChimpSettingsProvider implements ChannelMailChimpSettingsProviderInterface
{
    public function isMailChimpEnabled(): bool
    {
        return true;
    }

    public function getListId(): ?string
    {
        return 'eshop_en_list_id';
    }

    public function isDoubleOptInEnabled(): bool
    {
        return false;
    }
}
