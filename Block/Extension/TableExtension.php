<?php

/*
 * This file is part of the Sonatra package.
 *
 * (c) François Pluchino <francois.pluchino@sonatra.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sonatra\Bundle\GluonBundle\Block\Extension;

use Sonatra\Bundle\BlockBundle\Block\AbstractTypeExtension;
use Sonatra\Bundle\BlockBundle\Block\BlockInterface;
use Sonatra\Bundle\BlockBundle\Block\BlockView;
use Sonatra\Bundle\BlockBundle\Block\BlockFactoryInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

/**
 * Table Block Extension.
 *
 * @author François Pluchino <francois.pluchino@sonatra.com>
 */
class TableExtension extends AbstractTypeExtension
{
    /**
     * @var BlockFactoryInterface
     */
    protected $factory;

    /**
     * Constructor.
     *
     * @param BlockFactoryInterface $factory
     */
    public function __construct(BlockFactoryInterface $factory)
    {
        $this->factory = $factory;
    }

    /**
     * {@inheritdoc}
     */
    public function finishView(BlockView $view, BlockInterface $block, array $options)
    {
        if (isset($view->vars['header']->vars['header_columns'])) {
            $cols = &$view->vars['header']->vars['header_columns'];

            if (!isset($cols[0]) || (isset($cols[0]) && '_row_number' !== $cols[0]->vars['index'])) {
                $num = $this->factory->createNamed('_row_number', 'table_column_row_number');
                array_unshift($cols, $num->createView($view));
            }
        }
    }

    /**
     * {@inheritdoc}
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'responsive' => true,
            'row_number' => true,
        ));

        $resolver->addAllowedTypes(array(
            'row_number' => 'bool',
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
