<?php

/*
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
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\OptionsResolver\Options;

/**
 * Navbar Scrollable Block Type.
 *
 * @author François Pluchino <francois.pluchino@sonatra.com>
 */
class NavbarScrollableType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildView(BlockView $view, BlockInterface $block, array $options)
    {
        if (null !== $view->parent && in_array('navbar', $view->parent->vars['block_prefixes'])) {
            $class = isset($view->parent->vars['attr']['class']) ? $view->parent->vars['attr']['class'] : '';
            $class .= ' has-navbar-scrollable';

            $view->parent->vars['attr']['class'] = trim($class);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'navbar_scrollable';
    }
}
