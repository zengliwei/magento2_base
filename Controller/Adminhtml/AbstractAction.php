<?php

namespace Common\Base\Controller\Adminhtml;

use Magento\Backend\App\Action;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Model\AbstractModel;
use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

abstract class AbstractAction extends Action
{
    protected function loadModel($modelName, $resourceModelName)
    {
        /* @var $model AbstractModel */
        /* @var $resourceModel AbstractDb */
        $model = $this->_objectManager->create($modelName);
        $resourceModel = $this->_objectManager->create($resourceModelName);

        if (($id = $this->getRequest()->getParam('id'))) {
            $resourceModel->load($model, $id);
            if (!$model->getId()) {
                throw new NoSuchEntityException();
            }
        }

        return [$model, $resourceModel];
    }
}
