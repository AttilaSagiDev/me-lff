<?php
/**
 * Copyright © 2016 Magevolve Ltd. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Me\Lff\Model\System\Config\Backend;

use Magento\Framework\Model\Context;
use \Magento\Framework\Registry;
use \Magento\Framework\App\Config\ScopeConfigInterface;
use \Magento\Framework\App\Cache\TypeListInterface;
use \Magento\Framework\Model\ResourceModel\AbstractResource;
use \Magento\Framework\Data\Collection\AbstractDb;
use \Me\Lff\Helper\Data as DataHelper;
use Magento\Framework\UrlInterface;
use Magento\Theme\Model\ResourceModel\Theme\CollectionFactory as ThemeCollectionFactory;
use Magento\Theme\Model\ResourceModel\Theme\Collection as ThemeCollection;
use Magento\Framework\View\Design\Theme\ThemeProviderInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\App\Config\Value as ConfigValue;
use Magento\Framework\App\Area;
use Magento\Framework\View\DesignInterface;

class Check extends ConfigValue
{
    /**
     * @var DataHelper
     */
    private $helper;

    /**
     * @var UrlInterface
     */
    private $urlBuilder;

    /**
     * @var ThemeCollectionFactory
     */
    private $themesFactory;

    /**
     * @var ThemeProviderInterface
     */
    private $themeProviderInterface;

    /**
     * @param Context $context
     * @param Registry $registry
     * @param ScopeConfigInterface $config
     * @param TypeListInterface $cacheTypeList
     * @param AbstractResource|null $resource
     * @param AbstractDb|null $resourceCollection
     * @param DataHelper $dataHelper
     * @param UrlInterface $urlBuilder
     * @param ThemeCollectionFactory $themesFactory
     * @param ThemeProviderInterface $themeProviderInterface
     * @param array $data
     */
    public function __construct(
        Context $context,
        Registry $registry,
        ScopeConfigInterface $config,
        TypeListInterface $cacheTypeList,
        AbstractResource $resource = null,
        AbstractDb $resourceCollection = null,
        DataHelper $dataHelper,
        UrlInterface $urlBuilder,
        ThemeCollectionFactory $themesFactory,
        ThemeProviderInterface $themeProviderInterface,
        array $data = []
    ) {
        $this->helper = $dataHelper;
        $this->urlBuilder = $urlBuilder;
        $this->themesFactory = $themesFactory;
        $this->themeProviderInterface = $themeProviderInterface;
        parent::__construct(
            $context,
            $registry,
            $config,
            $cacheTypeList,
            $resource,
            $resourceCollection,
            $data
        );
    }

    /**
     * Processing object before save data
     *
     * @return $this
     * @throws \Exception
     */
    public function beforeSave()
    {
        $enabled = $this->getValue();
        $extraEnabled = (bool)$this->getData('groups/extra/fields/enabled/value');
        $extraValue = (float)$this->getData('groups/extra/fields/custom_amount/value');
        if ($enabled) {
            try {
                if (!$this->helper->isFreeShippingEnabled() && !$extraEnabled) {
                    throw new LocalizedException(
                        __(
                            'Free Shipping is disabled. ' .
                            'Please <a href="%1">configure correctly</a> ' .
                            'Free Shipping before enabling the extension.',
                            $this->getFreeShippingSectionUrl()
                        )
                    );
                }

                if (!$this->helper->isFreeShippingHasAmount() && !$extraEnabled) {
                    throw new LocalizedException(
                        __(
                            'Free Shipping minimum order amount is zero. ' .
                            'Please <a href="%1">configure correctly</a> ' .
                            'Free Shipping before enabling the extension.',
                            $this->getFreeShippingSectionUrl()
                        )
                    );
                }

                if ($extraEnabled
                    && $this->helper->isFreeShippingEnabled()
                    && $this->helper->isFreeShippingHasAmount()
                    && $this->helper->getFreeShippingAmount() != $extraValue
                ) {
                    throw new LocalizedException(
                        __(
                            'Free Shipping minimum order amount is not equal to the customized value. ' .
                            'This can cause errors in the notification! ' .
                            'Please <a href="%1">configure correctly</a> ' .
                            'Free Shipping before enabling the extension.',
                            $this->getFreeShippingSectionUrl()
                        )
                    );
                }

                $cartEnabled = (bool)$this->getData('groups/cart/fields/enabled/value');
                $cartNotification = $this->getData('groups/cart/fields/notification/value');
                if ($cartEnabled) {
                    $this->_checkNotification($cartNotification);
                }

                $sidebarNotification = $this->getData('groups/display/fields/block_notification/value');
                $this->_checkNotification($sidebarNotification);

                $customPosition = (bool)$this->getData('groups/display/fields/position/value');
                if ($customPosition) {
                    $container = (bool)$this->getData('groups/display/fields/containers/value');
                    $this->_checkContainers($container);
                }
            } catch (\Exception $e) {
                throw new LocalizedException(__($e->getMessage()), $e);
            }
        }
        return $this;
    }

    /**
     * Check notification
     *
     * @param string $notification
     * @throws LocalizedException
     */
    private function _checkNotification($notification = '')
    {
        if ($notification) {
            if (strpos($notification, '%s') === false) {
                throw new LocalizedException(
                    __('The notification text must include %s. Please correct it!')
                );
            }
        } else {
            throw new LocalizedException(
                __('The notification text can not be empty. Please correct it!')
            );
        }
    }

    /**
     * Check containers
     *
     * @param string $container
     * @throws LocalizedException
     */
    private function _checkContainers($container)
    {
        /** @var ThemeCollection $themeCollection */
        $themeCollection = $this->themesFactory->create();
        $themeCollection->addFieldToFilter('area', ['eq' => Area::AREA_FRONTEND]);
        if (!$themeCollection->getSize()) {
            throw new LocalizedException(
                __('No frontend themes found. Please check themes configuration!')
            );
        } elseif (!$this->hasCurrentThemeId()) {
            throw new LocalizedException(
                __('No default frontend theme found. Please check themes configuration!')
            );
        }

        if (!$container) {
            throw new LocalizedException(
                __('The selected container can not be empty. Please correct it!')
            );
        }
    }

    /**
     * Get basic theme Id
     *
     * @return int
     */
    private function hasCurrentThemeId()
    {
        $themeId = $this->_config->getValue(
            DesignInterface::XML_PATH_THEME_ID,
            ScopeConfigInterface::SCOPE_TYPE_DEFAULT
        );

        /** @var $theme \Magento\Framework\View\Design\ThemeInterface */
        $theme = $this->themeProviderInterface->getThemeById($themeId);
        if (null != $theme && $theme->getId()) {
            return true;
        }

        return false;
    }

    /**
     * Retrieve Free Shipping configuration url in admin
     *
     * @return  string
     */
    private function getFreeShippingSectionUrl()
    {
        return $this->getUrl(
            'adminhtml/system_config/edit/section/carriers',
            []
        );
    }

    /**
     * Return URL for admin area
     *
     * @param string $route
     * @param array $params
     * @return string
     */
    private function getUrl($route, $params = [])
    {
        return $this->urlBuilder->getUrl($route, $params);
    }
}
