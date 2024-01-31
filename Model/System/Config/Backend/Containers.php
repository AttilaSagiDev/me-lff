<?php
/**
 * Copyright © 2016 Magevolve Ltd. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Me\Lff\Model\System\Config\Backend;

use Magento\Framework\Option\ArrayInterface;
use Magento\Framework\View\Layout\ProcessorFactory;
use Magento\Theme\Model\ResourceModel\Theme\CollectionFactory;
use Magento\Theme\Model\ResourceModel\Theme\Collection as ThemeCollection;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\View\Design\Theme\ThemeProviderInterface;
use Magento\Framework\Message\ManagerInterface;
use Magento\Framework\View\Layout\ProcessorInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\View\DesignInterface;
use Magento\Theme\Model\Theme;

class Containers implements ArrayInterface
{
    /**#@+
     * Frontend page layouts
     */
    const PAGE_LAYOUT_1COLUMN = '1column-center';
    const PAGE_LAYOUT_2COLUMNS_LEFT = '2columns-left';
    const PAGE_LAYOUT_2COLUMNS_RIGHT = '2columns-right';
    const PAGE_LAYOUT_3COLUMNS = '3columns';
    /**#@-*/

    /**
     * @var ProcessorFactory
     */
    private $layoutProcessorFactory;

    /**
     * @var CollectionFactory
     */
    private $themesFactory;

    /**
     * @var ScopeConfigInterface
     */
    private $config;

    /**
     * @var ThemeProviderInterface
     */
    private $themeProviderInterface;

    /**
     * @var ManagerInterface
     */
    private $messageManager;

    /**
     * @param ProcessorFactory $layoutProcessorFactory
     * @param CollectionFactory $themesFactory
     * @param ScopeConfigInterface $config
     * @param ThemeProviderInterface $themeProviderInterface
     * @param ManagerInterface $messageManager
     */
    public function __construct(
        ProcessorFactory $layoutProcessorFactory,
        CollectionFactory $themesFactory,
        ScopeConfigInterface $config,
        ThemeProviderInterface $themeProviderInterface,
        ManagerInterface $messageManager
    ) {
        $this->layoutProcessorFactory = $layoutProcessorFactory;
        $this->themesFactory = $themesFactory;
        $this->config = $config;
        $this->themeProviderInterface = $themeProviderInterface;
        $this->messageManager = $messageManager;
    }

    /**
     * Options getter
     *
     * @return array
     */
    public function toOptionArray()
    {
        $options = [];

        $options[] = [
            'value' => '',
            'label' => __('-- Please Select --')
        ];

        try {
            $layoutMergeParams = ['theme' => $this->_getThemeInstance($this->getCurrentDefaultThemeId())];
            /** @var $layoutProcessor ProcessorInterface */
            $layoutProcessor = $this->layoutProcessorFactory
                ->create($layoutMergeParams);
            $layoutProcessor->addPageHandles(['default']);
            $layoutProcessor->load();

            $pageLayoutProcessor = $this->layoutProcessorFactory
                ->create($layoutMergeParams);
            $pageLayouts = $this->getPageLayouts();
            foreach ($pageLayouts as $pageLayout) {
                $pageLayoutProcessor->addHandle($pageLayout);
            }

            $pageLayoutProcessor->load();

            $containers = array_merge(
                $pageLayoutProcessor->getContainers(),
                $layoutProcessor->getContainers()
            );
            asort($containers, SORT_STRING);

            foreach ($containers as $containerName => $containerLabel) {
                $options[] = [
                    'value' => $containerName,
                    'label' => $containerLabel
                ];
            }
        } catch (LocalizedException $e) {
            $this->messageManager->addWarningMessage($e->getMessage());
        }

        return $options;
    }

    /**
     * Retrieve theme instance by its identifier
     *
     * @param int $themeId
     * @return \Magento\Framework\DataObject
     * @throws LocalizedException
     */
    private function _getThemeInstance($themeId)
    {
        /** @var ThemeCollection $themeCollection */
        $themeCollection = $this->themesFactory->create();
        /** @var Theme $theme */
        $theme = $themeCollection->getItemById($themeId);
        if (null != $theme && $theme->getId()) {
            return $themeCollection->getItemById($themeId);
        } else {
            throw new LocalizedException(
                __('No default frontend theme found. Custom position containers unavailable!')
            );
        }
    }

    /**
     * Get default theme Id
     *
     * @return int
     */
    private function getCurrentDefaultThemeId()
    {
        $themeId = $this->config->getValue(
            DesignInterface::XML_PATH_THEME_ID,
            ScopeConfigInterface::SCOPE_TYPE_DEFAULT
        );

        /** @var $theme \Magento\Framework\View\Design\ThemeInterface */
        $theme = $this->themeProviderInterface->getThemeById($themeId);

        return $theme->getId();
    }

    /**
     * Retrieve page layouts
     *
     * @return array
     */
    private function getPageLayouts()
    {
        return [
            self::PAGE_LAYOUT_1COLUMN,
            self::PAGE_LAYOUT_2COLUMNS_LEFT,
            self::PAGE_LAYOUT_2COLUMNS_RIGHT,
            self::PAGE_LAYOUT_3COLUMNS,
        ];
    }
}
