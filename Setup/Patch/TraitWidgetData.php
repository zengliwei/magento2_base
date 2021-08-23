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

namespace CrazyCat\Base\Setup\Patch;

use Magento\Framework\App\Area;
use Magento\Framework\Exception\AlreadyExistsException;
use Magento\Widget\Model\ResourceModel\Widget\Instance as ResourceWidget;
use Magento\Widget\Model\Widget\Instance as Widget;

/**
 * @package CrazyCat\Base
 * @author  Zengliwei <zengliwei@163.com>
 * @url https://github.com/zengliwei/magento2_base
 */
trait TraitWidgetData
{
    /**
     * @return ResourceWidget
     */
    private function getResourceWidget()
    {
        return $this->objectManager->get(ResourceWidget::class);
    }

    /**
     * @param array $data
     * @return Widget
     * @throws AlreadyExistsException
     */
    private function createWidget(array $data)
    {
        return $this->state->emulateAreaCode(
            Area::AREA_FRONTEND,
            function () use ($data) {
                $widget = $this->objectManager->create(Widget::class);
                $this->getResourceWidget()->save($widget->setData($data));
                return $widget;
            }
        );
    }
}
