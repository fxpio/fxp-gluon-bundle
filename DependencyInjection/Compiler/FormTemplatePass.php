<?php

/*
 * This file is part of the Sonatra package.
 *
 * (c) François Pluchino <francois.pluchino@sonatra.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sonatra\Bundle\GluonBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

/**
 * Add a custom form template in twig.form.resources.
 *
 * @author François Pluchino <francois.pluchino@sonatra.com>
 */
class FormTemplatePass implements CompilerPassInterface
{
    /**
     * {@inheritdoc}
     */
    public function process(ContainerBuilder $container)
    {
        $configs = $container->getExtensionConfig('twig');
        $resources = $container->getParameter('twig.form.resources');
        $offset = count($resources);

        if (isset($configs[0]['form_themes'])) {
            $configResources = $configs[0]['form_themes'];

            $offset = array_search($configResources[count($configResources) - 1], $resources);
        }

        array_splice($resources, $offset, 0, array(
            '@SonatraGluon/Form/form_bootstrap.html.twig',
        ));

        $container->setParameter('twig.form.resources', $resources);
    }
}
