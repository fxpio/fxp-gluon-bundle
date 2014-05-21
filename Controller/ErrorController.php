<?php

/**
 * This file is part of the Sonatra package.
 *
 * (c) François Pluchino <francois.pluchino@sonatra.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sonatra\Bundle\GluonBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;

/**
 * Error controller.
 *
 * @author François Pluchino <francois.pluchino@sonatra.com>
 */
class ErrorController extends Controller
{
    /**
     * Display page error for dev.
     */
    public function showAction($code)
    {
        $status = Response::$statusTexts;
        $text = isset($status[$code]) ? $status[$code] : null;

        return $this->render('@Twig/Exception/error.html.twig', array('status_code' => $code, 'status_text' => $text));
    }
}
