<?php
/**
 * Copyright © 2016 Magevolve Ltd. All rights reserved.
 * See COPYING.txt for license details.
 */

/**
 * Used in creating options for Tax Suffix config value selection
 */
namespace Me\Lff\Model\System\Config\Backend;

use Magento\Framework\Option\ArrayInterface;

class Taxsuffix implements ArrayInterface
{
    /**
     * var int
     */
    const EXCL_TAX = 1;

    /**
     * var int
     */
    const INCL_TAX = 2;

    /**
     * var int
     */
    const AUTO_TAX = 3;

    /**
     * Options getter
     *
     * @return array
     */
    public function toOptionArray()
    {
        return [
            [
                'value' => self::EXCL_TAX,
                'label' => __('Show (excl. tax) after price')
            ],
            [
                'value' => self::INCL_TAX,
                'label' => __('Show (incl. tax) after price')
            ],
            [
                'value' => self::AUTO_TAX,
                'label' => __(
                    'Show (excl. tax) or (incl. tax) ' .
                    'after price automatically depends on store settings'
                )
            ]
        ];
    }
}
