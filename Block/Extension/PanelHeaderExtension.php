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

/**
 * Panel Header Block Extension.
 *
 * @author François Pluchino <francois.pluchino@sonatra.com>
 */
class PanelHeaderExtension extends AbstractTypeExtension
{
    /**
     * {@inheritdoc}
     */
    public function finishView(BlockView $view, BlockInterface $block, array $options)
    {
        foreach ($view->children as $name => $child) {
            if (in_array('panel_actions', $child->vars['block_prefixes'])) {
                $view->vars['panel_actions'] = $child;
                unset($view->children[$name]);
                break;
            }
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getExtendedType()
    {
        return 'panel_header';
    }
}
