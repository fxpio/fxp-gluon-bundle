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
use Sonatra\Bundle\BlockBundle\Block\Exception\InvalidConfigurationException;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

/**
 * Footable Column Block Extension.
 *
 * @author François Pluchino <francois.pluchino@sonatra.com>
 */
class FootableColumnExtension extends AbstractTypeExtension
{
    /**
     * {@inheritdoc}
     */
    public function buildView(BlockView $view, BlockInterface $block, array $options)
    {
        if (null !== $view->parent && in_array('table', $view->parent->vars['block_prefixes']) && isset($view->parent->vars['footable_options'])) {
            $labelAttr = $view->vars['label_attr'];
            $attr = $view->vars['attr'];

            if (isset($attr['class'])) {
                $labelAttr['data-class'] = $attr['class'];
            }

            if (null !== $options['footable']['hide'] && !empty($options['footable']['hide'])) {
                $labelAttr['data-hide'] = implode(',', (array) $options['footable']['hide']);
            }

            if (null !== $options['footable']['ignore']) {
                $labelAttr['data-ignore'] = $options['footable']['ignore'] ? 'true' : 'false';
            }

            if (null !== $options['footable']['toggle']) {
                $labelAttr['data-toggle'] = $options['footable']['toggle'] ? 'true' : 'false';
            }

            if (null !== $options['footable']['name']) {
                $labelAttr['data-name'] = $options['footable']['name'];
            }

            if (null !== $options['footable']['type']) {
                $labelAttr['data-type'] = $options['footable']['type'];
            }

            $view->vars = array_replace($view->vars, array(
                'label_attr' => $labelAttr,
            ));
        }
    }

    /**
     * {@inheritdoc}
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'footable' => array(),
        ));

        $resolver->addAllowedTypes(array(
            'footable' => 'array',
        ));

        $resolver->setNormalizers(array(
            'footable' => function (Options $options, $value) {
                $footableResolver = new OptionsResolver();

                $footableResolver->setDefaults(array(
                    'hide'   => null,
                    'ignore' => null,
                    'toggle' => null,
                    'name'   => null,
                    'type'   => null,
                ));

                $footableResolver->setAllowedTypes(array(
                    'hide'   => array('null', 'string', 'array'),
                    'ignore' => array('null', 'bool'),
                    'toggle' => array('null', 'bool'),
                    'name'   => array('null', 'string'),
                    'type'   => array('null', 'string'),
                ));

                $footableResolver->addAllowedValues(array(
                    'type' => array(null, 'alpha', 'numeric'),
                ));

                $footableResolver->setNormalizers(array(
                    'hide' => function (Options $options, $value) {
                        $allowed = array('phone', 'tablet', 'default', 'all');
                        $value = (array) $value;

                        foreach ($value as $type) {
                            if (!in_array($type, $allowed)) {
                                $msg = 'The option "hide" has the value "%s", but is expected to be one of "%s"';
                                throw new InvalidConfigurationException(sprintf($msg, implode('", "', $value), implode('", "', $allowed)));
                            }
                        }

                        return $value;
                    },
                ));

                return $footableResolver->resolve($value);
            },
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getExtendedType()
    {
        return 'table_column';
    }
}
