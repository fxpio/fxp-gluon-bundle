<?php

/**
 * This file is part of the Sonatra package.
 *
 * (c) François Pluchino <francois.pluchino@sonatra.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sonatra\Bundle\GluonBundle\Block\Extension;

use Sonatra\Bundle\BlockBundle\Block\AbstractTypeExtension;
use Sonatra\Bundle\BlockBundle\Block\BlockBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\OptionsResolver\Options;

/**
 * Table Block Extension.
 *
 * @author François Pluchino <francois.pluchino@sonatra.com>
 */
class TableExtension extends AbstractTypeExtension
{
    /**
     * {@inheritdoc}
     */
    public function buildBlock(BlockBuilderInterface $builder, array $options)
    {
        if ($options['row_number']) {
            $builder->add('_row_number', 'table_column_row_number');
        }

        if ($options['selectable']) {
            $builder->add('_selectable', 'table_column_select', array(
                'multiple' => $options['multi_selectable'],
                'selected' => $options['selected'],
            ));
        }
    }

    /**
     * {@inheritdoc}
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'render_id'        => true,
            'responsive'       => true,
            'row_number'       => true,
            'selectable'       => false,
            'multi_selectable' => false,
            'selected'         => false,
        ));

        $resolver->addAllowedTypes(array(
            'row_number'       => 'bool',
            'selectable'       => 'bool',
            'multi_selectable' => 'bool',
            'selected'         => 'bool',
        ));

        $resolver->setNormalizers(array(
            'selectable' => function (Options $options, $value) {
                if ($options['multi_selectable']) {
                    return true;
                }

                return $value;
            },
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getExtendedType()
    {
        return 'table';
    }
}
