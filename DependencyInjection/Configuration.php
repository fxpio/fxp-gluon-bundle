<?php

/*
 * This file is part of the Sonatra package.
 *
 * (c) François Pluchino <francois.pluchino@sonatra.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sonatra\Bundle\GluonBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * This is the class that validates and merges configuration from your app/config files.
 *
 * @author François Pluchino <francois.pluchino@sonatra.com>
 */
class Configuration implements ConfigurationInterface
{
    /**
     * {@inheritdoc}
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('sonatra_gluon');

        $rootNode
            ->append($this->getFontNode())
        ;

        return $treeBuilder;
    }

    /**
     * Get fonts node.
     *
     * @return NodeDefinition
     */
    private function getFontNode()
    {
        $treeBuilder = new TreeBuilder();
        $node = $treeBuilder->root('font');

        $node
            ->addDefaultsIfNotSet()
            ->canBeDisabled()
            ->children()
                ->arrayNode('open_sans')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->booleanNode('light')->defaultFalse()->end()
                        ->booleanNode('light_italic')->defaultFalse()->end()
                        ->booleanNode('normal')->defaultTrue()->end()
                        ->booleanNode('normal_italic')->defaultTrue()->end()
                        ->booleanNode('semi_bold')->defaultFalse()->end()
                        ->booleanNode('semi_bold_italic')->defaultFalse()->end()
                        ->booleanNode('bold')->defaultTrue()->end()
                        ->booleanNode('bold_italic')->defaultTrue()->end()
                        ->booleanNode('extra_bold')->defaultFalse()->end()
                        ->booleanNode('extra_bold_italic')->defaultFalse()->end()
                    ->end()
                ->end()
                ->arrayNode('raleway')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->booleanNode('thin')->defaultTrue()->end()
                        ->booleanNode('extra_light')->defaultFalse()->end()
                        ->booleanNode('light')->defaultFalse()->end()
                        ->booleanNode('normal')->defaultFalse()->end()
                        ->booleanNode('medium')->defaultFalse()->end()
                        ->booleanNode('semi_bold')->defaultFalse()->end()
                        ->booleanNode('bold')->defaultFalse()->end()
                        ->booleanNode('extra_bold')->defaultFalse()->end()
                        ->booleanNode('ultra_bold')->defaultFalse()->end()
                    ->end()
                ->end()
            ->end()
        ;

        return $node;
    }
}
