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

use Fxp\Component\RequireAsset\Tag\Config\RequireStyleTagConfiguration;
use Symfony\Component\Config\Definition\Processor;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Loader;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;

/**
 * This is the class that loads and manages your bundle configuration.
 *
 * @author François Pluchino <francois.pluchino@gmail.com>
 */
class FxpGluonExtension extends Extension
{
    /**
     * {@inheritdoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        $loader = new Loader\XmlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('response.xml');
        $loader->load('block.xml');
        $loader->load('form.xml');
        $loader->load('twig.xml');

        $container->setParameter('fxp_gluon.config.auto_configuration', $config['auto_configuration']);
        $this->configGoogleFonts($config['google_fonts'], $container);
        $this->configFontAwesome($config['font_awesome'], $container);
    }

    /**
     * Configures the google fonts resource.
     *
     * @param array            $config    The config
     * @param ContainerBuilder $container The container builder
     */
    protected function configGoogleFonts(array $config, ContainerBuilder $container)
    {
        $inputs = $this->configGoogleTypeFonts('icon', $config['icons']);
        $inputs = array_merge($inputs, $this->configGoogleTypeFonts('css', $config['fonts']));

        $container->setParameter('fxp_gluon.template.google_fonts', $inputs);
    }

    /**
     * Configures the google font resource.
     *
     * @param string $type  The google font type
     * @param array  $fonts The google fonts
     *
     * @return array The urls of fonts
     */
    protected function configGoogleTypeFonts($type, array $fonts)
    {
        $url = 'https://fonts.googleapis.com/%s?family=%s:%s';
        $inputs = array();

        foreach ($fonts as $name => $weights) {
            $name = str_replace(' ', '+', $name);
            $weights = implode(',', $weights);
            $inputs[] = rtrim(sprintf($url, $type, $name, $weights), ':');
        }

        return $inputs;
    }

    /**
     * Configure the font awesome.
     *
     * @param array            $config    The config
     * @param ContainerBuilder $container The container builder
     */
    protected function configFontAwesome(array $config, ContainerBuilder $container)
    {
        if ($config['enabled']) {
            $this->addTwigRequireTag($container, $config['path'], $config['attributes']);
        }
    }

    /**
     * Add twig require tag.
     *
     * @param ContainerBuilder $container  The container builder
     * @param string           $name       The require asset name of google fonts resource
     * @param array            $attributes The HTML tag attributes
     */
    protected function addTwigRequireTag(ContainerBuilder $container, $name, array $attributes = array())
    {
        $processor = new Processor();
        $configuration = new RequireStyleTagConfiguration();
        $attributes = $processor->process($configuration->getNode(), array($attributes));

        $definition = new Definition('Fxp\Component\RequireAsset\Tag\RequireStyleTag', array($name, $attributes));
        $definition->setPublic(false);
        $definition->addTag('fxp_require_asset.require_tag');

        $container->setDefinition('fxp_gluon.twig.require_tag.'.$name, $definition);
    }
}
