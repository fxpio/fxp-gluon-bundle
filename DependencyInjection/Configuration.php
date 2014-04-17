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
            ->append($this->getCommonAssetsNode())
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
                        ->booleanNode('semi_bold')->defaultTrue()->end()
                        ->booleanNode('semi_bold_italic')->defaultTrue()->end()
                        ->booleanNode('bold')->defaultFalse()->end()
                        ->booleanNode('bold_italic')->defaultFalse()->end()
                        ->booleanNode('extra_bold')->defaultFalse()->end()
                        ->booleanNode('extra_bold_italic')->defaultFalse()->end()
                    ->end()
                ->end()
                ->arrayNode('raleway')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->booleanNode('thin')->defaultTrue()->end()
                        ->booleanNode('extra_light')->defaultTrue()->end()
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
                        ->scalarNode('variables')->defaultValue('@SonatraGluonBundle/Resources/assetic/less/font-awesome-variables.less')->end()
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

    /**
     * Get common assets node.
     *
     * @return NodeDefinition
     */
    private function getCommonAssetsNode()
    {
        $treeBuilder = new TreeBuilder();
        $node = $treeBuilder->root('common_assets');

        $node
            ->addDefaultsIfNotSet()
            ->canBeDisabled()
            ->children()
                ->arrayNode('stylesheets')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->arrayNode('sonatra_form_extensions')
                            ->addDefaultsIfNotSet()
                            ->children()
                                ->arrayNode('select2')
                                    ->addDefaultsIfNotSet()
                                    ->children()
                                        ->scalarNode('select2')
                                            ->defaultFalse()
                                            ->beforeNormalization()
                                            ->ifTrue(function ($v) { return true === $v; })
                                                ->then(function ($v) { return '%kernel.root_dir%/../vendor/sonatra_ivaynberg/select2/select2.css'; })
                                            ->end()
                                        ->end()
                                        ->scalarNode('select2_bootstrap')
                                            ->defaultFalse()
                                            ->beforeNormalization()
                                            ->ifTrue(function ($v) { return true === $v; })
                                                ->then(function ($v) { return '%kernel.root_dir%/../vendor/sonatra_ivaynberg/select2/select2-bootstrap.css'; })
                                            ->end()
                                        ->end()
                                    ->end()
                                ->end()
                            ->end()
                        ->end()
                    ->end()
                ->end()
                ->arrayNode('theme_stylesheets')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->scalarNode('cache_directory')->defaultValue('%kernel.cache_dir%/sonatra_gluon/less')->end()
                        ->scalarNode('directory')->defaultValue('@SonatraGluonBundle/Resources/assetic/less')->end()
                        ->scalarNode('variables')->defaultValue('@SonatraGluonBundle/Resources/assetic/less/variables.less')->end()
                        ->scalarNode('mixins')->defaultValue('@SonatraGluonBundle/Resources/assetic/less/mixins.less')->end()
                        ->arrayNode('components')
                            ->addDefaultsIfNotSet()
                            ->children()
                                ->booleanNode('wrapper')->defaultTrue()->end()
                                ->booleanNode('global')->defaultTrue()->end()
                                ->booleanNode('type')->defaultTrue()->end()
                                ->booleanNode('tables')->defaultTrue()->end()
                                ->booleanNode('forms')->defaultTrue()->end()
                                ->booleanNode('buttons')->defaultTrue()->end()
                                ->booleanNode('dropdowns')->defaultTrue()->end()
                                ->booleanNode('input_groups')->defaultTrue()->end()
                                ->booleanNode('navs')->defaultTrue()->end()
                                ->booleanNode('navbar')->defaultTrue()->end()
                                ->booleanNode('labels')->defaultTrue()->end()
                                ->booleanNode('progress_bars')->defaultTrue()->end()
                                ->booleanNode('panels')->defaultTrue()->end()
                                ->booleanNode('panels_collapse')->defaultTrue()->end()
                                ->booleanNode('blocks')->defaultTrue()->end()
                                ->booleanNode('nav_scroll')->defaultTrue()->end()
                                ->booleanNode('sidebar')->defaultTrue()->end()
                                ->booleanNode('forms_select2')->defaultTrue()->end()
                                ->booleanNode('tables_pager')->defaultTrue()->end()
                                ->scalarNode('datetime_picker')->defaultValue('@SonatraFormExtensionsBundle/Resources/assetic/less/datetime-picker.less')->end()
                                ->scalarNode('hammer_scroll')->defaultValue('@SonatraGluonBundle/Resources/assetic/less/hammer-scroll.less')->end()
                                ->booleanNode('footable')->defaultTrue()->end()
                                ->booleanNode('account')->defaultTrue()->end()
                                ->booleanNode('errors')->defaultTrue()->end()
                            ->end()
                        ->end()
                    ->end()
                ->end()
                ->arrayNode('javascripts')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->arrayNode('markusslima')
                            ->addDefaultsIfNotSet()
                            ->children()
                                ->scalarNode('bootstrap_filestyle')
                                    ->defaultValue('%kernel.root_dir%/../vendor/sonatra_markusslima/bootstrap-filestyle/src/bootstrap-filestyle.js')
                                    ->beforeNormalization()
                                    ->ifTrue(function ($v) { return true === $v; })
                                        ->then(function ($v) { return '%kernel.root_dir%/../vendor/sonatra_markusslima/bootstrap-filestyle/src/bootstrap-filestyle.js'; })
                                    ->end()
                                ->end()
                            ->end()
                        ->end()
                        ->arrayNode('eightmedia')
                            ->addDefaultsIfNotSet()
                            ->children()
                                ->scalarNode('hammer')
                                    ->defaultValue('%kernel.root_dir%/../vendor/sonatra_eightmedia/hammer-js/hammer.js')
                                    ->beforeNormalization()
                                    ->ifTrue(function ($v) { return true === $v; })
                                        ->then(function ($v) { return '%kernel.root_dir%/../vendor/sonatra_eightmedia/hammer-js/hammer.js'; })
                                    ->end()
                                ->end()
                            ->end()
                        ->end()
                        ->arrayNode('moment')
                            ->addDefaultsIfNotSet()
                            ->children()
                                ->scalarNode('moment')
                                    ->defaultValue('%kernel.root_dir%/../vendor/sonatra_moment/moment/moment.js')
                                    ->beforeNormalization()
                                    ->ifTrue(function ($v) { return true === $v; })
                                        ->then(function ($v) { return '%kernel.root_dir%/../vendor/sonatra_moment/moment/moment.js'; })
                                    ->end()
                                ->end()
                            ->end()
                        ->end()
                        ->arrayNode('sonatra_form_extensions')
                            ->addDefaultsIfNotSet()
                            ->children()
                                ->scalarNode('select2')
                                    ->defaultValue('%kernel.root_dir%/../vendor/sonatra_ivaynberg/select2/select2.js')
                                    ->beforeNormalization()
                                    ->ifTrue(function ($v) { return true === $v; })
                                        ->then(function ($v) { return '%kernel.root_dir%/../vendor/sonatra_ivaynberg/select2/select2.js'; })
                                    ->end()
                                ->end()
                                ->scalarNode('datetime_picker')
                                    ->defaultValue('@SonatraFormExtensionsBundle/Resources/assetic/js/datetime-picker.js')
                                    ->beforeNormalization()
                                    ->ifTrue(function ($v) { return true === $v; })
                                        ->then(function ($v) { return '@SonatraFormExtensionsBundle/Resources/assetic/js/datetime-picker.js'; })
                                    ->end()
                                ->end()
                            ->end()
                        ->end()
                        ->arrayNode('sonatra_gluon')
                            ->addDefaultsIfNotSet()
                            ->children()
                                ->scalarNode('nav_scroll')
                                    ->defaultValue('@SonatraGluonBundle/Resources/assetic/js/nav-scroll.js')
                                    ->beforeNormalization()
                                    ->ifTrue(function ($v) { return true === $v; })
                                        ->then(function ($v) { return '@SonatraGluonBundle/Resources/assetic/js/nav-scroll.js'; })
                                    ->end()
                                ->end()
                                ->scalarNode('sidebar')
                                    ->defaultValue('@SonatraGluonBundle/Resources/assetic/js/sidebar.js')
                                    ->beforeNormalization()
                                    ->ifTrue(function ($v) { return true === $v; })
                                        ->then(function ($v) { return '@SonatraGluonBundle/Resources/assetic/js/sidebar.js'; })
                                    ->end()
                                ->end()
                                ->scalarNode('sticky_header')
                                    ->defaultValue('@SonatraGluonBundle/Resources/assetic/js/sticky-header.js')
                                    ->beforeNormalization()
                                    ->ifTrue(function ($v) { return true === $v; })
                                        ->then(function ($v) { return '@SonatraGluonBundle/Resources/assetic/js/sticky-header.js'; })
                                    ->end()
                                ->end()
                                ->scalarNode('hammer_scroll')
                                    ->defaultValue('@SonatraGluonBundle/Resources/assetic/js/hammer-scroll.js')
                                    ->beforeNormalization()
                                    ->ifTrue(function ($v) { return true === $v; })
                                        ->then(function ($v) { return '@SonatraGluonBundle/Resources/assetic/js/hammer-scroll.js'; })
                                    ->end()
                                ->end()
                                ->scalarNode('select2_hammer_scroll')
                                    ->defaultValue('@SonatraGluonBundle/Resources/assetic/js/select2-hammer-scroll.js')
                                    ->beforeNormalization()
                                    ->ifTrue(function ($v) { return true === $v; })
                                        ->then(function ($v) { return '@SonatraGluonBundle/Resources/assetic/js/select2-hammer-scroll.js'; })
                                    ->end()
                                ->end()
                                ->scalarNode('select2_responsive')
                                    ->defaultValue('@SonatraGluonBundle/Resources/assetic/js/select2-responsive.js')
                                    ->beforeNormalization()
                                    ->ifTrue(function ($v) { return true === $v; })
                                        ->then(function ($v) { return '@SonatraGluonBundle/Resources/assetic/js/select2-responsive.js'; })
                                    ->end()
                                ->end()
                                ->scalarNode('dropdown_position')
                                    ->defaultValue('@SonatraGluonBundle/Resources/assetic/js/dropdown-position.js')
                                    ->beforeNormalization()
                                    ->ifTrue(function ($v) { return true === $v; })
                                        ->then(function ($v) { return '@SonatraGluonBundle/Resources/assetic/js/dropdown-position.js'; })
                                    ->end()
                                ->end()
                                ->scalarNode('table_select')
                                    ->defaultValue('@SonatraGluonBundle/Resources/assetic/js/table-select.js')
                                    ->beforeNormalization()
                                    ->ifTrue(function ($v) { return true === $v; })
                                        ->then(function ($v) { return '@SonatraGluonBundle/Resources/assetic/js/table-select.js'; })
                                    ->end()
                                ->end()
                                ->scalarNode('table_pager')
                                    ->defaultValue('@SonatraGluonBundle/Resources/assetic/js/table-pager.js')
                                    ->beforeNormalization()
                                    ->ifTrue(function ($v) { return true === $v; })
                                        ->then(function ($v) { return '@SonatraGluonBundle/Resources/assetic/js/table-pager.js'; })
                                    ->end()
                                ->end()
                                ->scalarNode('footable')
                                    ->defaultValue('%kernel.root_dir%/../vendor/fooplugins/footable/js/footable.js')
                                    ->beforeNormalization()
                                    ->ifTrue(function ($v) { return true === $v; })
                                        ->then(function ($v) { return '%kernel.root_dir%/../vendor/fooplugins/footable/js/footable.js'; })
                                    ->end()
                                ->end()
                                ->scalarNode('footable_striped')
                                    ->defaultValue('@SonatraGluonBundle/Resources/assetic/js/footable-striped.js')
                                    ->beforeNormalization()
                                    ->ifTrue(function ($v) { return true === $v; })
                                        ->then(function ($v) { return '@SonatraGluonBundle/Resources/assetic/js/footable-striped.js'; })
                                    ->end()
                                ->end()
                                ->scalarNode('footable_auto_hide')
                                    ->defaultValue('@SonatraGluonBundle/Resources/assetic/js/footable-auto-hide.js')
                                    ->beforeNormalization()
                                    ->ifTrue(function ($v) { return true === $v; })
                                        ->then(function ($v) { return '@SonatraGluonBundle/Resources/assetic/js/footable-auto-hide.js'; })
                                    ->end()
                                ->end()
                                ->scalarNode('table_pager_footable')
                                    ->defaultValue('@SonatraGluonBundle/Resources/assetic/js/table-pager-footable.js')
                                    ->beforeNormalization()
                                    ->ifTrue(function ($v) { return true === $v; })
                                        ->then(function ($v) { return '@SonatraGluonBundle/Resources/assetic/js/table-pager-footable.js'; })
                                    ->end()
                                ->end()
                                ->scalarNode('nav_footable')
                                    ->defaultValue('@SonatraGluonBundle/Resources/assetic/js/nav-footable.js')
                                    ->beforeNormalization()
                                    ->ifTrue(function ($v) { return true === $v; })
                                        ->then(function ($v) { return '@SonatraGluonBundle/Resources/assetic/js/nav-footable.js'; })
                                    ->end()
                                ->end()
                                ->scalarNode('panel_collapse')
                                    ->defaultValue('@SonatraGluonBundle/Resources/assetic/js/panel-collapse.js')
                                    ->beforeNormalization()
                                    ->ifTrue(function ($v) { return true === $v; })
                                        ->then(function ($v) { return '@SonatraGluonBundle/Resources/assetic/js/panel-collapse.js'; })
                                    ->end()
                                ->end()
                            ->end()
                        ->end()
                    ->end()
                ->end()
            ->end()
        ;

        return $node;
    }
}
