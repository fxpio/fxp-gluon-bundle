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

use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
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
            ->append($this->getGoogleFontsNode())
            ->append($this->getFontAwesomeNode())
        ;

        return $treeBuilder;
    }

    /**
     * Get Google Fonts Node.
     *
     * @return ArrayNodeDefinition
     */
    private function getGoogleFontsNode()
    {
        $treeBuilder = new TreeBuilder();
        $node = $treeBuilder->root('google_fonts');

        $node
            ->children()
                ->scalarNode('common_name')->defaultValue('google_fonts')->end()
                ->scalarNode('output')->defaultValue('css/google-fonts.css')->end()
                ->arrayNode('filters')
                    ->prototype('scalar')->end()
                ->end()
                ->arrayNode('options')
                    ->useAttributeAsKey('name')
                    ->normalizeKeys(false)
                    ->prototype('variable')->end()
                ->end()
                ->arrayNode('attributes')
                    ->useAttributeAsKey('name')
                    ->normalizeKeys(false)
                    ->prototype('variable')->end()
                ->end()
                ->arrayNode('fonts')
                    ->useAttributeAsKey('name', false)
                    ->normalizeKeys(false)
                    ->prototype('array')
                        ->prototype('scalar')->end()
                    ->end()
                ->end()
            ->end()
        ;

        return $node;
    }

    /**
     * Get Font Awesome Node.
     *
     * @return ArrayNodeDefinition
     */
    private function getFontAwesomeNode()
    {
        $treeBuilder = new TreeBuilder();
        $node = $treeBuilder->root('font_awesome');

        $node
            ->canBeDisabled()
            ->children()
                ->scalarNode('path')->defaultValue('@bower/font-awesome/css/font-awesome.css')->end()
                ->arrayNode('attributes')
                    ->useAttributeAsKey('name')
                    ->normalizeKeys(false)
                    ->prototype('variable')->end()
                ->end()
            ->end()
        ;

        return $node;
    }
}
