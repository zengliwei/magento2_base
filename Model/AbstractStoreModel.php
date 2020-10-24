<?php

namespace Common\Base\Model;

use Magento\Framework\Model\AbstractModel;

abstract class AbstractStoreModel extends AbstractModel
{
    /**
     * @inheritDoc
     */
    public function afterLoad()
    {
        if ($this->hasData('store_ids') && is_string($this->getData('store_ids'))) {
            $this->setData('store_ids', explode(',', $this->getData('store_ids')));
        }
        return parent::afterLoad();
    }

    /**
     * @inheritDoc
     */
    public function beforeSave()
    {
        if ($this->hasData('store_ids') && is_array($this->getData('store_ids'))) {
            $this->setData('store_ids', implode(',', $this->getData('store_ids')));
        }
        return parent::beforeSave();
    }
}
