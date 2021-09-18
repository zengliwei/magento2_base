<?php
/**
 * Copyright (c) Zengliwei. All rights reserved.
 * Each source file in this distribution is licensed under OSL 3.0, see LICENSE for details.
 */

namespace CrazyCat\Base\Block\Adminhtml\System\Config\Field;

use Magento\Config\Block\System\Config\Form\Field;
use Magento\Framework\Data\Form\Element\AbstractElement;

/**
 * @author  Zengliwei <zengliwei@163.com>
 * @url https://github.com/zengliwei/magento2_base
 */
class Button extends Field
{
    /**
     * @inheritDoc
     */
    protected function _getElementHtml(AbstractElement $element)
    {
        /** @var $buttonBlock \Magento\Backend\Block\Widget\Button */
        $buttonBlock = $this->getLayout()->createBlock(\Magento\Backend\Block\Widget\Button::class);

        $fieldConfig = $element->getDataByKey('field_config');
        $url = $buttonBlock->getUrl(
            $fieldConfig['button_url'],
            [
                'website' => $buttonBlock->getRequest()->getParam('website'),
                'store'   => $buttonBlock->getRequest()->getParam('store')
            ]
        );
        return $buttonBlock->setData(
            [
                'label' => __($fieldConfig['button_label']),
                'onclick' => 'setLocation("' . $buttonBlock->escapeUrl($url) . '")'
            ]
        )->toHtml();
    }
}
