<?php

namespace Common\Base\Controller\Adminhtml;

use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Exception\FileSystemException;
use Magento\Framework\File\Uploader;
use Magento\Framework\File\UploaderFactory;
use Magento\Framework\Filesystem;
use Magento\Store\Model\StoreManagerInterface;

abstract class AbstractUploadAction extends AbstractAjaxAction
{
    /**
     * @var Filesystem\Directory\WriteInterface
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
     * @param string $folder Folder path related to the media directory
     * @param array  $allowedExtensions
     * @return array|bool
     * @throws \Exception
     */
    protected function save($folder, $allowedExtensions = [])
    {
        $fieldName = $this->getRequest()->getParam('param_name');

        /* @var Uploader $uploader */
        $uploader = $this->uploaderFactory->create(['fileId' => $fieldName]);
        $result = $uploader->setAllowRenameFiles(true)
            ->setFilesDispersion(false)
            ->setAllowedExtensions($allowedExtensions)
            ->save($this->mediaDirectory->getAbsolutePath($folder));

        unset($result['path']);
        $result['url'] = $this->storeManager->getStore()->getBaseUrl(DirectoryList::MEDIA)
            . $folder . '/' . $result['file'];

        return $result;
    }
}
