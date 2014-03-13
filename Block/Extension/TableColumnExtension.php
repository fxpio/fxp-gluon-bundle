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
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

/**
 * Table Column Block Extension.
 *
 * @author François Pluchino <francois.pluchino@sonatra.com>
 */
class TableColumnExtension extends AbstractTypeExtension
{
    /**
     * {@inheritdoc}
     */
    public function buildView(BlockView $view, BlockInterface $block, array $options)
    {
        $labelAttr = $view->vars['label_attr'];
        $attr = $view->vars['attr'];
        $labelClass = isset($view->vars['label_attr']['class']) ? $view->vars['label_attr']['class'] : '';
        $class = isset($view->vars['attr']['class']) ? $view->vars['attr']['class'] : '';
        $labelStyle = isset($view->vars['label_attr']['style']) ? $view->vars['label_attr']['style'] : '';

        if (null !== $options['align']) {
            $labelClass = trim($labelClass . ' table-' . $options['align']);
            $class = trim($class . ' table-' . $options['align']);
        }

        if (null !== $options['min_width']) {
            $labelStyle = trim($labelStyle . ' min-width:' . $options['min_width'] . 'px;');
        }

        if (null !== $options['max_width']) {
            $labelStyle = trim($labelStyle . ' max-width:' . $options['max_width'] . 'px;');
        }

        if ('' !== $labelClass) {
            $labelAttr['class'] = $labelClass;
        }

        if ('' !== $class) {
            $attr['class'] = $class;
        }

        if ('' !== $labelStyle) {
            $labelAttr['style'] = $labelStyle;
        }

        $view->vars = array_replace($view->vars, array(
            'label_attr' => $labelAttr,
            'attr'       => $attr,
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'align'     => null,
            'min_width' => null,
            'max_width' => null,
        ));

        $resolver->addAllowedTypes(array(
            'align'     => array('null', 'string'),
            'min_width' => array('null', 'int'),
            'max_width' => array('null', 'int'),
        ));

        $resolver->addAllowedValues(array(
            'align' => array('left', 'center', 'right'),
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getExtendedType()
    {
        return 'table_column';
    }
}
