<?php

namespace Common\Base\Model\ResourceModel;

use Magento\Framework\Data\Collection\Db\FetchStrategyInterface;
use Magento\Framework\Data\Collection\EntityFactoryInterface;
use Magento\Framework\DB\Adapter\AdapterInterface;
use Magento\Framework\EntityManager\MetadataPool;
use Magento\Framework\Event\ManagerInterface;
use Magento\Framework\Model\ResourceModel\Db\AbstractDb;
use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;
use Magento\Store\Model\Store;
use Magento\Store\Model\StoreManagerInterface;
use Psr\Log\LoggerInterface;

abstract class AbstractStoreCollection extends AbstractCollection
{
    /**
     * @var MetadataPool
     */
    protected $metadataPool;

    /**
     * @var StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var string
     */
    protected $storeTable;

    /**
     * @param StoreManagerInterface  $storeManager
     * @param EntityFactoryInterface $entityFactory
     * @param LoggerInterface        $logger
     * @param FetchStrategyInterface $fetchStrategy
     * @param ManagerInterface       $eventManager
     * @param AdapterInterface|null  $connection
     * @param AbstractDb|null        $resource
     */
    public function __construct(
        StoreManagerInterface $storeManager,
        EntityFactoryInterface $entityFactory,
        LoggerInterface $logger,
        FetchStrategyInterface $fetchStrategy,
        ManagerInterface $eventManager,
        AdapterInterface $connection = null,
        AbstractDb $resource = null
    ) {
        $this->storeManager = $storeManager;

        parent::__construct($entityFactory, $logger, $fetchStrategy, $eventManager, $connection, $resource);
    }

    /**
     * @inheritDoc
     */
    protected function _afterLoad()
    {
        parent::_afterLoad();
        foreach ($this->_items as $item) {
            if ($item->hasData('store_ids') && is_string($item->getData('store_ids'))) {
                $item->setData('store_ids', explode(',', $item->getData('store_ids')));
            }
        }
        return $this;
    }

    /**
     * @param int|int[]|Store
     * @param bool $withAdmin
     * @return AbstractStoreCollection
     */
    public function addStoreFilter($store, $withAdmin = true)
    {
        $storeIds = $store;
        if ($store instanceof Store) {
            $storeIds = [$store->getId()];
        }
        if (!is_array($store)) {
            $storeIds = [$store];
        }
        if ($withAdmin) {
            $storeIds[] = Store::DEFAULT_STORE_ID;
        }

        $fields = [];
        $conditions = [];
        foreach ($storeIds as $storeId) {
            $fields[] = 'store_ids';
            $conditions[] = ['finset' => $storeId];
        }
        return $this->addFieldToFilter($fields, $conditions);
    }
}
