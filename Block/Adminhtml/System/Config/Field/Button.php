<?php
/*
 * Copyright (c) 2020 Zengliwei
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy of this software and associated
 * documentation files (the "Software"), to deal in the Software without restriction, including without limitation the
 * rights to use, copy, modify, merge, publish, distribute, sublicense, and/or sell copies of the Software, and to
 * permit persons to whom the Software is furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in all copies or substantial portions of the
 * Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE
 * WARRANTIES OF MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND NONINFINGEMENT. IN NO EVENT SHALL THE AUTHORS
 * OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR
 * OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.
 */

namespace Common\Base\Block\Adminhtml\System\Config\Field;

use Magento\Config\Block\System\Config\Form\Field;
use Magento\Framework\Data\Form\Element\AbstractElement;

/**
 * @package Common\Base
 * @author  Zengliwei <zengliwei@163.com>
 * @url https://github.com/zengliwei/magento2_base
 */
class Button extends Field
{
    protected function _getElementHtml(AbstractElement $element)
    {
        /* @var $buttonBlock \Magento\Backend\Block\Widget\Button */
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
                'label'   => __($fieldConfig['button_label']),
                'onclick' => 'setLocation("' . $buttonBlock->escapeUrl($url) . '")'
            ]
        )->toHtml();
    }
}
