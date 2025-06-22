<?php

declare(strict_types=1);

namespace Tests\ThreeBRS\SyliusMailChimpPlugin\Behat\Context\Ui\Shop;

use Behat\Behat\Context\Context;
use ThreeBRS\SyliusMailChimpPlugin\Service\mocks\MockMailChimpManager;

final class MailchimpSubscriptionContext implements Context
{
    public function __construct(private MockMailChimpManager $mailChimpManager) {}

    /**
     * @Then Mailchimp should have checked if the email :email is subscribed
     */
    public function mailchimpShouldHaveCheckedEmail(string $email): void
    {
        $this->mailChimpManager->assertCalledWithEmail($email);
    }
}
