<?php

declare(strict_types=1);

namespace Tests\ThreeBRS\SyliusMailChimpPlugin\Behat\Page\Admin\Channel;

use Behat\Mink\Session;
use Sylius\Behat\Page\Admin\Channel\UpdatePage as BaseUpdatePage;
use Tests\ThreeBRS\SyliusMailChimpPlugin\Behat\Page\Admin\Channel\UpdatePageInterface;

final class UpdatePage extends BaseUpdatePage implements UpdatePageInterface
{
    public function getRouteName(): string
    {
        return 'sylius_admin_channel_update';
    }

    public function enableMailchimp(): void
    {
        $this->getElement('mailchimp_enabled')->check();
    }

    public function enableDoubleOptIn(): void
    {
        $this->getElement('double_opt_in_enabled')->check();
    }

    public function specifyMailchimpListId(string $listId): void
    {
        $this->getElement('mailchimp_list_id')->setValue($listId);
    }

    protected function getDefinedElements(): array
    {
        return [
            'mailchimp_enabled' => '#sylius_channel_isMailChimpEnabled',
            'double_opt_in_enabled' => '#sylius_channel_isMailChimpListDoubleOptInEnabled',
            'mailchimp_list_id' => '#sylius_channel_mailChimpListId',
        ];
    }
}
