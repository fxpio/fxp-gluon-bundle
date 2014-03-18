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
use Sonatra\Bundle\BlockBundle\Block\Util\BlockUtil;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
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
                'label'    => ' ',
                'data'     => $options['selected'],
                'attr'     => array(
                    'data-multi-selectable-all' => 'true',
                ),
            ));
        }
    }

    /**
     * {@inheritdoc}
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'multiple'  => false,
            'selected'  => false,
            'options'   => array(),
            'max_width' => 1,
            'formatter' => 'twig',
        ));

        $resolver->setNormalizers(array(
            'formatter_options' => function (Options $options, $value) {
                $variables = isset($value['variables']) ? $value['variables'] : array();
                $variables['multiple'] = $options['multiple'];
                $variables['options'] = $options['options'];
                $variables['options']['data'] = $options['selected'];
                $variables['options']['required'] = false;
                $variables['options']['label'] = ' ';
                $variables['max_width'] = $options['max_width'];

                $value['variables'] = $variables;
                $value['resource'] = $this->resource;
                $value['resource_block'] = 'table_column_select_content';
                $value['empty_data'] = $options['empty_data'];

                return $value;
            },
        ));

        $resolver->addAllowedTypes(array(
            'multiple' => 'bool',
            'selected' => 'bool',
            'options'  => 'array',
        ));
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
