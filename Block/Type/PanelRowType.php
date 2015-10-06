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
use Sonatra\Bundle\BlockBundle\Block\Exception\InvalidConfigurationException;
use Sonatra\Bundle\BlockBundle\Block\Util\BlockUtil;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\OptionsResolver\Options;

/**
 * Panel Row Block Type.
 *
 * @author François Pluchino <francois.pluchino@sonatra.com>
 */
class PanelRowType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function addParent(BlockInterface $parent, BlockInterface $block, array $options)
    {
        if (!BlockUtil::isValidBlock('panel_section', $parent)) {
            $msg = 'The "panel_row" parent block (name: "%s") must be a "panel_section" block type';
            throw new InvalidConfigurationException(sprintf($msg, $block->getName()));
        }
    }

    /**
     * {@inheritdoc}
     */
    public function addChild(BlockInterface $child, BlockInterface $block, array $options)
    {
        if (!BlockUtil::isValidBlock('panel_cell', $child)) {
            $msg = 'The "panel_row" child block (name: "%s") must be a "panel_cell" block type';
            throw new InvalidConfigurationException(sprintf($msg, $child->getName()));
        }

        if (count($block->all()) >= $block->getOption('column')) {
            $msg = 'The "panel_row" block (name: "%s") can only have %s column(s)';
            throw new InvalidConfigurationException(sprintf($msg, $block->getName(), $options['column']));
        }

        $cOptions = array(
            'layout' => $block->getOption('layout_max') / $block->getOption('column'),
        );

        if (null !== $block->getOption('layout_size')) {
            $cOptions['layout_size'] = $block->getOption('layout_size');
        }

        if (null !== $block->getOption('cell_label_style') && null === $child->getOption('label_style')) {
            $cOptions['label_style'] = $block->getOption('cell_label_style');
        }

        $child->setOptions($cOptions);
    }

    /**
     * {@inheritdoc}
     */
    public function buildView(BlockView $view, BlockInterface $block, array $options)
    {
        $view->vars = array_replace($view->vars, array(
            'rendered' => $options['rendered'],
            'hidden_if_empty' => $options['hidden_if_empty'],
            'column' => $options['column'],
            'layout_max' => $options['layout_max'],
            'layout_size' => $options['layout_size'],
            'cell_label_style' => $options['cell_label_style'],
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function finishView(BlockView $view, BlockInterface $block, array $options)
    {
        $hasCell = 0;
        $hasRenderedCell = false;

        foreach ($view->children as $child) {
            if (in_array('panel_cell', $child->vars['block_prefixes'])) {
                ++$hasCell;

                if (!$hasRenderedCell && !$child->vars['hidden'] && $child->vars['rendered']) {
                    $hasRenderedCell = true;
                }
            }
        }

        if (!is_scalar($view->vars['value'])) {
            $view->vars['value'] = '';
        }

        if ($view->vars['hidden_if_empty'] && BlockUtil::isEmpty($view->vars['value'])
            && $hasCell === count($view->children)
            && !$hasRenderedCell) {
            $view->vars['rendered'] = false;
        }
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'inherit_data' => true,
            'rendered' => true,
            'hidden_if_empty' => true,
            'column' => 1,
            'layout_max' => 12,
            'layout_size' => null,
            'cell_label_style' => null,
        ));

        $resolver->addAllowedTypes('rendered', 'bool');
        $resolver->addAllowedTypes('hidden_if_empty', 'bool');
        $resolver->addAllowedTypes('column', 'int');
        $resolver->addAllowedTypes('layout_max', 'int');
        $resolver->addAllowedTypes('layout_size', array('null', 'string'));
        $resolver->addAllowedTypes('cell_label_style', array('null', 'string'));

        $resolver->setNormalizer('column', function (Options $options, $value) {
            $colNumMax = $options['layout_max'];

            if ($value > $colNumMax) {
                throw new InvalidConfigurationException('The "column" option must be lower of "layout_max" option');
            }

            if ($colNumMax % $value !== 0) {
                $msg = 'Result of %s is not an integer. The Panel row\'s column must be an integer after division per %s.';

                throw new InvalidConfigurationException(sprintf($msg, $colNumMax / $value, $colNumMax));
            }

            return $value;
        });
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'panel_row';
    }
}
