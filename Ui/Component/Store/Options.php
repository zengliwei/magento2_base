<?php
/**
 * Copyright (c) Zengliwei. All rights reserved.
 * Each source file in this distribution is licensed under OSL 3.0, see LICENSE for details.
 */

namespace CrazyCat\Base\Ui\Component\Store;

use Magento\Store\Ui\Component\Listing\Column\Store\Options as StoreOptions;

/**
 * @author  Zengliwei <zengliwei@163.com>
 * @url https://github.com/zengliwei/magento2_base
 */
class Options extends StoreOptions
{
    public const ALL_STORE_VIEWS = '0';

    /**
     * @inheritDoc
     */
    protected function generateCurrentOptions(): void
    {
        $this->currentOptions['All Store Views'] = [
            'label' => __('All Store Views'),
            'value' => self::ALL_STORE_VIEWS
        ];
        parent::generateCurrentOptions();
    }
}
