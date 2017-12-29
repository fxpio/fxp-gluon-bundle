<?php

/*
 * This file is part of the Fxp package.
 *
 * (c) François Pluchino <francois.pluchino@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Fxp\Bundle\GluonBundle;

use Fxp\Bundle\GluonBundle\DependencyInjection\Compiler\AddTemplatePathPass;
use Fxp\Bundle\GluonBundle\DependencyInjection\Compiler\BlockTemplatePass;
use Fxp\Bundle\GluonBundle\DependencyInjection\Compiler\FormTemplatePass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

/**
 * @author François Pluchino <francois.pluchino@gmail.com>
 */
class FxpGluonBundle extends Bundle
{
    public function build(ContainerBuilder $container)
    {
        parent::build($container);

        $container->addCompilerPass(new AddTemplatePathPass());
        $container->addCompilerPass(new FormTemplatePass());
        $container->addCompilerPass(new BlockTemplatePass());
    }
}
