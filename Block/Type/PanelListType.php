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
use Sonatra\Bundle\BlockBundle\Block\BlockBuilderInterface;
use Sonatra\Bundle\BlockBundle\Block\BlockInterface;
use Sonatra\Bundle\BlockBundle\Block\Exception\InvalidConfigurationException;
use Sonatra\Bundle\BlockBundle\Block\Util\BlockUtil;
use Sonatra\Bundle\BootstrapBundle\Block\Type\PanelHeaderType;
use Sonatra\Bundle\BootstrapBundle\Block\Type\PanelType;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Panel List Block Type.
 *
 * @author François Pluchino <francois.pluchino@sonatra.com>
 */
class PanelListType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildBlock(BlockBuilderInterface $builder, array $options)
    {
    }

    /**
     * {@inheritdoc}
     */
    public function addChild(BlockInterface $child, BlockInterface $block, array $options)
    {
        if (BlockUtil::isValidBlock(PanelType::class, $child)) {
            $panels = $block->getAttribute('panels', array());

            $block->remove($child->getName());
            $panels[$child->getName()] = $child;

            $block->setAttribute('panels', $panels);
        } elseif (!BlockUtil::isValidBlock(PanelHeaderType::class, $child)) {
            throw new InvalidConfigurationException('Only "panel" type child must be added into the panel list type');
        }
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'selected' => null,
            'groups' => array(),
        ));

        $resolver->setAllowedTypes('groups', 'array');
    }

    /**
     * {@inheritdoc}
     */
    public function getParent()
    {
        return PanelType::class;
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'panel_list';
    }
}
