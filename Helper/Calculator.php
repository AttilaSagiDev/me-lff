<?php
/**
 * Copyright © 2016 Magevolve Ltd. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Me\Lff\Helper;

/**
 * Extension Calculator Helper
 */
use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;
use Magento\Checkout\Model\Session as CheckoutSession;
use Magento\Quote\Model\Quote;
use Me\Lff\Helper\Data as DataHelper;
use Magento\Framework\Pricing\PriceCurrencyInterface;
use Me\Lff\Model\System\Config\Backend\Taxsuffix;

class Calculator extends AbstractHelper
{
    /**
     * @var CheckoutSession
     */
    private $checkoutSession;

    /**
     * @var Quote
     */
    private $quote;

    /**
     * @var PriceCurrencyInterface
     */
    private $priceCurrency;

    /**
     * @var DataHelper
     */
    private $helper;

    /**
     * Calculator constructor.
     *
     * @param Context $context
     * @param CheckoutSession $checkoutSession
     * @param DataHelper $dataHelper
     * @param PriceCurrencyInterface $priceCurrency
     */
    public function __construct(
        Context $context,
        CheckoutSession $checkoutSession,
        DataHelper $dataHelper,
        PriceCurrencyInterface $priceCurrency
    ) {
        $this->checkoutSession = $checkoutSession;
        $this->helper = $dataHelper;
        $this->priceCurrency = $priceCurrency;
        parent::__construct($context);
    }

    /**
     * Get left amount price
     *
     * @return float
     */
    public function getAmountPrice()
    {
        $amountPrice = 0;

        $this->_getQuote();
        $freeShippingAmount = $this->helper
            ->getCurrentFreeShippingAmount();

        if (null != $this->quote && $this->quote->getId()) {
            if (!$this->helper->showIfCartIsEmpty()
                && !(float)$this->quote->getBaseSubtotal()
            ) {
                $amountPrice = 0;
            } else {
                if (!$this->quote->getShippingAddress()->getFreeShipping()) {
                    $subtotalInclTax = $this->_getSubtotalInclTax($this->quote);
                    $amountPrice = $freeShippingAmount - $subtotalInclTax;
                } else {
                    $amountPrice = 0;
                }
            }
        }

        if (!(int)$amountPrice
            && $this->helper->showIfCartIsEmpty()
            && !$this->quote->getShippingAddress()->getFreeShipping()
        ) {
            $amountPrice = $freeShippingAmount;
        }

        return (float)$amountPrice;
    }

    /**
     * Get including or excluding tax string
     *
     * @return string
     */
    public function getTaxSuffixInfo()
    {
        $taxSuffixInfo = '';

        $taxSuffixConfig = $this->helper->getTaxSuffixInfo();
        if (Taxsuffix::EXCL_TAX == $taxSuffixConfig) {
            $taxSuffixInfo = __('(excl. tax)');
        } elseif (Taxsuffix::INCL_TAX == $taxSuffixConfig) {
            $taxSuffixInfo = __('(incl. tax)');
        } elseif (Taxsuffix::AUTO_TAX == $taxSuffixConfig) {
            $cartTax = $this->quote->getShippingAddress()->getBaseTaxAmount();
            if (null != $cartTax && (float)$cartTax) {
                $taxSuffixInfo = __('(incl. tax)');
            } else {
                $taxSuffixInfo = __('(excl. tax)');
            }
        }

        return $taxSuffixInfo;
    }

    /**
     * Format price
     *
     * @param float $price
     * @return string
     */
    public function formatPrice($price)
    {
        return $this->priceCurrency->format(
            $price,
            true,
            PriceCurrencyInterface::DEFAULT_PRECISION,
            $this->_getQuote()->getStore()
        );
    }

    /**
     * Get percentage
     *
     * @param float $price
     * @return float
     */
    public function getPercentage($price)
    {
        $percentage = 0;
        $currentAmount = $this->helper->getCurrentFreeShippingAmount();

        if ($price < $currentAmount) {
            $percentage = 100 * (1 - ($price / $this->helper->getCurrentFreeShippingAmount()));
        } else {
            $percentage = 100;
        }

        if ($this->helper->showIfCartIsEmpty() && $price == $currentAmount) {
            $percentage = 0;
        }

        if ($percentage > 0 && $percentage < 1) {
            $percentage = 1;
        }

        return floor($percentage);
    }

    /**
     * Get to show success message
     *
     * @param $price
     * @return bool
     */
    public function getShowSuccessMessage($price)
    {
        if ($this->helper->showSuccessMessage()
            && $price <= 0
            && null != $this->quote
            && $this->quote->getId()
            && (float)$this->quote->getBaseSubtotal() > 0
            && $this->quote->getIsActive()
        ) {
            return true;
        }

        return false;
    }

    /**
     * Get subtotal including tax
     *
     * @param Quote $quote
     * @return float
     */
    private function _getSubtotalInclTax(Quote $quote)
    {
        $subtotalInclTax = $quote->getShippingAddress()->getSubtotalInclTax();
        if (!(int)$subtotalInclTax) {
            $subtotalInclTax = $quote->getBillingAddress()->getSubtotalInclTax();
        }

        return $subtotalInclTax;
    }

    /**
     * Get active quote
     *
     * @return Quote
     */
    private function _getQuote()
    {
        if (null === $this->quote) {
            $this->quote = $this->checkoutSession->getQuote();
        }

        return $this->quote;
    }

    /**
     * Get cart items count
     *
     * @return int|mixed|null
     */
    public function getCartItemCount()
    {
        return $this->checkoutSession->getQuote()->getItemsCount();
    }
}
