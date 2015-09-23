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
use Sonatra\Bundle\BlockBundle\Block\BlockView;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\PropertyAccess\PropertyAccess;
use Symfony\Component\PropertyAccess\PropertyAccessor;
use Symfony\Component\PropertyAccess\PropertyAccessorInterface;
use Symfony\Component\Routing\RouterInterface;

/**
 * @author François Pluchino <francois.pluchino@sonatra.com>
 */
class LookupType extends AbstractType
{
    /**
     * @var RouterInterface
     */
    private $router;

    /**
     * @var PropertyAccessor
     */
    private $propertyAccessor;

    /**
     * Constructor.
     *
     * @param RouterInterface           $router           The rooter
     * @param PropertyAccessorInterface $propertyAccessor The property accessor
     */
    public function __construct(RouterInterface $router, PropertyAccessorInterface $propertyAccessor = null)
    {
        $this->router = $router;
        $this->propertyAccessor = $propertyAccessor ?: PropertyAccess::createPropertyAccessor();
    }

    /**
     * {@inheritdoc}
     */
    public function buildView(BlockView $view, BlockInterface $block, array $options)
    {
        if (null === $block->getData()) {
            return;
        }

        $routeParams = $options['route_parameters'];
        $value = null !== $options['property_path']
            ? $this->propertyAccessor->getValue($block->getData(), $options['property_path'])
            : null;

        foreach ($routeParams as $key => $params) {
            if (0 === strpos($params, '{{')) {
                $path = trim(trim(trim($params, '{{'), '}}'));
                $routeParams[$key] = $this->propertyAccessor->getValue($block->getData(), $path);
            }
        }

        $view->vars = array_replace($view->vars, array(
            'value' => $value,
            'attr' => array_merge($view->vars['attr'], array(
                'href' => $this->router->generate($options['route_name'], $routeParams, RouterInterface::ABSOLUTE_URL),
            )),
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'route_name' => null,
            'route_parameters' => array(),
        ));

        $resolver->setRequired('route_name');
        $resolver->setRequired('route_parameters');

        $resolver->addAllowedTypes('route_name', 'string');
    }

    /**
     * {@inheritdoc}
     */
    public function getParent()
    {
        return 'link';
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'lookup';
    }
}
