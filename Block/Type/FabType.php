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
use Sonatra\Bundle\BlockBundle\Block\BlockInterface;
use Sonatra\Bundle\BlockBundle\Block\BlockView;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * @author François Pluchino <francois.pluchino@sonatra.com>
 */
class FabType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildView(BlockView $view, BlockInterface $block, array $options)
    {
        $view->vars = array_replace($view->vars, array(
            'absolute_position' => str_replace('_', '-', $options['absolute_position']),
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function finishView(BlockView $view, BlockInterface $block, array $options)
    {
        if (isset($view->vars['dropdown']) && in_array($options['absolute_position'], array('top_right', 'bottom_right'))) {
            /* @var BlockView $dropView */
            $dropView = $view->vars['dropdown'];
            $dropAttr = &$dropView->vars['attr'];
            $dropAttr['class'] = array_key_exists('class', $dropAttr) ? $dropAttr['class'] : '';
            $dropAttr['class'] = trim($dropAttr['class'].' fab-pull-right');
        }
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'absolute_position' => null,
        ));

        $resolver->addAllowedTypes('absolute_position', array('null', 'string'));

        $resolver->addAllowedValues('absolute_position', array(
            null, 'top_left', 'top_right', 'bottom_left', 'bottom_right'
        ));
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
        return 'fab';
    }
}
