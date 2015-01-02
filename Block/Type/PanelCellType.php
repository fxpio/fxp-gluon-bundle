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

        if (null !== $options['help']) {
            $hOpts = array_replace($options['options'], array(
                'label'   => '?',
                'style'   => 'info',
                'size'    => 'xs',
                'attr'    => array('class' => 'panel-cell-help'),
                'popover' => $options['help'],
            ));

            $builder->add($builder->getName() . '_help', 'button', $hOpts);
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
        $class = isset($labelAttr['class']) ? $labelAttr['class'] : '';
        $class = trim('control-label ' . $class);

        if (null !== $options['label_style']) {
            $class .= ' control-label-' . $options['label_style'];
        }

        $labelAttr['class'] = trim($class);

        $view->vars = array_replace($view->vars, array(
            'control_attr'     => $options['control_attr'],
            'layout_col_size'  => $options['layout_size'],
            'layout_col_width' => $options['layout'],
            'layout_col_max'   => $options['layout_max'],
            'label_style'      => $options['label_style'],
            'label_attr'       => $labelAttr,
            'rendered'         => $options['rendered'],
            'hidden'           => $options['hidden'],
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function finishView(BlockView $view, BlockInterface $block, array $options)
    {
        foreach ($view->children as $name => $child) {
            if (in_array('button', $child->vars['block_prefixes'])) {
                $view->vars['button_help'] = $child;
                unset($view->children[$name]);
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
            'layout_size'    => 'sm',
            'layout'         => 12,
            'layout_max'     => 12,
            'label_style'    => null,
            'rendered'       => true,
            'hidden'         => false,
            'help'           => null,
        ));

        $resolver->addAllowedTypes(array(
            'type'           => array('null', 'string', 'Sonatra\Bundle\BlockBundle\Block\BlockTypeInterface'),
            'options'        => 'array',
            'control_attr'   => 'array',
            'layout_size'    => 'string',
            'layout'         => 'int',
            'layout_max'     => 'int',
            'label_style'    => array('null', 'string'),
            'rendered'       => 'bool',
            'hidden'         => 'bool',
            'help'           => array('null', 'string', 'array'),
        ));

        $resolver->addAllowedValues(array(
            'layout_size' => array('sm', 'md', 'lg'),
            'label_style' => array(
                null,
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
            'layout' => function (Options $options, $value) {
                $value = max($value, 1);
                $value = min($value, $options['layout_max']);

                return $value;
            },
            'help'   => function (Options $options, $value) {
                    if (null === $value) {
                        return $value;

                    } elseif (is_string($value)) {
                        $value = array(
                            'content' => $value,
                        );
                    }

                    $value = array_replace(array(
                        'html'      => true,
                        'placement' => 'auto top',
                    ), $value);

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
