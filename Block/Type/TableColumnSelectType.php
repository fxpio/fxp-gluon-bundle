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
use Sonatra\Bundle\BlockBundle\Block\BlockView;
use Sonatra\Bundle\BlockBundle\Block\BlockInterface;
use Sonatra\Bundle\BlockBundle\Block\Util\BlockUtil;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\OptionsResolver\Options;

/**
 * Table Column Select Block Type.
 *
 * @author François Pluchino <francois.pluchino@sonatra.com>
 */
class TableColumnSelectType extends AbstractType
{
    /**
     * @var string
     */
    protected $resource;

    /**
     * Constructor.
     *
     * @param string $resource
     */
    public function __construct($resource)
    {
        $this->resource = $resource;
    }

    /**
     * {@inheritdoc}
     */
    public function buildBlock(BlockBuilderInterface $builder, array $options)
    {
        if ($options['multiple']) {
            $builder->add(BlockUtil::createUniqueName(), 'form_checkbox', array(
                'required' => false,
                'label' => ' ',
                'data' => $options['selected'],
                'style' => $options['style'],
                'attr' => array(
                    'data-multi-selectable-all' => 'true',
                ),
            ));
        }
    }

    /**
     * {@inheritdoc}
     */
    public function buildView(BlockView $view, BlockInterface $block, array $options)
    {
        if (null !== $view->parent && in_array('table', $view->parent->vars['block_prefixes'])) {
            $view->parent->vars['attr']['data-table-select'] = 'true';
            $view->parent->vars['attr']['data-col-selectable'] = $block->getName();
            $view->parent->vars['column_selection_stype'] = $options['style'];

            if (null !== $options['max_selection']) {
                $view->parent->vars['attr']['data-max-selection'] = $options['max_selection'];
            }
        }
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'multiple' => false,
            'selected' => false,
            'max_selection' => null,
            'style' => 'accent',
            'options' => array(),
            'max_width' => 34,
            'width' => 34,
            'formatter' => 'twig',
            'footable' => array(
                'ignore' => true,
            ),
        ));

        $resolver->addAllowedTypes('multiple', 'bool');
        $resolver->addAllowedTypes('selected', 'bool');
        $resolver->addAllowedTypes('max_selection', array('null', 'int'));
        $resolver->addAllowedTypes('style', array('null', 'string'));
        $resolver->addAllowedTypes('options', 'array');

        $resolver->setNormalizer('formatter_options', function (Options $options, $value) {
            $variables = isset($value['variables']) ? $value['variables'] : array();
            $variables['multiple'] = $options['multiple'];
            $variables['options'] = $options['options'];
            $variables['options']['data'] = $options['selected'];
            $variables['options']['required'] = false;
            $variables['options']['label'] = ' ';
            $variables['options']['style'] = $options['style'];
            $variables['max_width'] = $options['max_width'];

            $value['variables'] = $variables;
            $value['resource'] = $this->resource;
            $value['resource_block'] = 'table_column_select_content';
            $value['empty_data'] = $options['empty_data'];
            $value['empty_message'] = $options['empty_message'];

            return $value;
        });
    }

    /**
     * {@inheritdoc}
     */
    public function getParent()
    {
        return 'table_column';
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'table_column_select';
    }
}
