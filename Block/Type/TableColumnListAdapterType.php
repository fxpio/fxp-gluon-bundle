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
use Sonatra\Bundle\BlockBundle\Block\Exception\InvalidConfigurationException;
use Sonatra\Bundle\BlockBundle\Block\Util\BlockUtil;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\OptionsResolver\Options;

/**
 * Table Column List Adapter Block Type.
 *
 * @author François Pluchino <francois.pluchino@sonatra.com>
 */
class TableColumnListAdapterType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function addParent(BlockInterface $parent, BlockInterface $block, array $options)
    {
        if (!BlockUtil::isValidBlock('table_list', $parent)) {
            $msg = 'The "table_column_list_adapter" parent block (name: "%s") must be a "table_list" block type';
            throw new InvalidConfigurationException(sprintf($msg, $block->getName()));
        }
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'resource' => null,
            'resource_block' => null,
            'formatter' => 'twig',
        ));

        $resolver->setAllowedTypes('resource', 'string');
        $resolver->setAllowedTypes('resource_block', array('null', 'string'));

        $resolver->setNormalizer('formatter_options', function (Options $options, $value) {
            $variables = isset($value['variables']) ? $value['variables'] : array();
            $value['variables'] = $variables;
            $value['resource'] = $options['resource'];
            $value['resource_block'] = $options['resource_block'];
            $value['empty_data'] = $options['empty_data'];
            $value['empty_message'] = $options['empty_message'];

            return $value;
        });

        $resolver->setNormalizer('index', function () {
            return null;
        });

        if (in_array('sortable', $resolver->getDefinedOptions())) {
            $resolver->setNormalizer('sortable', function () {
                return false;
            });
        }
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
        return 'table_column_list_adapter';
    }
}