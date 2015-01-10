<?php

/*
 * This file is part of the Sonatra package.
 *
 * (c) François Pluchino <francois.pluchino@sonatra.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sonatra\Bundle\GluonBundle;

use Sonatra\Bundle\GluonBundle\DependencyInjection\Compiler\ConfigurationPass;
use Symfony\Component\HttpKernel\Bundle\Bundle;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Sonatra\Bundle\GluonBundle\DependencyInjection\Compiler\FormTemplatePass;
use Sonatra\Bundle\GluonBundle\DependencyInjection\Compiler\BlockTemplatePass;

/**
 * @author François Pluchino <francois.pluchino@sonatra.com>
 */
class SonatraGluonBundle extends Bundle
{
    public function build(ContainerBuilder $container)
    {
        parent::build($container);

        $container->addCompilerPass(new FormTemplatePass());
        $container->addCompilerPass(new BlockTemplatePass());
        $container->addCompilerPass(new ConfigurationPass());
    }
}
