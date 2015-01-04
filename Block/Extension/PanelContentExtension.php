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
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

/**
 * Panel Content Block Extension.
 *
 * @author François Pluchino <francois.pluchino@sonatra.com>
 */
class PanelContentExtension extends AbstractTypeExtension
{
    /**
     * @var string
     */
    protected $type;

    /**
     * Constructor.
     *
     * @param string $type
     */
    public function __construct($type)
    {
        $this->type = $type;
    }

    /**
     * {@inheritdoc}
     */
    public function buildView(BlockView $view, BlockInterface $block, array $options)
    {
        $view->vars = array_replace($view->vars, array(
            'style' => $options['style'],
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'style' => null,
        ));

        $resolver->addAllowedTypes(array(
            'style' => array('null', 'string'),
        ));

        $resolver->addAllowedValues(array(
            'style' => array(
                null,
                'primary-box',
                'secondary-box',
                'success-box',
                'info-box',
                'warning-box',
                'danger-box',
            ),
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getExtendedType()
    {
        return 'panel_'.$this->type;
    }
}
