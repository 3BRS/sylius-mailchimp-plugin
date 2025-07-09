<?php

declare(strict_types=1);

namespace ThreeBRS\SyliusMailChimpPlugin\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use ThreeBRS\SyliusMailChimpPlugin\Service\MailChimpApiClientProvider;

final class ThreeBRSSyliusMailChimpExtension extends Extension
{
    /**
     * @inheritdoc
     */
    public function load(array $config, ContainerBuilder $container): void
    {
        $loader = new Loader\YamlFileLoader($container, new FileLocator(__DIR__ . '/../../config'));
        $loader->load('services.yml');

        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $config);

        $definition = $container->getDefinition(MailChimpApiClientProvider::class);
        $definition->addArgument($config);
    }
}
