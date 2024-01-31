<?php
/**
 * Copyright © 2016 Magevolve Ltd. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Me\Lff\Helper;

/**
 * Extension Helper
 */
use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Directory\Helper\Data as DirectoryHelper;
use Magento\Store\Model\ScopeInterface;

class Data extends AbstractHelper
{
    /**
     * Extension enabled path
     */
    const XML_PATH_ENABLED = 'lff/basic/enabled';

    /**
     * Path to store config free shipping is active
     *
     * @var string
     */
    const XML_PATH_FREE_SHIPPING_ACTIVE = 'carriers/freeshipping/active';

    /**
     * Path to store config free shipping amount
     *
     * @var string
     */
    const XML_PATH_FREE_AMNT = 'carriers/freeshipping/free_shipping_subtotal';

    /**
     * Path to store config if tax info is display
     *
     * @var string
     */
    const XML_PATH_TAX_INFO = 'lff/common/taxinfo';

    /**
     * Path to store config if tax suffix string
     *
     * @var string
     */
    const XML_PATH_TAX_SUFFIX = 'lff/common/taxsuffix';

    /**
     * Path to store config to show notification text if cart is empty
     *
     * @var string
     */
    const XML_PATH_EMPTY = 'lff/common/empty';

    /**
     * Path to store config to show success message
     *
     * @var string
     */
    const XML_PATH_SHOW_SUCCESS = 'lff/common/show_success';

    /**
     * Path to store config success message
     *
     * @var string
     */
    const XML_PATH_SUCCESS_MSG = 'lff/common/success_message';

    /**
     * Path to store config notification text enabled on cart page
     *
     * @var string
     */
    const XML_PATH_NOTIFICATION_TEXT_CART = 'lff/cart/enabled';

    /**
     * Path to store config free shipping notification text
     *
     * @var string
     */
    const XML_PATH_NOTIFICATION_TEXT = 'lff/cart/notification';

    /**
     * Path to store config progress bar on cart page
     *
     * @var string
     */
    const XML_PATH_PROGRESS = 'lff/cart/progress';

    /**
     * Path to store config to show notification block in custom position
     *
     * @var string
     */
    const XML_PATH_POSITION = 'lff/display/position';

    /**
     * Path to store config free shipping notification block title
     *
     * @var string
     */
    const XML_PATH_TITLE = 'lff/display/title';

    /**
     * Path to store config free shipping notification block notification text
     *
     * @var string
     */
    const XML_PATH_BLOCK_NOTIFICATION_TEXT = 'lff/display/block_notification';

    /**
     * Path to store config progress of the notification block
     *
     * @var string
     */
    const XML_PATH_BLOCK_PROGRESS = 'lff/display/progress';

    /**
     * Path to store config containers of the notification block
     *
     * @var string
     */
    const XML_PATH_BLOCK_CONTAINERS = 'lff/display/containers';

    /**
     * Path to store config custom amount enabled
     *
     * @var string
     */
    const XML_PATH_EXTRA_ENABLED = 'lff/extra/enabled';

    /**
     * Path to store config custom amount
     *
     * @var string
     */
    const XML_PATH_EXTRA_AMOUNT = 'lff/extra/custom_amount';

    /**
     * Store model manager
     *
     * @var StoreManagerInterface
     */
    private $storeManager;

    /**
     * @var DirectoryHelper
     */
    private $directory;

    /**
     * Data constructor.
     *
     * @param Context $context
     * @param StoreManagerInterface $storeManager
     * @param DirectoryHelper $directoryHelper
     */
    public function __construct(
        Context $context,
        StoreManagerInterface $storeManager,
        DirectoryHelper $directoryHelper
    ) {
        $this->storeManager = $storeManager;
        $this->directory = $directoryHelper;
        parent::__construct($context);
    }

    /**
     * Check if extension enabled
     *
     * @return bool
     */
    public function isEnabled()
    {
        return $this->scopeConfig->isSetFlag(
            self::XML_PATH_ENABLED,
            ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * Checks is free shipping active
     *
     * @return bool
     */
    public function isFreeShippingEnabled()
    {
        return $this->scopeConfig->isSetFlag(
            self::XML_PATH_FREE_SHIPPING_ACTIVE,
            ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * Get free shipping amount
     *
     * @return float
     */
    public function getFreeShippingAmount()
    {
        return (float)$this->scopeConfig->getValue(
            'carriers/freeshipping/free_shipping_subtotal',
            ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * Checks if free shipping has amount
     *
     * @return bool
     */
    public function isFreeShippingHasAmount()
    {
        $amount = (float)$this->scopeConfig->getValue(
            self::XML_PATH_FREE_AMNT,
            ScopeInterface::SCOPE_STORE
        );
        if ($amount) {
            return true;
        }

        return false;
    }

    /**
     * Check if free shipping notification available
     *
     * @return bool
     */
    public function isFreeShippingNotificationAvailable()
    {
        if ($this->isEnabled()
            && $this->isFreeShippingEnabled()
            && $this->isFreeShippingHasAmount()
        ) {
            return true;
        }

        if ($this->isEnabled()
            && $this->useCustomAmount()
            && (float)$this->getCustomAmount() > 0
        ) {
            return true;
        }

        return false;
    }

    /**
     * Get tax suffix after price
     *
     * @return string
     */
    public function getTaxSuffixInfo()
    {
        $taxSuffix = 0;

        $taxInfo = $this->scopeConfig->isSetFlag(
            self::XML_PATH_TAX_INFO,
            ScopeInterface::SCOPE_STORE
        );

        if ($taxInfo) {
            $taxSuffix = $this->scopeConfig->getValue(
                self::XML_PATH_TAX_SUFFIX,
                ScopeInterface::SCOPE_STORE
            );
        }

        return $taxSuffix;
    }

    /**
     * Checks whether show notification if cart is empty
     *
     * @return boolean
     */
    public function showIfCartIsEmpty()
    {
        return $this->scopeConfig->isSetFlag(
            self::XML_PATH_EMPTY,
            ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * Checks whether show success message
     *
     * @return boolean
     */
    public function showSuccessMessage()
    {
        return $this->scopeConfig->isSetFlag(
            self::XML_PATH_SHOW_SUCCESS,
            ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * Get success message
     *
     * @return string
     */
    public function getSuccessMessage()
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_SUCCESS_MSG,
            ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * Check if notification available on cart page
     *
     * @return boolean
     */
    public function isEnabledOnCartPage()
    {
        return $this->scopeConfig->isSetFlag(
            self::XML_PATH_NOTIFICATION_TEXT_CART,
            ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * Get notification text
     *
     * @return boolean
     */
    public function getCartNotificationText()
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_NOTIFICATION_TEXT,
            ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * Check if progress bar enabled on cart page
     *
     * @return string
     */
    public function isProgressBarCartEnabled()
    {
        return $this->scopeConfig->isSetFlag(
            self::XML_PATH_PROGRESS,
            ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * Checks whether notification can be displayed in custom position
     *
     * @return boolean
     */
    public function showInCustomPosition()
    {
        return $this->scopeConfig->isSetFlag(
            self::XML_PATH_POSITION,
            ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * Get free shipping notification block title
     *
     * @return boolean
     */
    public function getBlockTitle()
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_TITLE,
            ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * Get free shipping notification block text
     *
     * @return string
     */
    public function getBlockNotificationText()
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_BLOCK_NOTIFICATION_TEXT,
            ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * Check if progress enabled in notification block
     *
     * @return string
     */
    public function isProgressBarEnabled()
    {
        return $this->scopeConfig->isSetFlag(
            self::XML_PATH_BLOCK_PROGRESS,
            ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * Get free shipping notification block text
     *
     * @return string
     */
    public function getBlockNotificationContainer()
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_BLOCK_CONTAINERS,
            ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * Checks whether use custom amount instead of free shipping amount
     *
     * @return boolean
     */
    public function useCustomAmount()
    {
        return $this->scopeConfig->isSetFlag(
            self::XML_PATH_EXTRA_ENABLED,
            ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * Get custom amount value
     *
     * @return float
     */
    public function getCustomAmount()
    {
        return (float)$this->scopeConfig->getValue(
            self::XML_PATH_EXTRA_AMOUNT,
            ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * Get free shipping amount
     *
     * @return float|mixed
     */
    public function getCurrentFreeShippingAmount()
    {
        $freeShippingPrice = $this->scopeConfig->getValue(
            'carriers/freeshipping/free_shipping_subtotal',
            ScopeInterface::SCOPE_STORE
        );

        if ($this->useCustomAmount()) {
            $freeShippingPrice = $this->getCustomAmount();
        }

        $storeId = $this->storeManager->getStore()->getId();
        $defaultStoreId = $this->storeManager->getDefaultStoreView()->getId();
        if ($storeId != $defaultStoreId) {
            $baseCurrency = $this->storeManager
                ->getStore()->getBaseCurrencyCode();
            $currentCurrency = $this->storeManager
                ->getStore()->getCurrentCurrencyCode();
            if ($baseCurrency != $currentCurrency) {
                $freeShippingPrice = $this->directory->currencyConvert(
                    (float)$freeShippingPrice,
                    $baseCurrency,
                    $currentCurrency
                );
            }
        }

        return (float)$freeShippingPrice;
    }
}
