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

namespace Common\Base\Controller\Adminhtml;

use Magento\Backend\App\Action\Context;
use Magento\Backend\Helper\Data;
use Magento\Backend\Model\Auth;
use Magento\Backend\Model\Session;
use Magento\Backend\Model\UrlInterface;
use Magento\Backend\Model\View\Result\RedirectFactory;
use Magento\Framework\App\ActionFlag;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\App\Response\RedirectInterface;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\App\ViewInterface;
use Magento\Framework\AuthorizationInterface;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Data\Form\FormKey\Validator;
use Magento\Framework\File\UploaderFactory;
use Magento\Framework\Filesystem;
use Magento\Framework\Locale\ResolverInterface;
use Magento\Framework\Message\ManagerInterface;
use Magento\Framework\ObjectManagerInterface;
use Magento\Store\Model\StoreManagerInterface;

/**
 * @package Common\Base
 * @author  Zengliwei <zengliwei@163.com>
 * @url https://github.com/zengliwei/magento2_base
 */
class UploadContext extends Context
{
    protected Filesystem $filesystem;
    protected StoreManagerInterface $storeManager;
    protected UploaderFactory $uploaderFactory;

    public function __construct(
        Filesystem $filesystem,
        StoreManagerInterface $storeManager,
        UploaderFactory $uploaderFactory,
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
        $this->filesystem = $filesystem;
        $this->storeManager = $storeManager;
        $this->uploaderFactory = $uploaderFactory;
    }

    /**
     * @return Filesystem
     */
    public function getFilesystem()
    {
        return $this->filesystem;
    }

    /**
     * @return StoreManagerInterface
     */
    public function getStoreManager()
    {
        return $this->storeManager;
    }

    /**
     * @return UploaderFactory
     */
    public function getUploaderFactory()
    {
        return $this->uploaderFactory;
    }
}
