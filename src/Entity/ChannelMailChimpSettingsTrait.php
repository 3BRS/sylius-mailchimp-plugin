<?php

declare(strict_types=1);

namespace ThreeBRS\SyliusMailChimpPlugin\Entity;

use Doctrine\ORM\Mapping\Column;

trait ChannelMailChimpSettingsTrait
{
    #[Column(type: 'string', nullable: true)]
    private ?string $mailChimpListId = null;

    #[Column(type: 'boolean', options: ['default' => false])]
    private bool $isMailChimpListDoubleOptInEnabled = false;

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
