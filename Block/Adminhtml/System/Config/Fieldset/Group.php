<?php
/**
 * Copyright © 2016 Magevolve Ltd. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Me\Lff\Block\Adminhtml\System\Config\Fieldset;

use Magento\Config\Block\System\Config\Form\Fieldset;
use Magento\Framework\Data\Form\Element\AbstractElement;

class Group extends Fieldset
{
    /**
     * Return header comment part of html for field set
     *
     * @param AbstractElement $element
     * @return string
     */
    protected function _getHeaderCommentHtml($element)
    {
        if (!$element->getComment()) {
            return parent::_getHeaderCommentHtml($element);
        }

        $link = '<a target="_blank" href="' . $this->getWidgetUrl() . '">' . __('Widget') . '</a>';
        $comment = str_replace('Widget', $link, $element->getComment());

        $html = '<div class="comment">' . $comment . ' </div>';

        return $html;
    }

    /**
     * Retrieve Widget url in admin
     *
     * @return  string
     */
    private function getWidgetUrl()
    {
        return $this->getUrl(
            'adminhtml/widget_instance/index',
            []
        );
    }
}
