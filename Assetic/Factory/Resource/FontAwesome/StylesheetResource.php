<?php

/*
 * This file is part of the Sonatra package.
 *
 * (c) François Pluchino <francois.pluchino@sonatra.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sonatra\Bundle\GluonBundle\Assetic\Factory\Resource\FontAwesome;

use Sonatra\Bundle\BootstrapBundle\Assetic\Factory\Resource\DynamicResourceInterface;
use Symfony\Component\Filesystem\Filesystem;

/**
 * Font Awesome stylesheet resource.
 *
 * @author François Pluchino <francois.pluchino@sonatra.com>
 */
class StylesheetResource implements DynamicResourceInterface
{
    /**
     * @var string
     */
    protected $path;

    /**
     * @var string
     */
    protected $directory;

    /**
     * @var array
     */
    protected $components;

    /**
     * @var Filesystem
     */
    protected $filesystem;

    /**
     * Preserves the order of loading of font awesome.
     * @var array
     */
    protected $orderComponents = array(
        'variables', 'default_variables', 'custom_variables',
        'mixins', 'custom_mixins',
        'path',
        'core',
        'larger',
        'fixed_width',
        'list',
        'bordered_pulled',
        'spinning',
        'rotated_flipped',
        'stacked',
        'icons'
    );

    /**
     * Constructor.
     *
     * @param string $cacheDir   The cache directory
     * @param string $directory  The bootstrap less directory
     * @param array  $components The bootstrap less components configuration
     */
    public function __construct($cacheDir, $directory, array $components)
    {
        $this->path = sprintf('%s/font-awesome.less', $cacheDir);
        $this->directory = rtrim($directory, '/');
        $this->components = $components;
        $this->filesystem = new Filesystem();
    }

    /**
     * {@inheritdoc}
     */
    public function isFresh($timestamp)
    {
        return file_exists($this->path) && filemtime($this->path) <= $timestamp;
    }

    /**
     * {@inheritdoc}
     */
    public function getContent()
    {
        $this->compile();

        return file_get_contents($this->path);
    }

    /**
     * {@inheritdoc}
     */
    public function compile()
    {
        if (!file_exists($this->path)) {
            $content = '';

            foreach ($this->orderComponents as $component) {
                $content = $this->addImport($content, $component, $this->components[$component]);
            }

            $this->filesystem->dumpFile($this->path, $content);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function __toString()
    {
        return $this->path;
    }

    /**
     * Add import file in content.
     *
     * @param string      $content   The content
     * @param string      $component The name of component
     * @param string|bool $value     The value of component
     *
     * @return string The content
     */
    protected function addImport($content, $component, $value)
    {
        if (is_string($value)) {
            $content .= sprintf('@import "relative(%s)";', $value);
            $content .= PHP_EOL;

        } elseif ($value) {
            $content .= sprintf('@import "relative(%s/%s.less)";', $this->directory, str_replace('_', '-', $component));
            $content .= PHP_EOL;
        }

        return $content;
    }
}
