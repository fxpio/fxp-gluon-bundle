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
            ->append($this->getFontAwesomeNode())
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

    /**
     * Get font awesome node.
     *
     * @return NodeDefinition
     */
    private function getFontAwesomeNode()
    {
        $treeBuilder = new TreeBuilder();
        $node = $treeBuilder->root('font_awesome');

        $node
            ->addDefaultsIfNotSet()
            ->fixXmlConfig('path')
            ->children()
                ->arrayNode('font')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->arrayNode('paths')
                            ->addDefaultChildrenIfNoneSet()
                            ->prototype('scalar')->defaultValue('%kernel.root_dir%/../vendor/fortawesome/font-awesome/fonts')->end()
                            ->example(array('%kernel.root_dir%/../vendor/fortawesome/font-awesome/fonts', '%kernel.root_dir%/../vendor/foo/bar/fonts'))
                            ->validate()
                                ->ifTrue(function ($v) { return !in_array('%kernel.root_dir%/../vendor/fortawesome/font-awesome/fonts', $v); })
                                ->then(function ($v) {
                                    return array_merge(array('%kernel.root_dir%/../vendor/fortawesome/font-awesome/fonts'), $v);
                                })
                            ->end()
                        ->end()
                    ->end()
                ->end()
                ->booleanNode('build')->defaultTrue()->end()
                ->scalarNode('cache_directory')->defaultValue('%kernel.cache_dir%/sonatra_gluon/less')->end()
                ->scalarNode('directory')->defaultValue('%kernel.root_dir%/../vendor/fortawesome/font-awesome/less')->end()
                ->arrayNode('components')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->booleanNode('variables')->defaultTrue()->end()
                        ->scalarNode('default_variables')->defaultValue('@SonatraGluonBundle/Resources/assetic/less/font-awesome-variables.less')->end()
                        ->scalarNode('custom_variables')->defaultNull()->end()
                        ->booleanNode('mixins')->defaultTrue()->end()
                        ->scalarNode('custom_mixins')->defaultNull()->end()
                        ->booleanNode('path')->defaultTrue()->end()
                        ->booleanNode('core')->defaultTrue()->end()
                        ->booleanNode('larger')->defaultTrue()->end()
                        ->booleanNode('fixed_width')->defaultTrue()->end()
                        ->booleanNode('list')->defaultFalse()->end()
                        ->booleanNode('bordered_pulled')->defaultFalse()->end()
                        ->booleanNode('spinning')->defaultTrue()->end()
                        ->booleanNode('rotated_flipped')->defaultTrue()->end()
                        ->booleanNode('stacked')->defaultFalse()->end()
                        ->booleanNode('icons')->defaultTrue()->end()
                    ->end()
                ->end()
            ->end()
        ;

        return $node;
    }
}
