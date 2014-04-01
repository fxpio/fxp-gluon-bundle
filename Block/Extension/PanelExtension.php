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
    public function buildView(BlockView $view, BlockInterface $block, array $options)
    {
        $view->vars = array_replace($view->vars, array(
            'border_top_style' => $options['border_top_style'],
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
        ));

        $resolver->addAllowedTypes(array(
            'border_top_style' => array('null', 'string'),
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
