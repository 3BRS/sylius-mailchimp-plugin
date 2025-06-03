<?php

declare(strict_types=1);

namespace Tests\Acme\SyliusExamplePlugin\Application\Entity;

use Doctrine\ORM\Mapping\MappedSuperclass;
use Doctrine\ORM\Mapping\Table;
use Sylius\Component\Core\Model\Channel as BaseChannel;
use ThreeBRS\SyliusMailChimpPlugin\Entity\ChannelMailChimpSettingsInterface;
use ThreeBRS\SyliusMailChimpPlugin\Entity\ChannelMailChimpSettingsTrait;

/**
 * @MappedSuperclass
 * @Table(name="sylius_channel")
 */
class Channel extends BaseChannel implements ChannelMailChimpSettingsInterface
{
    use ChannelMailChimpSettingsTrait;
}
