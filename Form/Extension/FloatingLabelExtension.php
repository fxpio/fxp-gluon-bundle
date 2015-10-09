<?php

/*
 * This file is part of the Sonatra package.
 *
 * (c) François Pluchino <francois.pluchino@sonatra.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sonatra\Bundle\GluonBundle\Form\Extension;

use Sonatra\Bundle\BlockBundle\Block\Util\BlockUtil;
use Symfony\Component\Form\AbstractTypeExtension;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Floating Label Form Extension.
 *
 * @author François Pluchino <francois.pluchino@sonatra.com>
 */
class FloatingLabelExtension extends AbstractTypeExtension
{
    /**
     * @var string
     */
    protected $extendedType;

    /**
     * Constructor.
     *
     * @param string $extendedType The extended block type
     */
    public function __construct($extendedType)
    {
        $this->extendedType = $extendedType;
    }

    /**
     * {@inheritdoc}
     */
    public function buildView(FormView $view, FormInterface $block, array $options)
    {
        if ($options['floating_label']) {
            BlockUtil::addAttribute($view, 'data-floating-label', 'true');

            if (!BlockUtil::isEmpty($view->vars['value'])) {
                BlockUtil::addAttributeClass($view, 'has-floating-content');
            }
        }

        $view->vars = array_replace($view->vars, array(
            'floating_label' => $options['floating_label'],
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'floating_label' => false,
        ));

        $resolver->setAllowedTypes('floating_label', 'bool');

        $resolver->setNormalizer('floating_label', function (Options $options, $value) {
            return $options['layout'] === 'horizontal'
                ? false
                : $value;
        });
    }

    /**
     * {@inheritdoc}
     */
    public function getExtendedType()
    {
        return $this->extendedType;
    }
}
