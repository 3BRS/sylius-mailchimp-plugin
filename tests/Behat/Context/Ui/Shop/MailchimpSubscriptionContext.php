<?php

declare(strict_types=1);

namespace Tests\ThreeBRS\SyliusMailChimpPlugin\Behat\Context\Ui\Shop;

use Behat\Behat\Context\Context;
use ThreeBRS\SyliusMailChimpPlugin\Service\mocks\MockMailChimpManager;
use PHPUnit\Framework\Assert;


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

    /**
     * @Then Mailchimp API should have been called to subscribe :email
     */
    public function mailchimpApiShouldHaveBeenCalledToSubscribe(string $email): void
    {
        $subscribed = $this->mailChimpManager->getSubscribedEmails();

        Assert::assertContains($email, $this->mailChimpManager->getSubscribedEmails());
    }
}
