<?php
/**
 * Copyright (c) Zengliwei. All rights reserved.
 * Each source file in this distribution is licensed under OSL 3.0, see LICENSE for details.
 */

namespace CrazyCat\Base\Model;

use Magento\Framework\Data\Collection\AbstractDb;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Model\AbstractModel;
use Magento\Framework\Model\Context;
use Magento\Framework\Model\ResourceModel\AbstractResource;
use Magento\Framework\Registry;
use Magento\Store\Model\Store;
use Magento\Store\Model\StoreManagerInterface;

/**
 * @author  Zengliwei <zengliwei@163.com>
 * @url https://github.com/zengliwei/magento2_base
 */
abstract class AbstractStoreModel extends AbstractModel
{
    public const STORE_ID = 'store_id';
    public const STORE_IDS = 'store_ids';

    /**
     * @var StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @param StoreManagerInterface $storeManager
     * @param Context               $context
     * @param Registry              $registry
     * @param AbstractResource|null $resource
     * @param AbstractDb|null       $resourceCollection
     * @param array                 $data
     */
    public function __construct(
        StoreManagerInterface $storeManager,
        Context $context,
        Registry $registry,
        AbstractResource $resource = null,
        AbstractDb $resourceCollection = null,
        array $data = []
    ) {
        $this->storeManager = $storeManager;
        parent::__construct($context, $registry, $resource, $resourceCollection, $data);
    }

    /**
     * @inheritDoc
     */
    public function afterLoad()
    {
        if ($this->hasData(self::STORE_IDS) && is_string($this->getData(self::STORE_IDS))) {
            $this->setData(self::STORE_IDS, explode(',', $this->getData(self::STORE_IDS)));
        }
        return parent::afterLoad();
    }

    /**
     * @inheritDoc
     */
    public function beforeSave()
    {
        if ($this->hasData(self::STORE_IDS) && is_array($this->getData(self::STORE_IDS))) {
            $this->setData(self::STORE_IDS, implode(',', $this->getData(self::STORE_IDS)));
        }
        return parent::beforeSave();
    }

    /**
     * Check whether is in store
     *
     * @return bool
     * @throws NoSuchEntityException
     */
    public function isInStore()
    {
        return (in_array($this->getStoreId(), $this->getData(self::STORE_IDS))
            || in_array(Store::DEFAULT_STORE_ID, $this->getData(self::STORE_IDS)));
    }

    /**
     * Get store ID
     *
     * @return int
     * @throws NoSuchEntityException
     */
    public function getStoreId()
    {
        if ($this->hasData(self::STORE_ID)) {
            return (int)$this->getData(self::STORE_ID);
        }
        return (int)$this->storeManager->getStore()->getId();
    }
}
