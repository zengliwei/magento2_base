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
 * @package CrazyCat\Base
 * @author  Zengliwei <zengliwei@163.com>
 * @url https://github.com/zengliwei/magento2_base
 */
abstract class AbstractStoreModel extends AbstractModel
{
    public const STORE_ID = 'store_id';
    public const STORE_IDS = 'store_ids';

    protected StoreManagerInterface $storeManager;

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
     * @return bool
     * @throws NoSuchEntityException
     */
    public function isInStore()
    {
        return (in_array($this->getStoreId(), $this->getData(self::STORE_IDS))
            || in_array(Store::DEFAULT_STORE_ID, $this->getData(self::STORE_IDS)));
    }

    /**
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
