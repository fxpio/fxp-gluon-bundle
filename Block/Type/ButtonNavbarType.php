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
use Sonatra\Bundle\BlockBundle\Block\Util\BlockUtil;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Button Navbar Block Type.
 *
 * @author François Pluchino <francois.pluchino@sonatra.com>
 */
class ButtonNavbarType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildView(BlockView $view, BlockInterface $block, array $options)
    {
        if ($options['home']) {
            BlockUtil::addAttributeClass($view, 'btn-navbar-home');
        }

        if ($options['sidebar_toggle']) {
            BlockUtil::addAttributeClass($view, 'btn-sidebar-toggle');
        }

        if ($options['sidebar_locked_toggle']) {
            BlockUtil::addAttributeClass($view, 'sidebar-locked-toggle');
        }
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'style' => 'navbar',
            'render_id' => function (Options $options) {
                return $options['sidebar_toggle'] || $options['sidebar_locked_toggle'];
            },
            'sidebar_toggle' => function (Options $options) {
                return $options['sidebar_locked_toggle'];
            },
            'sidebar_locked_toggle' => false,
            'home' => function (Options $options) {
                return $options['sidebar_toggle'] || $options['sidebar_locked_toggle'];
            }
        ));

        $resolver->setAllowedTypes('sidebar_toggle', 'bool');
        $resolver->setAllowedTypes('sidebar_locked_toggle', 'bool');
        $resolver->setAllowedTypes('render_id', 'bool');
        $resolver->setAllowedTypes('home', 'bool');
    }

    /**
     * {@inheritdoc}
     */
    public function getParent()
    {
        return 'button';
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'button_navbar';
    }
}
