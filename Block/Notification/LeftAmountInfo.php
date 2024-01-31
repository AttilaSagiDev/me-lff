<?php
/**
 * Copyright © 2016 Magevolve Ltd. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Me\Lff\Block\Notification;

use Me\Lff\Block\Lff;
use Magento\Framework\View\Element\Template\Context;
use Me\Lff\Helper\Data as DataHelper;
use Me\Lff\Helper\Calculator as CalculatorHelper;

/**
 * Left amount info block
 */
class LeftAmountInfo extends Lff
{
    /**
     * @var CalculatorHelper
     */
    private $calculatorHelper;

    /**
     * LeftAmountInfo constructor.
     *
     * @param Context $context
     * @param DataHelper $dataHelper
     * @param CalculatorHelper $calculatorHelper
     * @param array $data
     */
    public function __construct(
        Context $context,
        DataHelper $dataHelper,
        CalculatorHelper $calculatorHelper,
        array $data = []
    ) {
        $this->calculatorHelper = $calculatorHelper;
        parent::__construct($context, $dataHelper, $data);
    }

    /**
     * Get left amount price
     *
     * @return float
     */
    public function getAmountPrice()
    {
        return $this->calculatorHelper->getAmountPrice();
    }

    /**
     * Get including or excluding tax string
     *
     * @return string|void
     */
    public function getTaxSuffixInfo()
    {
        $this->calculatorHelper->getTaxSuffixInfo();
    }

    /**
     * Format price
     *
     * @param float $price
     * @return string
     */
    public function formatPrice($price)
    {
        return $this->calculatorHelper->formatPrice($price);
    }

    /**
     * Get block title
     *
     * @return string
     */
    public function getBlockTitle()
    {
        $blockTitle = '';

        if ($blockTitle = $this->helper->getBlockTitle()) {
            $blockTitle = $this->escapeHtml($blockTitle);
        } else {
            $blockTitle = __('Free Shipping Info');
        }

        return $blockTitle;
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

        $notificationText = $isCart ?
            $this->helper->getCartNotificationText() : $this->helper->getBlockNotificationText();
        if ($notificationText != '') {
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
     * Get to show success message
     *
     * @param $price
     * @return bool
     */
    public function getShowSuccessMessage($price)
    {
        return $this->calculatorHelper->getShowSuccessMessage($price);
    }

    /**
     * Get helper
     *
     * @return DataHelper
     */
    public function getHelper()
    {
        return $this->helper;
    }
}
