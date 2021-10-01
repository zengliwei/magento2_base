<?php
/**
 * Copyright (c) Zengliwei. All rights reserved.
 * Each source file in this distribution is licensed under OSL 3.0, see LICENSE for details.
 */

namespace CrazyCat\Base\Ui\Component\Listing\Column;

use Magento\Framework\UrlInterface;
use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Ui\Component\Listing\Columns\Column;

/**
 * @author  Zengliwei <zengliwei@163.com>
 * @url https://github.com/zengliwei/magento2_base
 */
class Actions extends Column
{
    /**
     * @var UrlInterface
     */
    protected $urlBuilder;

    /**
     * @param UrlInterface       $urlBuilder
     * @param ContextInterface   $context
     * @param UiComponentFactory $uiComponentFactory
     * @param array              $components
     * @param array              $data
     */
    public function __construct(
        UrlInterface $urlBuilder,
        ContextInterface $context,
        UiComponentFactory $uiComponentFactory,
        array $components = [],
        array $data = []
    ) {
        $this->urlBuilder = $urlBuilder;
        parent::__construct($context, $uiComponentFactory, $components, $data);
    }

    /**
     * @inheritDoc
     */
    public function prepareDataSource(array $dataSource)
    {
        $config = $this->getData('config');
        $requestFieldName = $config['requestFieldName'] ?? 'id';
        $primaryFieldName = $config['primaryFieldName'] ?? 'id';
        $actions = $config['actions'] ?? ['edit', 'delete'];

        if (isset($config['fieldAction']['params']['route'])
            && isset($dataSource['data']['items'])
        ) {
            $route = rtrim($config['fieldAction']['params']['route'], '/') . '/';
            foreach ($dataSource['data']['items'] as &$item) {
                foreach ($actions as $action) {
                    switch ($action) {
                        case 'view':
                            $item['actions'][$action] = [
                                'label' => __('View'),
                                'href'  => $this->urlBuilder->getUrl(
                                    $route . 'view',
                                    [$requestFieldName => $item[$primaryFieldName]]
                                )
                            ];
                            break;

                        case 'edit':
                            $item['actions'][$action] = [
                                'label' => __('Edit'),
                                'href'  => $this->urlBuilder->getUrl(
                                    $route . 'edit',
                                    [$requestFieldName => $item[$primaryFieldName]]
                                )
                            ];
                            break;

                        case 'delete':
                            $item['actions'][$action] = [
                                'label'   => __('Delete'),
                                'href'    => $this->urlBuilder->getUrl(
                                    $route . 'delete',
                                    [$requestFieldName => $item[$primaryFieldName]]
                                ),
                                'post'    => true,
                                'confirm' => [
                                    'title'   => __('Delete'),
                                    'message' => __(
                                        'Are you sure you want to delete the record (ID: %1)?',
                                        $item[$primaryFieldName]
                                    )
                                ],
                            ];
                            break;
                    }
                }
            }
        }
        return $dataSource;
    }
}
