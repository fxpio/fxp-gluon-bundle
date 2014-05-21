<?php

/**
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
use Sonatra\Bundle\BlockBundle\Block\Util\BlockUtil;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

/**
 * Panel Block Extension.
 *
 * @author François Pluchino <francois.pluchino@sonatra.com>
 */
class PanelExtension extends AbstractTypeExtension
{
    /**
     * {@inheritdoc}
     */
    public function addChild(BlockInterface $child, BlockInterface $block, array $options)
    {
        if ($options['collapsible'] && BlockUtil::isValidBlock('panel_header', $child)) {
            $child->add('_panel_actions', 'panel_actions', array());
            $child->get('_panel_actions')->add('_button_collapse', 'button', array(
                'label'       => '',
                'attr'        => array('class' => 'btn-panel-collapse'),
                'prepend'     => '<span class="caret"></span>'
            ));

        } elseif (BlockUtil::isValidBlock('panel', $child)) {
            if ($block->getOption('recursive_style')) {
                $child->setOption('style', $block->getOption('style'));
            }

        } elseif (BlockUtil::isValidBlock('panel_section', $child)) {
            $cOptions = array();

            if (null !== $block->getOption('cell_label_style') && null === $child->getOption('cell_label_style')) {
                $cOptions['cell_label_style'] = $block->getOption('cell_label_style');
            }

            if (null !== $block->getOption('cell_layout_size') && null === $child->getOption('layout_size')) {
                $cOptions['layout_size'] = $block->getOption('cell_layout_size');
            }

            if (count($cOptions) > 0) {
                $child->setOptions($cOptions);
            }
        }
    }

    /**
     * {@inheritdoc}
     */
    public function buildView(BlockView $view, BlockInterface $block, array $options)
    {
        $view->vars = array_replace($view->vars, array(
            'border_top_style' => $options['border_top_style'],
            'collapsible'      => $options['collapsible'],
            'collapsed'        => $options['collapsed'],
            'panels_rendered'  => $options['panels_rendered'],
            'hidden_if_empty'  => $options['hidden_if_empty'],
            'recursive_style'  => $options['recursive_style'],
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function finishView(BlockView $view, BlockInterface $block, array $options)
    {
        $relatedPanels = array();

        foreach ($view->children as $name => $child) {
            if (in_array('panel', $child->vars['block_prefixes'])) {
                $relatedPanels[] = $child;
                unset($view->children[$name]);
            }
        }

        $view->vars['related_panels'] = $relatedPanels;
    }

    /**
     * {@inheritdoc}
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'border_top_style' => null,
            'cell_label_style' => null,
            'cell_layout_size' => null,
            'collapsible'      => false,
            'collapsed'        => false,
            'panels_rendered'  => true,
            'hidden_if_empty'  => true,
            'recursive_style'  => false,
        ));

        $resolver->addAllowedTypes(array(
            'border_top_style' => array('null', 'string'),
            'cell_label_style' => array('null', 'string'),
            'cell_layout_size' => array('null', 'string'),
            'collapsible'      => 'bool',
            'collapsed'        => 'bool',
            'panels_rendered'  => 'bool',
            'hidden_if_empty'  => 'bool',
            'recursive_style'  => 'bool',
        ));

        $resolver->addAllowedValues(array(
            'style' => array(
                'secondary',
                'primary-box',
                'secondary-box',
                'success-box',
                'info-box',
                'warning-box',
                'danger-box',
                'default-wire',
                'primary-wire',
                'secondary-wire',
                'success-wire',
                'info-wire',
                'warning-wire',
                'danger-wire',
                'default-frame',
                'primary-frame',
                'secondary-frame',
                'success-frame',
                'info-frame',
                'warning-frame',
                'danger-frame',
                'default-lite',
                'primary-lite',
                'secondary-lite',
                'success-lite',
                'info-lite',
                'warning-lite',
                'danger-lite',
            ),
            'border_top_style' => array(
                'default',
                'primary',
                'secondary',
                'success',
                'info',
                'warning',
                'danger',
            ),
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getExtendedType()
    {
        return 'panel';
    }
}
