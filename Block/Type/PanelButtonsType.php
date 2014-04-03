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
use Sonatra\Bundle\BlockBundle\Block\BlockFactoryInterface;
use Sonatra\Bundle\BlockBundle\Block\Exception\InvalidConfigurationException;
use Sonatra\Bundle\BlockBundle\Block\Util\BlockUtil;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

/**
 * Panel Buttons Block Type.
 *
 * @author François Pluchino <francois.pluchino@sonatra.com>
 */
class PanelButtonsType extends AbstractType
{
    /**
     * @var BlockFactoryInterface
     */
    protected $blockFactory;

    /**
     * Constructor.
     *
     * @param BlockFactoryInterface $blockFactory
     */
    public function __construct(BlockFactoryInterface $blockFactory)
    {
        $this->blockFactory = $blockFactory;
    }

    /**
     * {@inheritdoc}
     */
    public function addChild(BlockInterface $child, BlockInterface $block, array $options)
    {
        if (!BlockUtil::isValidBlock(array('button', 'nav_scrollable'), $child)) {
            $msg = 'The "panel_buttons" child block (name: "%s") must be a "button" block type';
            throw new InvalidConfigurationException(sprintf($msg, $child->getName()));
        }
    }

    /**
     * {@inheritdoc}
     */
    public function buildView(BlockView $view, BlockInterface $block, array $options)
    {
        $view->vars = array_replace($view->vars, array(
            'scrollable' => $options['scrollable'],
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function finishView(BlockView $view, BlockInterface $block, array $options)
    {
        $buttons = array();
        $scrollable = null;

        foreach ($view->children as $name => $child) {
            if (in_array('button', $child->vars['block_prefixes'])) {
                $buttons[] = $child;

            } elseif (null === $scrollable && in_array('nav_scrollable', $child->vars['block_prefixes'])) {
                $scrollable = $child;
            }
        }

        if (null !== $scrollable) {
            $scrollable->vars['attr']['data-class-nav'] = 'nav-btn-group';

        } elseif ($options['scrollable']) {
            var_dump('ici');
        }
    }

    /**
     * {@inheritdoc}
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'scrollable' => true,
        ));

        $resolver->setAllowedTypes(array(
            'scrollable' => 'bool',
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'panel_buttons';
    }
}
