<?php

declare(strict_types=1);

namespace ThreeBRS\SyliusMailChimpPlugin\Model;

use Psr\Log\LoggerInterface;
use Sylius\Component\Core\Model\CustomerInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\ShopUser;
use Symfony\Component\EventDispatcher\GenericEvent;
use Symfony\Component\Security\Http\Event\InteractiveLoginEvent;
use ThreeBRS\SyliusMailChimpPlugin\Exception\MailChimpException;
use ThreeBRS\SyliusMailChimpPlugin\Service\ChannelMailChimpSettingsProviderInterface;
use ThreeBRS\SyliusMailChimpPlugin\Service\MailChimpChannelSubscriberInterface;

class CustomerListener implements CustomerListenerInterface
{
    private bool $isMailChimpEnabled = false;

    public function __construct(
        private readonly MailChimpChannelSubscriberInterface $mailChimpChannelSubscriber,
        private readonly LoggerInterface $logger,
        ChannelMailChimpSettingsProviderInterface $channelMailChimpSettingsProvider,
    ) {
        $this->isMailChimpEnabled = $channelMailChimpSettingsProvider->isMailChimpEnabled() && $channelMailChimpSettingsProvider->getListId() !== null;
    }

    public function syncCustomerToMailChimp(CustomerInterface $customer): void
    {
        $email = $customer->getEmailCanonical();

        if ($email === null) {
            return;
        }

        try {
            $isSubscribed = $this->mailChimpChannelSubscriber->isSubscribed($email);

            if ($isSubscribed && !$customer->isSubscribedToNewsletter()) {
                $this->mailChimpChannelSubscriber->unsubscribe($email);
            } elseif (!$isSubscribed && $customer->isSubscribedToNewsletter()) {
                $this->mailChimpChannelSubscriber->subscribe($email);
            }
        } catch (MailChimpException $e) {
            $this->logger->error($e->getMessage() . ', when trying to sync subscription to mailChimp', [
                'exception' => $e,
                'customerId' => $customer->getId(),
            ]);
        }
    }

    public function syncSubscriptionToMailChimp(GenericEvent $event): void
    {
        if (!$this->isMailChimpEnabled) {
            return;
        }

        $subject = $event->getSubject();
        if ($subject instanceof CustomerInterface) {
            $this->syncCustomerToMailChimp($subject);
        } elseif ($subject instanceof OrderInterface) {
            $customer = $subject->getCustomer();
            if ($customer instanceof CustomerInterface) {
                $this->syncCustomerToMailChimp($customer);
            }
        }
    }

    public function syncSubscriptionStateFromMailChimp(InteractiveLoginEvent $event): void
    {
        if (!$this->isMailChimpEnabled) {
            return;
        }

        $user = $event->getAuthenticationToken()->getUser();

        if (!($user instanceof ShopUser) || $user->getCustomer() === null) {
            return;
        }

        $customer = $user->getCustomer();
        assert($customer instanceof CustomerInterface);
        $email = $customer->getEmailCanonical();
        if ($email === null) {
            return;
        }

        try {
            $isSubscribed = $this->mailChimpChannelSubscriber->isSubscribed($email);
            $customer->setSubscribedToNewsletter($isSubscribed);
        } catch (MailChimpException $e) {
            $this->logger->error($e->getMessage() . ', when trying to fetch subscription state', [
                'exception' => $e,
                'customerId' => $customer->getId(),
            ]);
        }
    }
}
