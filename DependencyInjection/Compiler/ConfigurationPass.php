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

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBag;
use Symfony\Component\Yaml\Parser;

/**
 * @author François Pluchino <francois.pluchino@sonatra.com>
 */
class ConfigurationPass implements CompilerPassInterface
{
    /**
     * {@inheritdoc}
     */
    public function process(ContainerBuilder $container)
    {
        $parser = new Parser();
        $refl = new \ReflectionClass($this);
        $path = dirname(dirname(dirname($refl->getFileName()))).'/Resources/config/require_asset.yml';
        $config = $parser->parse(file_get_contents($path));

        if ($container->getParameter('sonatra_gluon.config.auto_configuration')) {
            $this->processManager($container, 'package_manager', 'addPackages', $config['packages']);
            $this->processManager($container, 'asset_replacement_manager', 'addReplacements', $config['asset_replacement']);
        }

        /* @var ParameterBag $pb */
        $pb = $container->getParameterBag();
        $pb->remove('sonatra_gluon.config.auto_configuration');
    }

    /**
     * @param ContainerBuilder $container The container builder
     * @param string           $parameter The parameter name
     * @param string           $method    The method call
     * @param array            $argument  The argument of method
     */
    protected function processManager(ContainerBuilder $container, $parameter, $method, array $argument)
    {
        $managerDef = $container->getDefinition('fxp_require_asset.assetic.config.'.$parameter);
        $managerDef->addMethodCall($method, array($argument));
    }
}
