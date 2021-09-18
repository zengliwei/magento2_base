<?php
/**
 * Copyright (c) Zengliwei. All rights reserved.
 * Each source file in this distribution is licensed under OSL 3.0, see LICENSE for details.
 */

namespace CrazyCat\Base\Controller\Adminhtml;

use Magento\Backend\App\Action\Context;
use Magento\Backend\Helper\Data;
use Magento\Backend\Model\Auth;
use Magento\Backend\Model\Session;
use Magento\Backend\Model\UrlInterface;
use Magento\Backend\Model\View\Result\RedirectFactory;
use Magento\Framework\App\ActionFlag;
use Magento\Framework\App\Request\DataPersistorInterface;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\App\Response\RedirectInterface;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\App\ViewInterface;
use Magento\Framework\AuthorizationInterface;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Data\Form\FormKey\Validator;
use Magento\Framework\Filesystem;
use Magento\Framework\Locale\ResolverInterface;
use Magento\Framework\Message\ManagerInterface;
use Magento\Framework\ObjectManagerInterface;

/**
 * @author  Zengliwei <zengliwei@163.com>
 * @url https://github.com/zengliwei/magento2_base
 */
class SaveContext extends Context
{
    /**
     * @var DataPersistorInterface
     */
    protected $dataPersistor;

    /**
     * @var Filesystem
     */
    protected $filesystem;

    /**
     * @param DataPersistorInterface                    $dataPersistor
     * @param Filesystem                                $filesystem
     * @param RequestInterface                          $request
     * @param ResponseInterface                         $response
     * @param ObjectManagerInterface                    $objectManager
     * @param \Magento\Framework\Event\ManagerInterface $eventManager
     * @param \Magento\Framework\UrlInterface           $url
     * @param RedirectInterface                         $redirect
     * @param ActionFlag                                $actionFlag
     * @param ViewInterface                             $view
     * @param ManagerInterface                          $messageManager
     * @param RedirectFactory                           $resultRedirectFactory
     * @param ResultFactory                             $resultFactory
     * @param Session                                   $session
     * @param AuthorizationInterface                    $authorization
     * @param Auth                                      $auth
     * @param Data                                      $helper
     * @param UrlInterface                              $backendUrl
     * @param Validator                                 $formKeyValidator
     * @param ResolverInterface                         $localeResolver
     * @param bool                                      $canUseBaseUrl
     */
    public function __construct(
        DataPersistorInterface $dataPersistor,
        Filesystem $filesystem,
        RequestInterface $request,
        ResponseInterface $response,
        ObjectManagerInterface $objectManager,
        \Magento\Framework\Event\ManagerInterface $eventManager,
        \Magento\Framework\UrlInterface $url,
        RedirectInterface $redirect,
        ActionFlag $actionFlag,
        ViewInterface $view,
        ManagerInterface $messageManager,
        RedirectFactory $resultRedirectFactory,
        ResultFactory $resultFactory,
        Session $session,
        AuthorizationInterface $authorization,
        Auth $auth,
        Data $helper,
        UrlInterface $backendUrl,
        Validator $formKeyValidator,
        ResolverInterface $localeResolver,
        $canUseBaseUrl = false
    ) {
        parent::__construct(
            $request,
            $response,
            $objectManager,
            $eventManager,
            $url,
            $redirect,
            $actionFlag,
            $view,
            $messageManager,
            $resultRedirectFactory,
            $resultFactory,
            $session,
            $authorization,
            $auth,
            $helper,
            $backendUrl,
            $formKeyValidator,
            $localeResolver,
            $canUseBaseUrl
        );
        $this->dataPersistor = $dataPersistor;
        $this->filesystem = $filesystem;
    }

    /**
     * Get data persistor
     *
     * @return DataPersistorInterface
     */
    public function getDataPersistor()
    {
        return $this->dataPersistor;
    }

    /**
     * Get filesystem
     *
     * @return Filesystem
     */
    public function getFilesystem()
    {
        return $this->filesystem;
    }
}
