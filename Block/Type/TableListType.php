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
use Sonatra\Bundle\BlockBundle\Block\BlockFactoryInterface;
use Sonatra\Bundle\BlockBundle\Block\BlockInterface;
use Sonatra\Bundle\BlockBundle\Block\BlockView;
use Sonatra\Bundle\BlockBundle\Block\Exception\InvalidConfigurationException;
use Sonatra\Bundle\BlockBundle\Block\Util\BlockUtil;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * @author François Pluchino <francois.pluchino@sonatra.com>
 */
class TableListType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildBlock(BlockBuilderInterface $builder, array $options)
    {
        $builder->setAttribute('block_factory', $builder->getBlockFactory());
    }

    /**
     * {@inheritdoc}
     */
    public function addChild(BlockInterface $child, BlockInterface $block, array $options)
    {
        if (BlockUtil::isValidBlock(array('table_column_list_sort'), $child)) {
            $block->getData()->addColumn($child);
        } elseif (!BlockUtil::isValidBlock(array('table_header', 'table_column_select', 'table_pager', 'table_column_list_adapter'), $child)) {
            $msg = 'The "table_list" child block (name: "%s") must be a "table_column_list_adapter" or "table_column_list_sort" block type ("%s" type given)';
            throw new InvalidConfigurationException(sprintf($msg, $child->getName(), $child->getConfig()->getType()->getName()));
        }
    }

    /**
     * {@inheritdoc}
     */
    public function removeChild(BlockInterface $child, BlockInterface $block, array $options)
    {
        if (!BlockUtil::isValidBlock(array('table_column_list_sort'), $child)) {
            $block->getData()->removeColumn($child->getName());
        }
    }

    /**
     * {@inheritdoc}
     */
    public function buildView(BlockView $view, BlockInterface $block, array $options)
    {
        BlockUtil::addAttributeClass($view, 'table-list');

        if ($options['multi_selectable']) {
            BlockUtil::addAttributeClass($view, 'table-list-multiple');
        }
    }

    /**
     * {@inheritdoc}
     */
    public function finishView(BlockView $view, BlockInterface $block, array $options)
    {
        if (!isset($view->vars['pager'])) {
            return;
        }

        /* @var BlockView[] $sortColumns */
        $sortColumns = array();

        foreach ($view->children as $name => $child) {
            if (in_array('table_column_list_sort', $child->vars['block_prefixes'])) {
                $sortColumns[] = $child;
                unset($view->children[$name]);
            }
        }

        if (count($sortColumns) > 0) {
            /* @var BlockFactoryInterface $factory */
            $factory = $block->getConfig()->getAttribute('block_factory');
            $sortDropdown = $factory->create('dropdown', null, array('ripple' => true, 'wrapper' => false, 'attr' => array('class' => 'table-pager-list-sort-menu')));

            foreach ($sortColumns as $sortColumn) {
                $colOptions = array(
                    'label' => $sortColumn->vars['label'],
                    'translation_domain' => $sortColumn->vars['translation_domain'],
                    'link_attr' => array_replace($sortColumn->vars['label_attr'], array(
                        'data-col-name' => $sortColumn->vars['name'],
                    )),
                );
                $sortDropdown->add($sortColumn->vars['name'], 'dropdown_item', $colOptions);
            }

            $view->vars['pager']->vars['sort_columns'] = $sortDropdown;
        }
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        if (in_array('footable', $resolver->getDefinedOptions())) {
            $resolver->setNormalizer('footable', function () {
                return array(
                    'enabled' => false,
                );
            });
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getParent()
    {
        return 'table';
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'table_list';
    }
}
