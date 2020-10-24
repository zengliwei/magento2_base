<?php

namespace Common\Base\Ui\Component\Store;

use Magento\Store\Ui\Component\Listing\Column\Store\Options as StoreOptions;

class Options extends StoreOptions
{
    public const ALL_STORE_VIEWS = '0';

    /**
     * @inheritDoc
     */
    protected function generateCurrentOptions()
    {
        $this->currentOptions['All Store Views'] = [
            'label' => __('All Store Views'),
            'value' => self::ALL_STORE_VIEWS
        ];
        parent::generateCurrentOptions();
    }
}
