<?php
/**
 * Copyright (c) Zengliwei. All rights reserved.
 * Each source file in this distribution is licensed under OSL 3.0, see LICENSE for details.
 */

namespace CrazyCat\Base\Ui\Component\Listing\Column;

use CrazyCat\Base\Helper\Url as UrlHelper;
use Magento\Framework\UrlInterface;
use Magento\Framework\View\Asset\Repository as AssetRepository;
use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Ui\Component\Listing\Columns\Column;

/**
 * @package CrazyCat\Base
 * @author  Zengliwei <zengliwei@163.com>
 * @url https://github.com/zengliwei/magento2_base
 */
class Thumbnail extends Column
{
    private UrlHelper $urlHelper;
    private AssetRepository $assetRepository;

    public function __construct(
        AssetRepository $assetRepository,
        UrlHelper $urlHelper,
        ContextInterface $context,
        UiComponentFactory $uiComponentFactory,
        array $components = [],
        array $data = []
    ) {
        $this->assetRepository = $assetRepository;
        $this->urlHelper = $urlHelper;

        parent::__construct($context, $uiComponentFactory, $components, $data);
    }

    /**
     * @inheritDoc
     */
    public function prepareDataSource(array $dataSource)
    {
        if (isset($dataSource['data']['items'])) {
            $baseUrl = $this->urlHelper->getFrontendBuilder()->getBaseUrl(['_type' => UrlInterface::URL_TYPE_MEDIA]);
            $defaultUrl = $this->assetRepository->createAsset($this->getData('config/default_value'))->getUrl();
            $fieldName = $this->getDataByKey('name');
            $folder = $this->getData('config/folder');
            foreach ($dataSource['data']['items'] as &$item) {
                $item[$fieldName . '_src'] = $item[$fieldName]
                    ? ($baseUrl . $folder . '/' . $item[$fieldName])
                    : $defaultUrl;
            }
        }
        return $dataSource;
    }
}
