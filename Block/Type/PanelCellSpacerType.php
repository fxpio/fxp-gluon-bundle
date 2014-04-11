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

/**
 * Panel Cell Spacer Block Type.
 *
 * @author François Pluchino <francois.pluchino@sonatra.com>
 */
class PanelCellSpacerType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function getParent()
    {
        return 'panel_cell';
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'panel_cell_spacer';
    }
}
