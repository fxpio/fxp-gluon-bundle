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
use Sonatra\Bundle\BlockBundle\Block\BlockView;
use Sonatra\Bundle\BlockBundle\Block\BlockInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

/**
 * Sidebar Block Type.
 *
 * @author François Pluchino <francois.pluchino@sonatra.com>
 */
class SidebarType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildView(BlockView $view, BlockInterface $block, array $options)
    {
        $attr = $view->vars['attr'];
        $attr['data-sidebar'] = $this->formatBoolean(true);
        $attr['data-force-toggle'] = is_bool($options['force_toggle']) ? $this->formatBoolean($options['force_toggle']) : $options['force_toggle'];

        if (null !== $options['open_on_hover']) {
            $attr['data-open-on-hover'] = $this->formatBoolean($options['open_on_hover']);
        }

        if (null !== $options['min_lock_width']) {
            $attr['data-min-lock-width'] = $options['min_lock_width'];
        }

        $view->vars = array_replace($view->vars, array(
            'style'         => $options['style'],
            'attr'          => $attr,
            'sticky_header' => $options['sticky_header'],
            'toggle_label'  => $options['toggle_label'],
            'opened'        => $options['opened'],
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'open_on_hover'  => null,
            'force_toggle'   => true,
            'min_lock_width' => null,
            'sticky_header'  => true,
            'style'          => 'default',
            'toggle_label'   => 'Sidebar toggle',
            'opened'         => false,
        ));

        $resolver->setAllowedTypes(array(
            'open_on_hover'  => array('null', 'bool'),
            'force_toggle'   => array('bool', 'string'),
            'min_lock_width' => array('null', 'int'),
            'sticky_header'  => array('null', 'bool'),
            'style'          => 'string',
            'toggle_label'   => 'string',
            'opened'         => array('bool', 'string'),
        ));

        $resolver->setAllowedValues(array(
            'force_toggle' => array(false, true, 'always'),
            'style'  => array('default', 'inverse'),
            'opened' => array(false, true, 'force'),
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'sidebar';
    }

    /**
     * Format the boolean to string html tag value.
     *
     * @param bool $value
     *
     * @return string
     */
    protected function formatBoolean($value)
    {
        if ($value) {
            return 'true';
        }

        return 'false';
    }
}
