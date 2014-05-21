<?php

/**
 * This file is part of the Sonatra package.
 *
 * (c) François Pluchino <francois.pluchino@sonatra.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sonatra\Bundle\GluonBundle\Assetic\Factory\Resource\FontAwesome;

use Sonatra\Bundle\BootstrapBundle\Assetic\Factory\Resource\AbstractDynamicResource;

/**
 * Font Awesome stylesheet resource.
 *
 * @author François Pluchino <francois.pluchino@sonatra.com>
 */
class StylesheetResource extends AbstractDynamicResource
{
    /**
     * Preserves the order of loading.
     * @var array
     */
    protected $orderComponents = array(
        'variables', 'custom_variables',
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
     * @param array  $bundles    The bundles directories
    */
    public function __construct($cacheDir, $directory, array $components, array $bundles)
    {
        parent::__construct(sprintf('%s/font-awesome.less', $cacheDir), $directory, $components, $bundles);
    }
}
