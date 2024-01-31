<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Me\Lff\CustomerData;

use Magento\Customer\CustomerData\SectionSourceInterface;
use Me\Lff\Helper\Data as DataHelper;
use Me\Lff\Helper\Calculator as CalculatorHelper;
use Magento\Framework\Escaper;

/**
 * Lff section
 */
class Lff implements SectionSourceInterface
{
    /**
     * @var DataHelper
     */
    private $helper;

    /**
     * @var CalculatorHelper
     */
    private $calculatorHelper;

    /**
     * @var Escaper
     */
    private $escaper;

    /**
     * Lff constructor.
     *
     * @param DataHelper $dataHelper
     * @param CalculatorHelper $calculatorHelper
     * @param Escaper $escaper
     */
    public function __construct(
        DataHelper $dataHelper,
        CalculatorHelper $calculatorHelper,
        Escaper $escaper
    ) {
        $this->helper = $dataHelper;
        $this->calculatorHelper = $calculatorHelper;
        $this->escaper = $escaper;
    }

    /**
     * Get data
     *
     * @return array
     */
    public function getSectionData()
    {
        $price = $this->calculatorHelper->getAmountPrice();
        $showSuccess = $this->calculatorHelper->getShowSuccessMessage($price);
        $percent = $this->calculatorHelper->getPercentage($price);
        $priceInfo = $this->_getPriceInfo($price);
        $sectionData = [
            'price_text' => $this->_getPriceText($priceInfo),
            'block_title' => $this->_getBlockTitle(),
            'show_block' => (int)$price > 0 ? 1 : 0,
            'show_success' => $showSuccess,
            'success_message' => $showSuccess ? $this->helper->getSuccessMessage() : '',
            'show_progress' => (int)$this->helper->isProgressBarEnabled(),
            'show_progress_cart' => (int)$this->helper->isProgressBarCartEnabled(),
            'progress_value' => $percent
        ];

        return $sectionData;
    }

    /**
     * Get price text
     *
     * @param string $priceInfo
     * @return string
     */
    private function _getPriceText($priceInfo = '')
    {
        $priceText = '';

        if ($notificationText = $this->helper->getBlockNotificationText()) {
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
     * Get block title
     *
     * @return string
     */
    private function _getBlockTitle()
    {
        $blockTitle = '';

        if ($blockTitle = $this->helper->getBlockTitle()) {
            $blockTitle = $this->escaper->escapeHtml($blockTitle);
        } else {
            $blockTitle = __('Free Shipping Info');
        }

        return $blockTitle;
    }

    /**
     * Get price info
     *
     * @param float $price
     * @return string
     */
    private function _getPriceInfo($price)
    {
        return $this->calculatorHelper->formatPrice($price) . ' ' .
        $this->calculatorHelper->getTaxSuffixInfo();
    }
}
