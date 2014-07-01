<?php

/*
 * This file is part of the Sonatra package.
 *
 * (c) François Pluchino <francois.pluchino@sonatra.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sonatra\Bundle\GluonBundle\Assetic\Factory\Resource;

use Sonatra\Bundle\BootstrapBundle\Assetic\Factory\Resource\AbstractDynamicResource;

/**
 * Theme stylesheet resource.
 *
 * @author François Pluchino <francois.pluchino@sonatra.com>
 */
class StylesheetThemeResource extends AbstractDynamicResource
{
    /**
     * Preserves the order of loading.
     * @var array
     */
    protected $orderComponents = array(
        'variables',
        'mixins'
    );
}
