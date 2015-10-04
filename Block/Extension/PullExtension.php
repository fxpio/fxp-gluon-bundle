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
use Symfony\Component\OptionsResolver\Exception\InvalidOptionsException;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Pull Block Extension.
 *
 * @author François Pluchino <francois.pluchino@sonatra.com>
 */
class PullExtension extends AbstractTypeExtension
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
    public function buildView(BlockView $view, BlockInterface $block, array $options)
    {
        $attr = &$view->vars['attr'];

        if (!array_key_exists('class', $attr)) {
            $attr['class'] = '';
        }

        if (is_array($options['pull'])) {
            foreach ($options['pull'] as $pull) {
                $attr['class'] = trim($attr['class'].' '.$options['pull_prefix'].'pull-'.$pull);
            }
        }
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'pull' => null,
            'pull_prefix' => null,
        ));

        $resolver->setAllowedTypes('pull', array('null', 'string', 'array'));
        $resolver->setAllowedTypes('pull_prefix', array('null', 'string'));

        $resolver->setNormalizer('pull', function (Options $options, $value) {
            if (is_string($value)) {
                $value = array($value);
            }

            if (is_array($value)) {
                foreach ($value as $pull) {
                    if (!in_array($pull, array('top', 'right'))) {
                        $msg = 'The option "pull" with value "%s" is invalid';
                        throw new InvalidOptionsException(sprintf($msg, $pull));
                    }
                }
            }

            return $value;
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
