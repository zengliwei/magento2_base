<?php
/**
 * Copyright (c) Zengliwei. All rights reserved.
 * Each source file in this distribution is licensed under OSL 3.0, see LICENSE for details.
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
