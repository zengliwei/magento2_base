<?php
/**
 * Copyright (c) Zengliwei. All rights reserved.
 * Each source file in this distribution is licensed under OSL 3.0, see LICENSE for details.
 */

namespace CrazyCat\Base\Model\ResourceModel;

use Magento\Framework\Model\AbstractModel;
use Magento\Framework\Model\ResourceModel\Db\AbstractDb;
use Magento\Store\Model\Store;

/**
 * @package CrazyCat\Base
 * @author  Zengliwei <zengliwei@163.com>
 * @url https://github.com/zengliwei/magento2_base
 */
abstract class AbstractStoreSeparatedResource extends AbstractDb
{
    protected string $storeTable;
    protected string $storeTableKey;
    protected array $storeFields;

    /**
     * @inheritDoc
     */
    protected function _init($mainTable, $idFieldName)
    {
        parent::_init($mainTable, $idFieldName);

        $this->storeFields = [];
        $conn = $this->getConnection();
        $skippedFields = [$this->storeTableKey, 'store_id'];
        foreach (array_keys($conn->describeTable($conn->getTableName($this->storeTable))) as $field) {
            if (!in_array($field, $skippedFields)) {
                $this->storeFields[] = $field;
            }
        }
    }

    /**
     * @inheritDoc
     */
    protected function _getLoadSelect($field, $value, $object)
    {
        $conn = $this->getConnection();
        $table = $this->getMainTable();
        $select = $conn->select()
            ->from($table)
            ->joinLeft(
                ['sd' => $conn->getTableName($this->storeTable)],
                'sd.' . $this->storeTableKey . ' = ' . $table . '.id AND sd.store_id = ' . Store::DEFAULT_STORE_ID
            )
            ->where($conn->quoteIdentifier($table . '.' . $field) . ' = ?', $value);

        if ($object->getStoreId() != Store::DEFAULT_STORE_ID) {
            $cols = [];
            foreach ($this->storeFields as $field) {
                $cols[$field] = 'IF (s.' . $field . ' IS NULL, sd.' . $field . ', s.' . $field . ')';
            }
            $select->joinLeft(
                ['s' => $conn->getTableName($this->storeTable)],
                's.' . $this->storeTableKey . ' = ' . $table . '.id AND s.store_id = ' . $object->getStoreId(),
                $cols
            );
        }

        return $select;
    }

    /**
     * @inheritDoc
     */
    protected function _afterSave(AbstractModel $object)
    {
        $data = [
            $this->storeTableKey => $object->getId(),
            'store_id'           => $object->getStoreId()
        ];
        foreach ($this->storeFields as $field) {
            $data[$field] = $object->getDataByKey($field);
        }

        $conn = $this->getConnection();
        $conn->insertOnDuplicate(
            $conn->getTableName($this->storeTable),
            $data,
            $this->storeFields
        );
        return parent::_afterSave($object);
    }

    /**
     * @inheritDoc
     */
    protected function _afterDelete(AbstractModel $object)
    {
        $conn = $this->getConnection();
        $conn->delete(
            $conn->getTableName($this->storeTable),
            [$this->storeTableKey . ' = ?' => $object->getOrigData('id')]
        );
        return parent::_afterDelete($object);
    }

    /**
     * @retrun string
     */
    public function getStoreTable()
    {
        return $this->storeTable;
    }

    /**
     * @retrun string
     */
    public function getStoreTableKey()
    {
        return $this->storeTableKey;
    }

    /**
     * @retrun array
     */
    public function getStoreFields()
    {
        return $this->storeFields;
    }
}
