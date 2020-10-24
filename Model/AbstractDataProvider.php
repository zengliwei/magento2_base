<?php

namespace Common\Base\Model;

use Magento\Framework\App\ObjectManager;
use Magento\Framework\App\Request\DataPersistorInterface;
use Magento\Framework\App\ResourceConnection\SourceProviderInterface;
use Magento\Ui\DataProvider\Modifier\PoolInterface;
use Magento\Ui\DataProvider\ModifierPoolDataProvider;

abstract class AbstractDataProvider extends ModifierPoolDataProvider
{
    /**
     * @var SourceProviderInterface
     */
    protected $collection;

    /**
     * @var DataPersistorInterface
     */
    protected $dataPersistor;

    /**
     * @var array
     */
    protected $loadedData;

    /**
     * @param string                 $name
     * @param string                 $primaryFieldName
     * @param string                 $requestFieldName
     * @param DataPersistorInterface $dataPersistor
     * @param array                  $meta
     * @param array                  $data
     * @param PoolInterface|null     $pool
     */
    public function __construct(
        $name,
        $primaryFieldName,
        $requestFieldName,
        DataPersistorInterface $dataPersistor,
        array $meta = [],
        array $data = [],
        PoolInterface $pool = null
    ) {
        parent::__construct($name, $primaryFieldName, $requestFieldName, $meta, $data, $pool);

        $this->dataPersistor = $dataPersistor;

        $this->init();
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
     * @return array|null
     */
    public function getData()
    {
        if ($this->loadedData !== null) {
            return $this->loadedData;
        }

        $models = $this->collection->getItems();
        foreach ($models as $model) {
            $this->loadedData[$model->getId()] = ['data' => $model->getData()];
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
     * @return void
     */
    abstract protected function init();
}
