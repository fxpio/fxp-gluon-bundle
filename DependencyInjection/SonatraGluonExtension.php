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

use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader;
use Symfony\Component\DependencyInjection\Extension\PrependExtensionInterface;
use Symfony\Component\Config\FileLocator;

/**
 * This is the class that loads and manages your bundle configuration.
 *
 * @author François Pluchino <francois.pluchino@sonatra.com>
 */
class SonatraGluonExtension extends Extension implements PrependExtensionInterface
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
        $loader->load('twig.xml');

        if ($config['font']['enabled']) {
            $this->configFonts($config['font'], $container);
        }
    }

    /**
     * Configures the fonts resource.
     *
     * @param array            $config
     * @param ContainerBuilder $container
     */
    protected function configFonts(array &$config, ContainerBuilder $container)
    {
        $openSans = 'https://fonts.googleapis.com/css?family=Open+Sans:';
        $raleway = 'https://fonts.googleapis.com/css?family=Raleway:';

        foreach ($config['open_sans'] as $font => $enabled) {
            switch ($font) {
                case 'light':
                    $openSans .= !$enabled ? '' : '300';
                    break;
                case 'light_italic':
                    $openSans .= !$enabled ? '' : '300italic';
                    break;
                case 'normal':
                    $openSans .= !$enabled ? '' : '400';
                    break;
                case 'normal_italic':
                    $openSans .= !$enabled ? '' : '400italic';
                    break;
                case 'semi_bold':
                    $openSans .= !$enabled ? '' : '600';
                    break;
                case 'semi_bold_italic':
                    $openSans .= !$enabled ? '' : '600italic';
                    break;
                case 'bold':
                    $openSans .= !$enabled ? '' : '700';
                    break;
                case 'bold_italic':
                    $openSans .= !$enabled ? '' : '700italic';
                    break;
                case 'extra_bold':
                    $openSans .= !$enabled ? '' : '800';
                    break;
                case 'extra_bold_italic':
                    $openSans .= !$enabled ? '' : '800italic';
                    break;
                default:
                   break;
            }

            $openSans .= !$enabled ? '' : ',';
        }

        foreach ($config['raleway'] as $font => $enabled) {
            switch ($font) {
                case 'thin':
                    $raleway .= !$enabled ? '' : '100';
                    break;
                case 'extra_light':
                    $raleway .= !$enabled ? '' : '200';
                    break;
                case 'light':
                    $raleway .= !$enabled ? '' : '300';
                    break;
                case 'normal':
                    $raleway .= !$enabled ? '' : '400';
                    break;
                case 'medium':
                    $raleway .= !$enabled ? '' : '500';
                    break;
                case 'semi_bold':
                    $raleway .= !$enabled ? '' : '600';
                    break;
                case 'bold':
                    $raleway .= !$enabled ? '' : '700';
                    break;
                case 'extra_bold':
                    $raleway .= !$enabled ? '' : '800';
                    break;
                case 'ultra_bold':
                    $raleway .= !$enabled ? '' : '900';
                    break;
                default:
                    break;
            }

            $raleway .= !$enabled ? '' : ',';
        }

        $def = $container->getDefinition('sonatra_gluon.twig.extension.font');
        $def->addArgument(rtrim($openSans, ','));
        $def->addArgument(rtrim($raleway, ','));
    }

    /**
     * {@inheritdoc}
     */
    public function prepend(ContainerBuilder $container)
    {
        $exts = $container->getExtensions();

        if (isset($exts['sonatra_bootstrap'])) {
            $container->prependExtensionConfig(
                'sonatra_bootstrap',
                array(
                    'common_assets' => array(
                        'stylesheets' => array(
                            'bootstrap' => array(
                                'components' => array(
                                    'default_variables' => '@SonatraGluonBundle/Resources/assetic/less/variables.less'
                                ),
                            ),
                        ),
                    ),
                )
            );
        }
    }
}
