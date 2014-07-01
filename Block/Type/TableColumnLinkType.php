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
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\OptionsResolver\Options;

/**
 * Table Column Link Block Type.
 *
 * @author François Pluchino <francois.pluchino@sonatra.com>
 */
class TableColumnLinkType extends AbstractType
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
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'link_options'   => array(),
            'route_name'     => null,
            'route_options'  => array(),
            'route_absolute' => false,
            'formatter'      => 'twig',
        ));

        $resolver->setNormalizers(array(
            'formatter_options' => function (Options $options, $value) {
                $variables = isset($value['variables']) ? $value['variables'] : array();
                $variables['link_options'] = $options['link_options'];
                $variables['route_name'] = $options['route_name'];
                $variables['route_options'] = $options['route_options'];
                $variables['route_absolute'] = $options['route_absolute'];

                $value['variables'] = $variables;
                $value['resource'] = $this->resource;
                $value['resource_block'] = 'table_column_link_content';
                $value['empty_data'] = $options['empty_data'];

                return $value;
            },
        ));

        $resolver->addAllowedTypes(array(
            'link_options'   => 'array',
            'route_name'     => array('null', 'string'),
            'route_options'  => 'array',
            'route_absolute' => 'bool',
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
        return 'table_column_link';
    }
}
