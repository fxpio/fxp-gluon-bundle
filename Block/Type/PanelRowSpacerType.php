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
use Sonatra\Bundle\BlockBundle\Block\Exception\InvalidConfigurationException;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

/**
 * Panel Row Spacer Block Type.
 *
 * @author François Pluchino <francois.pluchino@sonatra.com>
 */
class PanelRowSpacerType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function addChild(BlockInterface $child, BlockInterface $block, array $options)
    {
        $msg = 'The "panel_row_spacer" block (name: "%s") can not have children';
        throw new InvalidConfigurationException(sprintf($msg, $child->getName()));
    }

    /**
     * {@inheritdoc}
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'hidden_if_empty' => false,
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getParent()
    {
        return 'panel_row';
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'panel_row_spacer';
    }
}
