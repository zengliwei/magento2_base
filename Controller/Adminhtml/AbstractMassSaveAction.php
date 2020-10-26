<?php

namespace Common\Base\Controller\Adminhtml;

use Magento\Framework\Model\AbstractModel;
use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

abstract class AbstractMassSaveAction extends AbstractMassAction
{
    protected function save($modelName)
    {
        $messages = [];
        $error = false;

        if ($this->getRequest()->getParam('isAjax')) {
            $postItems = $this->getRequest()->getParam('items', []);
            if (!count($postItems)) {
                $messages[] = __('Please correct the data sent.');
                $error = true;
            } else {
                /* @var $model AbstractModel */
                /* @var $resourceModel AbstractDb */
                [$model, $resourceModel] = $this->getModels($modelName);
                foreach ($postItems as $id => $data) {
                    try {
                        $resourceModel->load($model, $id);
                        $resourceModel->save($model->addData($data));
                    } catch (\Exception $e) {
                        $messages[] = '[ID: ' . $model->getId() . '] ' . $e->getMessage();
                        $error = true;
                    }
                }
            }
        }

        return $this->processResult($messages, $error);
    }
}
