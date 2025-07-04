<?php

declare(strict_types=1);

namespace ThreeBRS\SyliusMailChimpPlugin\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\Column;

trait ChannelMailChimpSettingsTrait
{
    /** @ORM\Column(type="string", nullable=true) */
    #[Column(type: 'string', nullable: true)]
    private ?string $mailChimpListId = null;

    /** @ORM\Column(type="boolean", options={"default": false}) */
    #[Column(type: 'boolean', options: ['default' => false])]
    private bool $isMailChimpListDoubleOptInEnabled = false;

    /** @ORM\Column(type="boolean", options={"default": false}) */
    #[Column(type: 'boolean', options: ['default' => false])]
    private bool $isMailChimpEnabled = false;

    public function setMailChimpListId(?string $listId): void
    {
        $this->mailChimpListId = $listId;
    }

    public function getMailChimpListId(): ?string
    {
        return $this->mailChimpListId;
    }

    public function setIsMailChimpListDoubleOptInEnabled(bool $isDoubleOptInEnabled): void
    {
        $this->isMailChimpListDoubleOptInEnabled = $isDoubleOptInEnabled;
    }

    public function isMailChimpListDoubleOptInEnabled(): bool
    {
        return $this->isMailChimpListDoubleOptInEnabled;
    }

    public function setIsMailChimpEnabled(bool $isMailChimpEnabled): void
    {
        $this->isMailChimpEnabled = $isMailChimpEnabled;
    }

    public function isMailChimpEnabled(): bool
    {
        return $this->isMailChimpEnabled;
    }
}
