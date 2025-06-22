<?php

declare(strict_types=1);

namespace ThreeBRS\SyliusMailChimpPlugin\Model;

use Symfony\Component\EventDispatcher\GenericEvent;
use Symfony\Component\Security\Http\Event\InteractiveLoginEvent;

interface CustomerListenerInterface
{
    public function syncSubscriptionToMailChimp(GenericEvent $event): void;

    public function syncSubscriptionStateFromMailChimp(InteractiveLoginEvent $event): void;
}
