<?php
/**
 * Copyright (c) Zengliwei. All rights reserved.
 * Each source file in this distribution is licensed under OSL 3.0, see LICENSE for details.
 */

namespace CrazyCat\Base\Block;

use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Filesystem\DriverInterface;
use Magento\Framework\View\Element\Template;

/**
 * @author  Zengliwei <zengliwei@163.com>
 * @url https://github.com/zengliwei/magento2_base
 */
abstract class MediaBlock extends Template
{
    /**
     * @var string
     */
    protected $defaultMediaFileId = 'CrazyCat_Base::images/default.jpg';

    /**
     * @var string
     */
    protected $mediaFolder = 'base';

    /**
     * @var DriverInterface
     */
    protected $driver;

    /**
     * @param DriverInterface  $driver
     * @param Template\Context $context
     * @param array            $data
     */
    public function __construct(
        DriverInterface $driver,
        Template\Context $context,
        array $data = []
    ) {
        $this->driver = $driver;
        parent::__construct($context, $data);
    }

    /**
     * Get media URL
     *
     * @param string      $mediaFile
     * @param string|null $mediaFolder
     * @return string
     * @throws NoSuchEntityException
     */
    public function getMediaUrl($mediaFile, $mediaFolder = null)
    {
        if ($mediaFile) {
            $mediaFolder = $mediaFolder ?: $this->mediaFolder;
            $mediaDirectory = $this->_filesystem->getDirectoryRead(DirectoryList::MEDIA);
            if ($this->driver->isFile($mediaDirectory->getAbsolutePath($mediaFolder . '/' . $mediaFile))) {
                return $this->_storeManager->getStore()->getBaseUrl(DirectoryList::MEDIA) .
                    $mediaFolder . '/' . $mediaFile;
            }
        }
        return $this->getViewFileUrl($this->defaultMediaFileId);
    }
}
