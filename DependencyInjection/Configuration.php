<?php

/*
 * This file is part of the Fxp package.
 *
 * (c) François Pluchino <francois.pluchino@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Fxp\Bundle\GluonBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * This is the class that validates and merges configuration from your app/config files.
 *
 * @author François Pluchino <francois.pluchino@gmail.com>
 */
class Configuration implements ConfigurationInterface
{
    /**
     * {@inheritdoc}
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder('fxp_gluon');
        /* @var ArrayNodeDefinition $rootNode */
        $rootNode = $treeBuilder->getRootNode();

        $rootNode
            ->children()
                ->booleanNode('auto_configuration')->defaultTrue()
            ->end()
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
        $treeBuilder = new TreeBuilder('google_fonts');
        /* @var ArrayNodeDefinition $node */
        $node = $treeBuilder->getRootNode();

        $node
            ->addDefaultsIfNotSet()
            ->children()
                ->arrayNode('fonts')
                    ->useAttributeAsKey('name', false)
                    ->normalizeKeys(false)
                    ->defaultValue($this->getDefaultFonts())
                    ->prototype('array')
                        ->prototype('scalar')->end()
                    ->end()
                ->end()
                ->arrayNode('icons')
                    ->useAttributeAsKey('name', false)
                    ->normalizeKeys(false)
                    ->defaultValue([])
                    ->prototype('array')
                        ->prototype('scalar')->end()
                    ->end()
                ->end()
            ->end()
        ;

        return $node;
    }

    /**
     * Get the default fonts.
     *
     * @return array
     */
    private function getDefaultFonts()
    {
        return [
            'Open Sans' => [400, '400italic', 600, '600italic'],
            'Raleway' => [100, 200],
        ];
    }

    /**
     * Get Font Awesome Node.
     *
     * @return ArrayNodeDefinition
     */
    private function getFontAwesomeNode()
    {
        $treeBuilder = new TreeBuilder('font_awesome');
        /* @var ArrayNodeDefinition $node */
        $node = $treeBuilder->getRootNode();

        $node
            ->canBeEnabled()
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
