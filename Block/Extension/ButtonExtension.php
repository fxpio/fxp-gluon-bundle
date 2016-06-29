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
use Sonatra\Bundle\BlockBundle\Block\Util\BlockUtil;
use Sonatra\Bundle\BootstrapBundle\Block\Type\ButtonType;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Button Block Extension.
 *
 * @author François Pluchino <francois.pluchino@sonatra.com>
 */
class ButtonExtension extends AbstractTypeExtension
{
    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'navbar_group' => null,
        ));

        $resolver->addAllowedValues('style', array('accent', 'navbar'));
        $resolver->addAllowedTypes('navbar_group', array('null', 'bool'));
    }

    /**
     * {@inheritdoc}
     */
    public function buildView(BlockView $view, BlockInterface $block, array $options)
    {
        $useGroup = null === $options['navbar_group'] || true === $options['navbar_group'];

        if ('navbar' === $options['style'] && $useGroup) {
            BlockUtil::addAttributeClass($view, 'btn-navbar-group', false, 'btn_group_attr');
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getExtendedType()
    {
        return ButtonType::class;
    }
}
