<?php

/**
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
 * Configure the bootstrap stylesheet.
 *
 * @author François Pluchino <francois.pluchino@sonatra.com>
 */
class BootstrapStylesheetPass implements CompilerPassInterface
{
    /**
     * {@inheritdoc}
     */
    public function process(ContainerBuilder $container)
    {
        if (!$container->hasDefinition('sonatra_bootstrap.assetic.common_stylesheets_resource.stylesheet')) {
            return;
        }

        $def = $container->getDefinition('sonatra_bootstrap.assetic.common_stylesheets_resource.stylesheet');
        $components = $def->getArgument(2);

        $configs = $container->getExtensionConfig('sonatra_bootstrap');

        if (!isset($configs[0]['common_assets']['stylesheets']['bootstrap']['components']['variables'])) {
            $components['variables'] = '@SonatraGluonBundle/Resources/assetic/less/variables.less';
            $components['mixins'] = '@SonatraGluonBundle/Resources/assetic/less/mixins.less';
        }

        $def->replaceArgument(2, $components);
    }
}
