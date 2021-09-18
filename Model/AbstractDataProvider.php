<?php
/**
 * Copyright (c) Zengliwei. All rights reserved.
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

namespace CrazyCat\Base\Model;

use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\App\ObjectManager;
use Magento\Framework\App\Request\DataPersistorInterface;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Exception\FileSystemException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Filesystem;
use Magento\Framework\Filesystem\Directory\WriteInterface;
use Magento\Framework\ObjectManagerInterface;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Ui\DataProvider\Modifier\PoolInterface;
use Magento\Ui\DataProvider\ModifierPoolDataProvider;

/**
 * @package CrazyCat\Base
 * @author  Zengliwei <zengliwei@163.com>
 * @url https://github.com/zengliwei/magento2_base
 */
abstract class AbstractDataProvider extends ModifierPoolDataProvider
{
    protected DataPersistorInterface $dataPersistor;
    protected WriteInterface $mediaDirectory;
    protected ObjectManagerInterface $objectManager;
    protected RequestInterface $request;
    protected StoreManagerInterface $storeManager;

    protected array $mediaFields = [];
    protected string $mediaFolder = '';
    protected ?string $baseMediaUrl = null;
    protected ?array $loadedData = null;
    protected string $persistKey;
    protected string $requestScopeFieldName = 'store';

    /**
     * @param string                 $name
     * @param string                 $primaryFieldName
     * @param string                 $requestFieldName
     * @param DataPersistorInterface $dataPersistor
     * @param StoreManagerInterface  $storeManager
     * @param Filesystem             $filesystem
     * @param ObjectManagerInterface $objectManager
     * @param array                  $meta
     * @param array                  $data
     * @param PoolInterface|null     $pool
     * @throws FileSystemException
     */
    public function __construct(
        $name,
        $primaryFieldName,
        $requestFieldName,
        RequestInterface $request,
        DataPersistorInterface $dataPersistor,
        StoreManagerInterface $storeManager,
        Filesystem $filesystem,
        ObjectManagerInterface $objectManager,
        array $meta = [],
        array $data = [],
        PoolInterface $pool = null
    ) {
        $this->dataPersistor = $dataPersistor;
        $this->objectManager = $objectManager;
        $this->request = $request;
        $this->storeManager = $storeManager;
        parent::__construct($name, $primaryFieldName, $requestFieldName, $meta, $data, $pool);

        $this->mediaDirectory = $filesystem->getDirectoryWrite(DirectoryList::MEDIA);

        $this->init();
    }

    /**
     * @return void
     */
    abstract protected function init();

    /**
     * @inheritDoc
     */
    public function getMeta()
    {
        $this->meta['general'] = [
            'children' => [
                $this->requestScopeFieldName => [
                    'arguments' => [
                        'data' => [
                            'config' => [
                                'componentType' => 'field',
                                'formElement'   => 'hidden',
                                'dataType'      => 'int',
                                'source'        => 'data',
                                'value'         => $this->request->getParam($this->requestScopeFieldName, null)
                            ]
                        ]
                    ]
                ]
            ]
        ];
        return parent::getMeta();
    }

    /**
     * @return array|null
     * @throws NoSuchEntityException
     */
    public function getData()
    {
        if ($this->loadedData !== null) {
            return $this->loadedData;
        }

        $this->loadedData = [];
        $models = $this->collection->getItems();
        foreach ($models as $model) {
            $data = $this->prepareMediaFields($model->getData());
            $this->loadedData[$model->getId()] = ['data' => $data];
        }

        $data = $this->dataPersistor->get($this->persistKey);
        if (!empty($data)) {
            $model = $this->collection->getNewEmptyItem();
            $model->setData($data);
            $this->loadedData[$model->getId()] = ['data' => $model->getData()];
            $this->dataPersistor->clear($this->persistKey);
        }

        return $this->loadedData;
    }

    /**
     * @param string $collectionName
     * @return void
     */
    protected function initCollection(string $collectionName): void
    {
        $this->collection = ObjectManager::getInstance()->create($collectionName);
    }

    /**
     * @param array $data
     * @return array
     * @throws NoSuchEntityException
     */
    protected function prepareMediaFields($data)
    {
        foreach ($this->mediaFields as $mediaField) {
            if (!empty($data[$mediaField]) && !is_array($data[$mediaField])) {
                $data[$mediaField] = $this->prepareFileData(
                    $data,
                    $mediaField,
                    $this->mediaFolder,
                    null
                );
            }
        }
        return $data;
    }

    /**
     * @param array       $data
     * @param string      $field
     * @param string      $folder
     * @param string|null $type
     * @return array|null
     * @throws NoSuchEntityException
     */
    protected function prepareFileData($data, $field, $folder, $type = null)
    {
        $filePath = $this->mediaDirectory->getAbsolutePath($folder . '/' . $data[$field]);
        if (is_file($filePath)) {
            return [
                [
                    'name' => $data[$field],
                    'file' => $data[$field],
                    'url'  => $this->getBaseMediaUrl() . $folder . '/' . $data[$field],
                    'size' => filesize($filePath),
                    'type' => $type ?: getImageSize($filePath)['mime']
                ]
            ];
        }
        return null;
    }

    /**
     * @return string
     * @throws NoSuchEntityException
     */
    protected function getBaseMediaUrl()
    {
        if ($this->baseMediaUrl === null) {
            $this->baseMediaUrl = $this->storeManager->getStore()->getBaseUrl(DirectoryList::MEDIA);
        }
        return $this->baseMediaUrl;
    }
}
