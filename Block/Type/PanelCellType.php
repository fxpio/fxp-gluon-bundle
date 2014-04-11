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
use Sonatra\Bundle\BlockBundle\Block\BlockView;
use Sonatra\Bundle\BlockBundle\Block\Exception\InvalidConfigurationException;
use Sonatra\Bundle\BlockBundle\Block\Util\BlockUtil;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\OptionsResolver\Options;

/**
 * Panel Cell Block Type.
 *
 * @author François Pluchino <francois.pluchino@sonatra.com>
 */
class PanelCellType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildBlock(BlockBuilderInterface $builder, array $options)
    {
        if (null !== $options['type']) {
            $cOpts = array_replace($options['options'], array(
                'wrapped'       => false,
                'mapped'        => true,
                'property_path' => null !== $options['property_path'] ? $options['property_path'] : null,
            ));

            $builder->add($builder->getName(), $options['type'], $cOpts);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function addParent(BlockInterface $parent, BlockInterface $block, array $options)
    {
        if (!BlockUtil::isValidBlock(array('panel_section', 'panel_row'), $parent)) {
            $msg = 'The "panel_cell" parent block (name: "%s") must be a "panel_section" block type';
            throw new InvalidConfigurationException(sprintf($msg, $block->getName()));
        }
    }

    /**
     * {@inheritdoc}
     */
    public function buildView(BlockView $view, BlockInterface $block, array $options)
    {
        $labelAttr = $view->vars['label_attr'];

        if (null !== $options['label_style']) {
            $class = isset($labelAttr['class']) ? $labelAttr['class'] : '';
            $class .= ' block-label-' . $options['label_style'];

            $labelAttr['class'] = trim($class);
        }

        $view->vars = array_replace($view->vars, array(
            'control_attr'       => $options['control_attr'],
            'layout'             => 'horizontal',
            'layout_col_size'    => $options['layout_size'],
            'layout_col_label'   => $options['layout_label'],
            'layout_col_control' => $options['layout_control'],
            'label_style'        => $options['label_style'],
            'label_attr'         => $labelAttr,
            'rendered'           => $options['rendered'],
            'hidden'             => $options['hidden'],
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function finishView(BlockView $view, BlockInterface $block, array $options)
    {
        foreach ($view->children as $name => $child) {
            if (in_array('heading', $child->vars['block_prefixes'])) {

            }
        }

        if (!is_scalar($view->vars['value'])) {
            $view->vars['value'] = '';
        }
    }

    /**
     * {@inheritdoc}
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'inherit_data'   => true,
            'type'           => null,
            'options'        => array(),
            'control_attr'   => array(),
            'layout_size'    => 'lg',
            'layout_label'   => 2,
            'layout_control' => 10,
            'label_style'    => null,
            'rendered'       => true,
            'hidden'         => false,
        ));

        $resolver->addAllowedTypes(array(
            'type'           => array('null', 'string', 'Sonatra\Bundle\BlockBundle\Block\BlockTypeInterface'),
            'options'        => 'array',
            'control_attr'   => 'array',
            'layout_size'    => 'string',
            'layout_label'   => 'int',
            'layout_control' => 'int',
            'label_style'    => array('null', 'string'),
            'rendered'       => 'bool',
            'hidden'         => 'bool',
        ));

        $resolver->addAllowedValues(array(
            'layout_size' => array('xs', 'sm', 'md', 'lg'),
            'label_style' => array(
                'default',
                'primary',
                'secondary',
                'success',
                'info',
                'warning',
                'danger',
            ),
        ));

        $resolver->setNormalizers(array(
            'layout_label'   => function (Options $options, $value) {
                if ($value < 1) {
                    $msg = 'The "layout_label" option must be greater than 1, given %s.';
                    throw new InvalidConfigurationException(sprintf($msg, $value));
                }

                return $value;
            },
            'layout_control' => function (Options $options, $value) {
                if ($value < 1) {
                    $msg = 'The "layout_control" option must be greater than 1, given %s';
                    throw new InvalidConfigurationException(sprintf($msg, $value));
                }

                return $value;
            },
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'panel_cell';
    }
}
