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
use Sonatra\Bundle\BlockBundle\Block\BlockBuilderInterface;
use Sonatra\Bundle\BlockBundle\Block\BlockInterface;
use Sonatra\Bundle\BlockBundle\Block\Exception\InvalidConfigurationException;
use Sonatra\Bundle\BlockBundle\Block\Util\BlockUtil;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

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
        if (BlockUtil::isValidBlock('panel', $child)) {
            $panels = $block->getAttribute('panels', array());

            $block->remove($child->getName());
            $panels[$child->getName()] = $child;

            $block->setAttribute('panels', $panels);

        } elseif (!BlockUtil::isValidBlock('panel_header', $child)) {
            throw new InvalidConfigurationException('Only "panel" type child must be added into the panel list type');
        }
    }

    /**
     * {@inheritdoc}
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            //'inherit_data'  => true,
            'selected'      => null,
            //'route_name'    => null,
            //'route_options' => array(),
            //'route_list_id' => 'list',
            'groups'        => array(),
        ));

        $resolver->setAllowedTypes(array(
            //'route_name'    => 'string',
            //'route_options' => 'array',
            'groups'        => 'array',
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getParent()
    {
        return 'panel';
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'panel_list';
    }
}
