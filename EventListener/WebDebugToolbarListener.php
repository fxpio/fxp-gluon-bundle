<?php

/*
 * This file is part of the Fxp package.
 *
 * (c) François Pluchino <francois.pluchino@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Fxp\Bundle\GluonBundle\EventListener;

use Symfony\Bundle\TwigBundle\TwigEngine;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\FilterResponseEvent;
use Symfony\Component\HttpKernel\HttpKernelInterface;

/**
 * WebDebugToolbarListener injects the javascript edit Web Debug Toolbar.
 *
 * The onKernelResponse method must be connected to the kernel.response event.
 *
 * @author François Pluchino <francois.pluchino@gmail.com>
 */
class WebDebugToolbarListener
{
    const ENV_ENABLED = 'dev';

    protected $templating;
    protected $env;

    /**
     * Constroctor of Class.
     *
     * @param TwigEngine $templating A Twig object of template
     * @param string     $env        A type of environment (dev, prod, etc...)
     */
    public function __construct(TwigEngine $templating, $env = self::ENV_ENABLED)
    {
        $this->templating = $templating;
        $this->env = (string) $env;
    }

    /**
     * Method for a dependency injection.
     *
     * @param FilterResponseEvent $event A event object
     */
    public function onKernelResponse(FilterResponseEvent $event)
    {
        if (HttpKernelInterface::MASTER_REQUEST !== $event->getRequestType()) {
            return;
        }

        $response = $event->getResponse();
        $request = $event->getRequest();

        // do not capture redirects or modify XML HTTP Requests
        if ($request->isXmlHttpRequest()) {
            return;
        }

        if (self::ENV_ENABLED != $this->env
            || !$response->headers->has('X-Debug-Token')
            || '3' === substr($response->getStatusCode(), 0, 1)
            || ($response->headers->has('Content-Type') && false === strpos($response->headers->get('Content-Type'), 'html'))
            || 'html' !== $request->getRequestFormat()
        ) {
            return;
        }

        $this->injectEditToolbar($response);
    }

    /**
     * Injects the javavascript edit web debug toolbar into the given Response.
     *
     * @param Response $response A Response instance
     */
    protected function injectEditToolbar(Response $response)
    {
        if (function_exists('mb_stripos')) {
            $posrFunction = 'mb_strripos';
            $substrFunction = 'mb_substr';
        } else {
            $posrFunction = 'strripos';
            $substrFunction = 'substr';
        }

        $content = $response->getContent();

        if (false !== $pos = $posrFunction($content, '</head>')) {
            $toolbar = "\n".str_replace("\n", '', $this->templating->render(
                '@FxpGluon/Profiler/toolbar_css.html.twig',
                ['token' => $response->headers->get('X-Debug-Token')]
                ))."\n";
            $content = $substrFunction($content, 0, $pos).$toolbar.$substrFunction($content, $pos);
            $response->setContent($content);
        }
    }
}
