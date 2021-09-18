<?php
/**
 * Copyright (c) Zengliwei. All rights reserved.
 * Each source file in this distribution is licensed under OSL 3.0, see LICENSE for details.
 */

namespace CrazyCat\Base\Model\ResourceModel;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;
use Magento\Store\Model\Store;

/**
 * @package CrazyCat\Base
 * @author  Zengliwei <zengliwei@163.com>
 * @url https://github.com/zengliwei/magento2_base
 */
abstract class AbstractStoreSeparatedCollection extends AbstractCollection
{
    protected int $storeId = Store::DEFAULT_STORE_ID;

    /**
     * @return int
     */
    public function getStoreId()
    {
        return $this->storeId;
    }

    /**
     * @param int $storeId
     * @return $this
     */
    public function setStoreId($storeId)
    {
        $this->storeId = $storeId;
        return $this;
    }

    protected function _initSelect()
    {
        parent::_initSelect();

        /** @var $resource AbstractStoreSeparatedResource */
        $resource = $this->getResource();

        $conn = $this->getConnection();
        $this->getSelect()
            ->joinLeft(
                ['sd' => $conn->getTableName($resource->getStoreTable())],
                'sd.' . $resource->getStoreTableKey() . ' = main_table.id AND sd.store_id = ' . Store::DEFAULT_STORE_ID,
                null
            );

        foreach ($resource->getStoreFields() as $field) {
            $this->addExpressionFieldToSelect(
                $field,
                'IF ({{store_value}} IS NULL, {{default_value}}, {{store_value}})',
                ['store_value' => 's.' . $field, 'default_value' => 'sd.' . $field]
            );
            $this->addFilterToMap($field, 'IF (s.' . $field . ' IS NULL, sd.' . $field . ', s.' . $field . ')');
        }

        return $this;
    }

    /**
     * @inheritDoc
     */
    protected function _translateCondition($field, $condition)
    {
        $field = isset($this->_map['fields'][$field])
            ? $this->_getMappedField($field)
            : $this->getConnection()->quoteIdentifier($field);
        return $this->_getConditionSql($field, $condition);
    }

    /**
     * @inheritDoc
     */
    protected function _beforeLoad()
    {
        if (!$this->isLoaded()) {
            /** @var $resource AbstractStoreSeparatedResource */
            $resource = $this->getResource();
            $this->getSelect()->joinLeft(
                ['s' => $this->getConnection()->getTableName($resource->getStoreTable())],
                's.' . $resource->getStoreTableKey() . ' = main_table.id AND s.store_id = ' . $this->storeId,
                null
            );
        }
        return parent::_beforeLoad();
    }
}
