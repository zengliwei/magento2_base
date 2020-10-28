<?php


namespace Common\Base\Controller\Adminhtml;

use Magento\Framework\App\Action\HttpPostActionInterface;
use Magento\Framework\Controller\Result\Json;
use Magento\Framework\Controller\ResultFactory;

abstract class AbstractAjaxAction extends AbstractAction implements HttpPostActionInterface
{
    /**
     * @param array $result
     * @return Json
     */
    protected function processResult($result)
    {
        /* @var Json $resultJson */
        $resultJson = $this->resultFactory->create(ResultFactory::TYPE_JSON);
        return $resultJson->setData($result);
    }
}
