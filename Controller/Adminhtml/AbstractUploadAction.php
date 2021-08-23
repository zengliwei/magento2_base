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

namespace CrazyCat\Base\Controller\Adminhtml;

use Exception;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Exception\FileSystemException;
use Magento\Framework\File\Uploader;
use Magento\Framework\File\UploaderFactory;
use Magento\Framework\Filesystem\Directory\WriteInterface;
use Magento\Store\Model\StoreManagerInterface;

/**
 * @package CrazyCat\Base
 * @author  Zengliwei <zengliwei@163.com>
 * @url https://github.com/zengliwei/magento2_base
 */
abstract class AbstractUploadAction extends AbstractAjaxAction
{
    /**
     * @var WriteInterface
     */
    protected WriteInterface $mediaDirectory;

    /**
     * @var UploaderFactory
     */
    protected UploaderFactory $uploaderFactory;

    /**
     * @var StoreManagerInterface
     */
    protected StoreManagerInterface $storeManager;

    /**
     * @param UploadContext $context
     * @throws FileSystemException
     */
    public function __construct(UploadContext $context)
    {
        parent::__construct($context);

        $this->mediaDirectory = $context->getFilesystem()->getDirectoryWrite(DirectoryList::MEDIA);
        $this->uploaderFactory = $context->getUploaderFactory();
        $this->storeManager = $context->getStoreManager();
    }

    /**
     * @param string $folder  Folder path related to the media directory
     * @param array  $allowedExtensions
     * @param bool   $keepPath
     * @return array|bool
     * @throws Exception
     */
    protected function save($folder, $allowedExtensions = [], $keepPath = false)
    {
        $fieldName = $this->getRequest()->getParam('param_name');

        /* @var Uploader $uploader */
        $uploader = $this->uploaderFactory->create(['fileId' => $fieldName]);
        $result = $uploader->setAllowRenameFiles(true)
            ->setFilesDispersion(false)
            ->setAllowedExtensions($allowedExtensions)
            ->save($this->mediaDirectory->getAbsolutePath($folder));

        if (!$keepPath) {
            unset($result['path']);
        }

        $result['url'] = $this->storeManager->getStore()->getBaseUrl(DirectoryList::MEDIA)
            . $folder . '/' . $result['file'];

        if (!empty($result['file'])) {
            $result['name'] = $result['file'];
        }

        return $result;
    }
}
