<?php
/**
 * Copyright (c) Zengliwei. All rights reserved.
 * Each source file in this distribution is licensed under OSL 3.0, see LICENSE for details.
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
 * @author  Zengliwei <zengliwei@163.com>
 * @url https://github.com/zengliwei/magento2_base
 */
abstract class AbstractUploadAction extends AbstractAjaxAction
{
    /**
     * @var WriteInterface
     */
    protected $mediaDirectory;

    /**
     * @var UploaderFactory
     */
    protected $uploaderFactory;

    /**
     * @var StoreManagerInterface
     */
    protected $storeManager;

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
     * Save file
     *
     * @param string $folder  Folder path related to the media directory
     * @param array  $allowedExtensions
     * @param bool   $keepPath
     * @return array|bool
     * @throws Exception
     */
    protected function save($folder, $allowedExtensions = [], $keepPath = false)
    {
        $fieldName = $this->getRequest()->getParam('param_name');

        /** @var Uploader $uploader */
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
