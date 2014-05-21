<?php

/**
 * This file is part of the Sonatra package.
 *
 * (c) François Pluchino <francois.pluchino@sonatra.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sonatra\Bundle\GluonBundle\Block\Type;

use Sonatra\Bundle\BlockBundle\Block\AbstractType;
use Sonatra\Bundle\BlockBundle\Block\BlockView;
use Sonatra\Bundle\BlockBundle\Block\BlockInterface;

/**
 * Nav Scrollable Block Type.
 *
 * @author François Pluchino <francois.pluchino@sonatra.com>
 */
class NavScrollableType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildView(BlockView $view, BlockInterface $block, array $options)
    {
        if (null !== $view->parent && in_array('navbar', $view->parent->vars['block_prefixes'])) {
            $class = isset($view->parent->vars['attr']['class']) ? $view->parent->vars['attr']['class'] : '';
            $class .= ' has-nav-scrollable';

            $view->parent->vars['attr']['class'] = trim($class);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function finishView(BlockView $view, BlockInterface $block, array $options)
    {
        $class = isset($view->vars['attr']['class']) ? $view->vars['attr']['class'] : '';

        foreach ($view->children as $child) {
            if (in_array('nav', $child->vars['block_prefixes'])) {
                $class = trim('is-nav-' . $child->vars['style'] . ' ' . $class);
            }
        }

        if ('' !== $class) {
            $view->vars['attr']['class'] = $class;
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'nav_scrollable';
    }
}
