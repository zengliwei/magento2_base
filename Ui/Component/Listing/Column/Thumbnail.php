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

namespace Common\Base\Ui\Component\Listing\Column;

use Common\Base\Helper\Url as UrlHelper;
use Magento\Framework\UrlInterface;
use Magento\Framework\View\Asset\Repository as AssetRepository;
use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Ui\Component\Listing\Columns\Column;

/**
 * @package Common\Base
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
            $defaultUrl = $this->assetRepository->createAsset('Common_Brand::images/default.jpg')->getUrl();
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
