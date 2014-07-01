<?php

/*
 * This file is part of the Sonatra package.
 *
 * (c) François Pluchino <francois.pluchino@sonatra.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sonatra\Bundle\GluonBundle\Block\Extension;

use Sonatra\Bundle\BlockBundle\Block\AbstractTypeExtension;
use Sonatra\Bundle\BlockBundle\Block\BlockInterface;
use Sonatra\Bundle\BlockBundle\Block\BlockView;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

/**
 * Checkbox Block Extension.
 *
 * @author François Pluchino <francois.pluchino@sonatra.com>
 */
class CheckboxExtension extends AbstractTypeExtension
{
    /**
     * {@inheritdoc}
     */
    public function buildView(BlockView $view, BlockInterface $block, array $options)
    {
        $view->vars = array_replace($view->vars, array(
            'style' => $options['style'],
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'style' => null,
        ));

        $resolver->setAllowedTypes(array(
            'style' => array('null', 'string'),
        ));

        $resolver->setAllowedValues(array(
            'style' => array('default', 'primary', 'secondary', 'success', 'info', 'warning', 'danger', 'link'),
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getExtendedType()
    {
        return 'checkbox';
    }
}
