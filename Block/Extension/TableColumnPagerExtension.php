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
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Table Column Pager Block Extension.
 *
 * @author François Pluchino <francois.pluchino@sonatra.com>
 */
class TableColumnPagerExtension extends AbstractTypeExtension
{
    /**
     * {@inheritdoc}
     */
    public function buildView(BlockView $view, BlockInterface $block, array $options)
    {
        $attr = $view->vars['label_attr'];

        if ($options['sortable']) {
            $attr['data-table-pager-sortable'] = 'true';
        }

        $view->vars = array_replace($view->vars, array(
            'sortable' => $options['sortable'],
            'label_attr' => $attr,
        ));

        if ($options['sortable'] && !isset($attr['data-table-sort'])) {
            $view->vars['value'] = is_string($view->vars['value']) ? $view->vars['value'] : '';
            $view->vars['value'] .= '<i class="table-sort-icon fa"></i>';
        }
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'sortable' => false,
        ));

        $resolver->addAllowedTypes('sortable', 'bool');
    }

    /**
     * {@inheritdoc}
     */
    public function getExtendedType()
    {
        return 'table_column';
    }
}
