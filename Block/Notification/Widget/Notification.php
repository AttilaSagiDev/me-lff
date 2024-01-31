<?php
/**
 * Copyright © 2016 Magevolve Ltd. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Me\Lff\Block\Notification\Widget;

use Me\Lff\Block\Notification\LeftAmountInfo;
use Magento\Widget\Block\BlockInterface;

/**
 * @method string getTitle()
 * @method string getNotification()
 * @method int getProgress()
 */
class Notification extends LeftAmountInfo implements BlockInterface
{
    /**
     * Get block title
     *
     * @return string
     */
    public function getBlockTitle()
    {
        return $this->escapeHtml($this->getTitle());
    }

    /**
     * Get price text
     *
     * @param string $priceInfo
     * @param bool $isCart
     * @return string
     */
    public function getPriceText($priceInfo = '', $isCart = false)
    {
        $priceText = '';

        if ($notificationText = $this->getNotification()) {
            $priceText = str_replace('%s', $priceInfo, $notificationText);
        } else {
            $priceText = __(
                'You need to add %1 more to your cart, ' .
                'to gain free shipping.',
                $priceInfo
            );
        }

        return $priceText;
    }

    /**
     * Get show progress
     *
     * @return int
     */
    public function getShowProgressBar()
    {
        return (int)$this->getProgress();
    }
}
