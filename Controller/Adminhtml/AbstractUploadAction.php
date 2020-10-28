<?php

namespace Common\Base\Controller\Adminhtml;

use Magento\Backend\App\Action\Context;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Controller\Result\JsonFactory;
use Magento\Framework\Exception\FileSystemException;
use Magento\Framework\Filesystem;
use Magento\Framework\Registry;
use Magento\MediaStorage\Model\File\Uploader;
use Magento\MediaStorage\Model\File\UploaderFactory;
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
     * @param Filesystem            $filesystem
     * @param UploaderFactory       $uploaderFactory
     * @param StoreManagerInterface $storeManager
     * @param JsonFactory           $resultJsonFactory
     * @param Context               $context
     * @param Registry              $coreRegistry
     * @throws FileSystemException
     */
    public function __construct(
        Filesystem $filesystem,
        UploaderFactory $uploaderFactory,
        StoreManagerInterface $storeManager,
        JsonFactory $resultJsonFactory,
        Context $context,
        Registry $coreRegistry
    ) {
        $this->mediaDirectory = $filesystem->getDirectoryWrite(DirectoryList::MEDIA);
        $this->uploaderFactory = $uploaderFactory;
        $this->storeManager = $storeManager;

        parent::__construct($resultJsonFactory, $context, $coreRegistry);
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
