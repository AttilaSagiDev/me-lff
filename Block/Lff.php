<?php
/**
 * Copyright © 2016 Magevolve Ltd. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Me\Lff\Block;

use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;
use Me\Lff\Helper\Data as DataHelper;

/**
 * Lff block
 */
class Lff extends Template
{
    /**
     * @var DataHelper
     */
    protected $helper;

    /**
     * Sample constructor.
     *
     * @param Context $context
     * @param DataHelper $dataHelper
     * @param array $data
     */
    public function __construct(
        Context $context,
        DataHelper $dataHelper,
        array $data = []
    ) {
        $this->helper = $dataHelper;
        parent::__construct($context, $data);
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
