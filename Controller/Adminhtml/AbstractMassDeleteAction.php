<?php
/**
 * Copyright (c) Zengliwei. All rights reserved.
 * Each source file in this distribution is licensed under OSL 3.0, see LICENSE for details.
 */

namespace CrazyCat\Base\Controller\Adminhtml;

use Exception;
use Magento\Backend\Model\View\Result\Redirect;
use Magento\Framework\App\Action\HttpPostActionInterface;
use Magento\Framework\Controller\Result\Json;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Model\AbstractModel;
use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

/**
 * @author  Zengliwei <zengliwei@163.com>
 * @url https://github.com/zengliwei/magento2_base
 */
abstract class AbstractMassDeleteAction extends AbstractAction implements HttpPostActionInterface
{
    /**
     * Delete record of given model by request ID
     *
     * @param string $modelName
     * @return Json
     * @throws Exception
     */
    protected function delete($modelName)
    {
        $messages = [];

        if ($this->getRequest()->getParam('isAjax')) {
            $postItems = $this->getRequest()->getParam('items', []);
            if (!count($postItems)) {
                $messages[] = __('Please correct the data sent.');
                $error = true;
            } else {
                /** @var $model AbstractModel */
                /** @var $resourceModel AbstractDb */
                [$model, $resourceModel] = $this->getModels($modelName);
                foreach ($postItems as $id => $data) {
                    try {
                        $resourceModel->delete($model->setId($id));
                    } catch (Exception $e) {
                        $messages[] = '[ID: ' . $model->getId() . '] ' . $e->getMessage();
                    }
                }
            }
        }

        /** @var $resultRedirect Redirect */
        $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
        return $resultRedirect->setPath('customs-duty/duty');
    }
}
