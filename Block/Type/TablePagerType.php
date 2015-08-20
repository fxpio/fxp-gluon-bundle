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

use Sonatra\Bundle\AjaxBundle\AjaxEvents;
use Sonatra\Bundle\BlockBundle\Block\AbstractType;
use Sonatra\Bundle\BlockBundle\Block\BlockView;
use Sonatra\Bundle\BlockBundle\Block\BlockInterface;
use Sonatra\Bundle\GluonBundle\Event\GetAjaxTableEvent;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Routing\RouterInterface;

/**
 * Table Pager Block Type.
 *
 * @author François Pluchino <francois.pluchino@sonatra.com>
 */
class TablePagerType extends AbstractType
{
    /**
     * @var Request
     */
    protected $request;

    /**
     * @var EventDispatcherInterface
     */
    protected $dispatcher;

    /**
     * @var RouterInterface
     */
    protected $router;

    /**
     * Constructor.
     *
     * @param EventDispatcherInterface $dispatcher
     * @param RequestStack             $requestStack
     * @param RouterInterface          $router
     */
    public function __construct(EventDispatcherInterface $dispatcher, RequestStack $requestStack, RouterInterface $router)
    {
        $this->request = $requestStack->getMasterRequest();
        $this->dispatcher = $dispatcher;
        $this->router = $router;
    }

    /**
     * {@inheritdoc}
     */
    public function buildView(BlockView $view, BlockInterface $block, array $options)
    {
        $url = $this->request->getRequestUri();
        $source = $block->getParent()->getData();
        $sortOrder = array();

        if (null === $options['route']) {
            $event = new GetAjaxTableEvent($view->parent->vars['id'], $this->request, $source);
            $this->dispatcher->dispatch(AjaxEvents::INJECTION, $event);
        } else {
            $routeParams = $options['route_parameters'];
            $routeReferenceType = $options['route_reference_type'];
            $url = $this->router->generate($options['route'], $routeParams, $routeReferenceType);
        }

        foreach ($source->getSortColumns() as $def) {
            $sortOrder[] = $def['name'];
        }

        $view->vars = array_replace($view->vars, array(
            'source' => $source,
            'attr'   => array_replace($view->vars['attr'], array(
                'data-table-pager'    => 'true',
                'data-locale'         => $source->getLocale(),
                'data-page-size'      => $source->getPageSize(),
                'data-page-number'    => $source->getPageNumber(),
                'data-size'           => $source->getSize(),
                'data-parameters'     => json_encode($source->getParameters()),
                'data-ajax-id'        => null === $options['route'] ? $view->parent->vars['id'] : null,
                'data-url'            => $url,
                'data-multi-sortable' => $options['multi_sortable'] ? 'true' : 'false',
                'data-sort-order'     => json_encode($sortOrder),
            )),
        ));

        foreach ($source->getColumns() as $child) {
            /* @var BlockInterface $child */
            if ($child->getOption('sortable')) {
                $view->vars['attr']['data-sortable'] = 'true';
                break;
            }
        }

        if (array_key_exists('data-table-select', $view->parent->vars['attr'])
                && 'true' === $view->parent->vars['attr']['data-table-select']) {
            $view->vars['attr']['data-table-id'] = $view->parent->vars['id'];
        }
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'locale'               => \Locale::getDefault(),
            'page_size'            => null,
            'page_number'          => null,
            'route'                => null,
            'route_parameters'     => array(),
            'route_reference_type' => RouterInterface::ABSOLUTE_PATH,
            'multi_sortable'       => false,
        ));

        $resolver->addAllowedTypes(array(
            'locale'               => 'string',
            'page_size'            => array('null', 'int'),
            'page_number'          => array('null', 'int'),
            'route'                => array('null', 'string'),
            'route_parameters'     => 'array',
            'route_reference_type' => 'bool',
            'multi_sortable'       => 'bool',
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'table_pager';
    }
}
