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

namespace Common\Base\Helper;

use Exception;
use Magento\Framework\App\Area;
use Magento\Framework\ObjectManagerInterface;
use Magento\Framework\Url\ScopeResolverInterface;
use Magento\Framework\UrlInterface;

/**
 * @package Common\Base
 * @author  Zengliwei <zengliwei@163.com>
 * @url https://github.com/zengliwei/magento2_base
 */
class Url
{
    private ObjectManagerInterface $objectManager;
    private array $urlBuilders = [];

    /**
     * @param ObjectManagerInterface $objectManager
     */
    public function __construct(
        ObjectManagerInterface $objectManager
    ) {
        $this->objectManager = $objectManager;
    }

    /**
     * @param string $areaCode
     * @param string $instanceClass
     * @return UrlInterface
     */
    private function getUrlBuilder($areaCode, $instanceClass)
    {
        if (!isset($this->urlBuilders[$areaCode])) {
            $scopeResolver = $this->objectManager->create(
                ScopeResolverInterface::class,
                ['areaCode' => $areaCode]
            );
            $this->urlBuilders[$areaCode] = $this->objectManager->create(
                $instanceClass,
                ['scopeResolver' => $scopeResolver]
            );
        }
        return $this->urlBuilders[$areaCode];
    }

    /**
     * @param null $routePath
     * @param null $routeParams
     * @return string
     * @throws Exception
     */
    public function getStorefrontUrl($routePath = null, $routeParams = null)
    {
        return $this->getUrlBuilder(Area::AREA_FRONTEND, \Magento\Framework\Url::class)
            ->getUrl($routePath, $routeParams);
    }

    /**
     * @param null $routePath
     * @param null $routeParams
     * @return string
     * @throws Exception
     */
    public function getAdminPanelUrl($routePath = null, $routeParams = null)
    {
        return $this->getUrlBuilder(Area::AREA_ADMINHTML, \Magento\Backend\Model\Url::class)
            ->getUrl($routePath, $routeParams);
    }
}
