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
     * {@inheritDoc}
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
     * {@inheritDoc}
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
     * {@inheritDoc}
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
