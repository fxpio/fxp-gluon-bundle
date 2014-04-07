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
use Sonatra\Bundle\BlockBundle\Block\Extension\Core\DataMapper\WrapperMapper;
use Sonatra\Bundle\BlockBundle\Block\Exception\InvalidConfigurationException;
use Sonatra\Bundle\BlockBundle\Block\Util\BlockUtil;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

/**
 * Panel Section Block Type.
 *
 * @author François Pluchino <francois.pluchino@sonatra.com>
 */
class PanelSectionType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildBlock(BlockBuilderInterface $builder, array $options)
    {
        $builder->setDataMapper(new WrapperMapper());

        if (!BlockUtil::isEmpty($options['label'])) {
            $builder->add('_heading', 'heading', array(
                'size'  => 6,
                'label' => $options['label'],
            ));
        }

        if ($options['collapsible']) {
            $builder->add('_panel_section_actions', 'panel_actions', array());
            $builder->get('_panel_section_actions')->add('_button_collapse', 'button', array(
                'label'       => '',
                'attr'        => array('class' => 'btn-panel-collapse'),
                'prepend'     => '<span class="caret"></span>'
            ));

        }
    }

    /**
     * {@inheritdoc}
     */
    public function addParent(BlockInterface $parent, BlockInterface $block, array $options)
    {
        if (!BlockUtil::isValidBlock('panel', $parent)) {
            $msg = 'The "panel_section" parent block (name: "%s") must be a "panel" block type';
            throw new InvalidConfigurationException(sprintf($msg, $block->getName()));
        }
    }

    /**
     * {@inheritdoc}
     */
    public function addChild(BlockInterface $child, BlockInterface $block, array $options)
    {
        if (BlockUtil::isValidBlock('heading', $child)) {
            if ($block->has('_heading')) {
                $msg = 'The panel section block "%s" has already panel section title. Removes the label option of the panel section block.';
                throw new InvalidConfigurationException(sprintf($msg, $block->getName()));
            }

        } elseif (BlockUtil::isValidBlock('panel_actions', $child)) {
            if ($block->getAttribute('already_actions')) {
                $actions = $block->get($block->getAttribute('already_actions'));

                foreach ($actions->all() as $name => $action) {
                    $child->add($action);
                }

                $block->remove($block->getAttribute('already_actions'));

            } else {
                $block->setAttribute('already_actions', $child->getName());
            }
        }
    }

    /**
     * {@inheritdoc}
     */
    public function buildView(BlockView $view, BlockInterface $block, array $options)
    {
        $view->vars = array_replace($view->vars, array(
            'rendered'    => $options['rendered'],
            'collapsible' => $options['collapsible'],
            'collapsed'   => $options['collapsed'],
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function finishView(BlockView $view, BlockInterface $block, array $options)
    {
        foreach ($view->children as $name => $child) {
            if (in_array('heading', $child->vars['block_prefixes'])) {
                $class = isset($child->vars['attr']['class']) ? $child->vars['attr']['class'] : '';
                $class .= ' panel-section-title';

                $child->vars['attr']['class'] = trim($class);

                $view->vars['panel_section_heading'] = $child;
                unset($view->children[$name]);

            } elseif (in_array('panel_actions', $child->vars['block_prefixes'])) {
                if (count($child->children) > 0 || isset($child->vars['panel_button_collapse'])) {
                    $view->vars['panel_section_actions'] = $child;
                }

                unset($view->children[$name]);
                break;
            }
        }
    }

    /**
     * {@inheritdoc}
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'inherit_data' => true,
            'compound'     => true,
            'rendered'     => true,
            'collapsible'  => false,
            'collapsed'    => false,
        ));

        $resolver->addAllowedTypes(array(
            'rendered'    => 'bool',
            'collapsible' => 'bool',
            'collapsed'   => 'bool',
        ));

        $resolver->addAllowedValues(array(

        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'panel_section';
    }
}
