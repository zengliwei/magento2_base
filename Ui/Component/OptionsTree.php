<?php
/**
 * Copyright (c) Zengliwei. All rights reserved.
 * Each source file in this distribution is licensed under OSL 3.0, see LICENSE for details.
 */

namespace CrazyCat\Base\Ui\Component;

use Magento\Framework\Model\AbstractModel;
use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

/**
 * @package CrazyCat\Base
 * @author  Zengliwei <zengliwei@163.com>
 * @url https://github.com/zengliwei/magento2_base
 */
trait OptionsTree
{
    protected string $fieldParentId = 'parent_id';
    protected string $fieldTitle = 'title';
    protected string $fieldValue = 'id';
    protected ?string $filterField = null;

    /**
     * @param AbstractCollection $collection
     * @return array
     */
    protected function getOptionsTree($collection)
    {
        $itemGroups = [];
        foreach ($collection as $item) {
            /** @var $item AbstractModel */
            if (!isset($itemGroups[$item->getDataByKey($this->fieldParentId)])) {
                $itemGroups[$item->getDataByKey($this->fieldParentId)] = [];
            }
            $itemGroups[$item->getDataByKey($this->fieldParentId)][] = $item;
        }
        $options = $this->collectOptions($itemGroups);
        if ($this->filterField === null) {
            return array_merge([['label' => '[ root ]', 'value' => '0']], $options);
        }

        $optionGroups = [];
        foreach ($options as $option) {
            if (!isset($optionGroups[$option[$this->filterField]])) {
                $optionGroups[$option[$this->filterField]] = [];
            }
            $optionGroups[$option[$this->filterField]][] = $option;
        }
        $options = [];
        foreach ($optionGroups as $filterIndex => $group) {
            $options[] = ['label' => '[ root ]', 'value' => '0', $this->filterField => (string)$filterIndex];
            foreach ($group as $option) {
                $options[] = $option;
            }
        }
        return $options;
    }

    /**
     * @param AbstractModel $item
     * @param int           $level
     * @param bool          $isLast
     * @return string
     */
    protected function getOptionLabel($item, $level, $isLast)
    {
        return sprintf(
            ' %s %s (ID: %d)',
            str_repeat(html_entity_decode('&#160;', ENT_NOQUOTES, 'UTF-8'), $level * 4),
            $item->getDataByKey($this->fieldTitle),
            $item->getId()
        );
    }

    /**
     * @param array $itemGroups
     * @param int   $parentId
     * @param int   $level
     * @return array
     */
    protected function collectOptions($itemGroups, $parentId = 0, $level = 0)
    {
        $options = [];
        if (isset($itemGroups[$parentId])) {
            $level++;
            $lastIndex = count($itemGroups[$parentId]) - 1;
            foreach ($itemGroups[$parentId] as $i => $item) {
                $option = [
                    'label' => $this->getOptionLabel($item, $level, $lastIndex == $i),
                    'value' => (string)$item->getDataByKey($this->fieldValue)
                ];
                if ($this->filterField !== null) {
                    $option[$this->filterField] = $item->getDataByKey($this->filterField);
                }
                $options[] = $option;
                if (isset($itemGroups[$parentId])) {
                    $options = array_merge(
                        $options,
                        $this->collectOptions($itemGroups, $item->getId(), $level)
                    );
                }
            }
        }
        return $options;
    }
}
