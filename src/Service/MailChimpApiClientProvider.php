<?php

declare(strict_types=1);

namespace ThreeBRS\SyliusMailChimpPlugin\Service;

use DrewM\MailChimp\MailChimp;

class MailChimpApiClientProvider implements MailChimpApiClientProviderInterface
{
    private ?string $apiKey;

    private ?MailChimp $client = null;

    /**
     * @param array<mixed> $config
     */
    public function __construct(array $config)
    {
        $this->apiKey = $config['mailchimp_api_key'];
    }

    public function getClient(): ?MailChimp
    {
        if ($this->apiKey !== null && $this->client === null) {
            $this->client = new MailChimp($this->apiKey);

            return $this->client;
        }

        return null;
    }
}
