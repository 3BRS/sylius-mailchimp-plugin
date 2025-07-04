<?php

declare(strict_types=1);

namespace Tests\ThreeBRS\SyliusMailChimpPlugin\Entity;

use Doctrine\ORM\Mapping as ORM;
use Sylius\Component\Core\Model\Channel as BaseChannel;
use ThreeBRS\SyliusMailChimpPlugin\Entity\ChannelMailChimpSettingsInterface;
use ThreeBRS\SyliusMailChimpPlugin\Entity\ChannelMailChimpSettingsTrait;

/**
 * @ORM\Entity
 * @ORM\Table(name="sylius_channel")
 */
#[ORM\Entity]
#[ORM\Table(name: "sylius_channel")]
class Channel extends BaseChannel implements ChannelMailChimpSettingsInterface
{
    use ChannelMailChimpSettingsTrait;
}
