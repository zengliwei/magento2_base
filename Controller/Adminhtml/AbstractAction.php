<?php
/**
 * Copyright (c) Zengliwei. All rights reserved.
 * Each source file in this distribution is licensed under OSL 3.0, see LICENSE for details.
 */

namespace CrazyCat\Base\Controller\Adminhtml;

use Magento\Backend\App\Action;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Model\AbstractModel;
use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

/**
 * @package CrazyCat\Base
 * @author  Zengliwei <zengliwei@163.com>
 * @url https://github.com/zengliwei/magento2_base
 */
abstract class AbstractAction extends Action
{
    /**
     * @param string $modelName
     * @return array
     * @throws NoSuchEntityException
     */
    protected function loadModel($modelName)
    {
        /** @var $model AbstractModel */
        /** @var $resourceModel AbstractDb */
        [$model, $resourceModel] = $this->getModels($modelName);

        if (($id = $this->getRequest()->getParam('id'))) {
            $resourceModel->load($model, $id);
            if (!$model->getId()) {
                throw new NoSuchEntityException();
            }
        }

        return [$model, $resourceModel];
    }

    /**
     * @param string $modelName
     * @return array
     */
    protected function getModels($modelName)
    {
        $model = $this->_objectManager->create($modelName);
        $resourceModel = $this->_objectManager->create($model->getResourceName());

        return [$model, $resourceModel];
    }
}
