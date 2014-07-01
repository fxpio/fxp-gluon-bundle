<?php

/*
 * This file is part of the Sonatra package.
 *
 * (c) François Pluchino <francois.pluchino@sonatra.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sonatra\Bundle\GluonBundle\Twig\Extension;

/**
 * The fonts twig extension.
 *
 * @author François Pluchino <francois.pluchino@sonatra.com>
 */
class FontExtension extends \Twig_Extension
{
    /**
     * @var string
     */
    protected $linkOpenSans;

    /**
     * @var string
     */
    protected $linkRaleway;

    /**
     * @var \Twig_Environment
     */
    protected $environment;

    /**
     * Construcotr.
     *
     * @param string $linkOpenSans
     * @param string $linkRaleway
     */
    public function __construct($linkOpenSans, $linkRaleway)
    {
        $this->linkOpenSans = $linkOpenSans;
        $this->linkRaleway = $linkRaleway;
    }

    /**
     * {@inheritdoc}
     */
    public function initRuntime(\Twig_Environment $environment)
    {
        $this->environment = $environment;
    }

    /**
     * {@inheritdoc}
     */
    public function getFunctions()
    {
        return array(
            new \Twig_SimpleFunction('sonatra_gluon_fonts', array($this, 'renderFonts'), array('is_safe' => array('html'))),
        );
    }

    /**
     * Render link fonts.
     *
     * @return string The HTML
     */
    public function renderFonts()
    {
        $options = array();

        if (null !== $this->linkOpenSans) {
            $options['link_open_sans'] = $this->linkOpenSans;
        }

        if (null !== $this->linkRaleway) {
            $options['link_raleway'] = $this->linkRaleway;
        }

        if (0 === count($options)) {
            return '';
        }

        return $this->environment->render('SonatraGluonBundle:Font:font.html.twig', $options);
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'sonatra_gluon_extension';
    }
}
