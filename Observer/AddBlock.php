<?php
/**
 * Copyright © 2016 Magevolve Ltd. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Me\Lff\Observer;

use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\View\LayoutFactory;
use Magento\Framework\View\LayoutInterface;
use Me\Lff\Helper\Data as DataHelper;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Event\Observer;
use Psr\Log\LoggerInterface;
use Magento\Framework\View\Element\BlockInterface;

class AddBlock implements ObserverInterface
{
    /**
     * Core store config
     *
     * @var ScopeConfigInterface
     */
    private $scopeConfig;

    /**
     * @var LayoutFactory
     */
    private $layoutFactory;

    /**
     * @var DataHelper
     */
    private $helper;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * AddBlock constructor.
     *
     * @param ScopeConfigInterface $scopeConfig
     * @param LayoutFactory $layoutFactory
     * @param DataHelper $dataHelper
     * @param LoggerInterface $loggerInterface
     */
    public function __construct(
        ScopeConfigInterface $scopeConfig,
        LayoutFactory $layoutFactory,
        DataHelper $dataHelper,
        LoggerInterface $loggerInterface
    ) {
        $this->scopeConfig = $scopeConfig;
        $this->layoutFactory = $layoutFactory;
        $this->helper = $dataHelper;
        $this->logger = $loggerInterface;
    }

    /**
     * Add block observer
     *
     * @param Observer $observer
     * @return self|boolean
     */
    public function execute(Observer $observer)
    {
        if ($this->helper->isFreeShippingNotificationAvailable()) {
            try {
                /** @var LayoutInterface $layout */
                $layout = $observer->getLayout();
                $fullActionName = $observer->getFullActionName();

                if ($fullActionName != 'customer_section_load') {
                    $childBlock = null;
                    $parentName = '';
                    $showBlock = false;
                    if ($this->helper->isEnabledOnCartPage() && $fullActionName == 'checkout_cart_index') {
                        $parentName = 'content.top';
                        $childBlock = $this->_getChildBlock(true);
                        $showBlock = true;
                    } elseif ($this->helper->showInCustomPosition() && $fullActionName != 'checkout_cart_index') {
                        $parentName = $this->helper->getBlockNotificationContainer();
                        $childBlock = $this->_getChildBlock();
                        $showBlock = true;
                    }

                    if ($showBlock && (null != $childBlock)) {
                        $parentBlockAlias = $layout->getElementAlias($parentName);

                        if (!$parentBlockAlias || $parentBlockAlias == '') {
                            return false;
                        }

                        $layout->addBlock($childBlock, 'me.lff', $parentName);
                    }
                }
            } catch (\Exception $e) {
                $this->logger->critical($e);
            }
        }

        return $this;
    }

    /**
     * Get child block
     *
     * @param bool $isCart
     * @return BlockInterface
     */
    private function _getChildBlock($isCart = false)
    {
        $template = 'Me_Lff::sidebar/lff.phtml';
        $name = 'lff.sidebar';

        if ($isCart) {
            $template = 'Me_Lff::checkout/cart/lff.phtml';
            $name = 'lff.cart';
        }

        $childBlock = $this->layoutFactory->create()->createBlock(
            'Me\Lff\Block\Notification\LeftAmountInfo',
            $name,
            [
                'data' => [
                    'template' => $template
                ]
            ]
        );

        return $childBlock;
    }
}
