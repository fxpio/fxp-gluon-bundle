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
use Sonatra\Bundle\BlockBundle\Block\Util\BlockUtil;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\OptionsResolver\Options;

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
            'collapsible'      => false,
            'collapsed'        => false,
        ));

        $resolver->addAllowedTypes(array(
            'border_top_style' => array('null', 'string'),
            'collapsible'      => 'bool',
            'collapsed'        => 'bool',
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
