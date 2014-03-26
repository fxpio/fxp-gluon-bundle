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
use Sonatra\Bundle\BlockBundle\Block\BlockView;
use Sonatra\Bundle\BlockBundle\Block\BlockInterface;
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
        $source = $block->getParent()->getData();
        $sort = $source->getSortColumn($block->getName());

        if (null !== $sort) {
            $view->vars['label_attr']['data-table-sort'] = $sort;
            $view->vars['value'] = is_string($view->vars['value']) ? $view->vars['value'] : '';
            $view->vars['value'] .= '<i class="table-sort-icon fa"></i>';
        }
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
            'width'     => null,
        ));

        $resolver->addAllowedTypes(array(
            'align'     => array('null', 'string'),
            'min_width' => array('null', 'int'),
            'max_width' => array('null', 'int'),
        ));

        $resolver->addAllowedValues(array(
            'align' => array('left', 'center', 'right'),
        ));

        $resolver->setNormalizers(array(
            'label_attr' => function (Options $options, $value) {
                $class = isset($value['class']) ? $value['class'] : '';
                $style = isset($value['style']) ? $value['style'] : '';

                if ($options['align']) {
                    $class = trim($class . ' table-' . $options['align']);
                }

                if (null !== $options['min_width']) {
                    $style = trim($style . ' min-width:' . $options['min_width'] . 'px;');
                }

                if (null !== $options['max_width']) {
                    $style = trim($style . ' max-width:' . $options['max_width'] . 'px;');
                }

                if (null !== $options['width']) {
                    $style = trim($style . ' width:' . $options['width'] . 'px;');
                }

                if ('' !== $class) {
                   $value['class'] = $class;
                }

                if ('' !== $style) {
                    $value['style'] = $style;
                }

                return $value;
            },
            'attr' => function (Options $options, $value) {
                $class = isset($value['class']) ? $value['class'] : '';

                if ($options['align']) {
                    $class = trim($class . ' table-' . $options['align']);
                }

                if ('' !== $class) {
                   $value['class'] = $class;
                }

                return $value;
            },
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
