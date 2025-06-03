<?php

declare(strict_types=1);

namespace ThreeBRS\SyliusMailChimpPlugin\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

final class Configuration implements ConfigurationInterface
{
    /**
     * @inheritdoc
     */
    public function getConfigTreeBuilder(): TreeBuilder
    {
        $treeBuilder = new TreeBuilder('three_brs_sylius_mail_chimp');
        $rootNode = $treeBuilder->getRootNode();

        $rootNode
            ->children()
                ->scalarNode('mailchimp_api_key')
                ->isRequired()
                ->cannotBeEmpty()
                ->end()
            ->end();

        return $treeBuilder;
    }
}
