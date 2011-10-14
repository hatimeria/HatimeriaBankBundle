<?php

namespace Hatimeria\BankBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface
{
    /**
     * Generates the configuration tree.
     *
     * @return TreeBuilder
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('hatimeria_bank');

        $rootNode
            ->children()
                ->scalarNode('model_classes_path')->isRequired()->end()
                ->booleanNode('fake_dotpay_response')->defaultFalse()->end();
        
        return $treeBuilder;
    }
    
}
