<?php

declare(strict_types=1);

namespace ThreeBRS\SyliusMailChimpPlugin\Service;

use DrewM\MailChimp\MailChimp;

class MailChimpApiClientProvider implements MailChimpApiClientProviderInterface
{
    /** @var string|null */
    private $apiKey;

    /** @var MailChimp|null */
    private $client;

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
