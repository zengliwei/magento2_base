<?php
/**
 * Copyright (c) Zengliwei. All rights reserved.
 * Each source file in this distribution is licensed under OSL 3.0, see LICENSE for details.
 */

namespace CrazyCat\Base\Helper;

use Exception;
use Magento\Framework\App\Area;
use Magento\Framework\ObjectManagerInterface;
use Magento\Framework\Url\ScopeResolverInterface;
use Magento\Framework\UrlInterface;

/**
 * @author  Zengliwei <zengliwei@163.com>
 * @url https://github.com/zengliwei/magento2_base
 */
class Url
{
    /**
     * @var ObjectManagerInterface
     */
    private $objectManager;

    /**
     * @var array
     */
    private $urlBuilders = [];

    /**
     * @param ObjectManagerInterface $objectManager
     */
    public function __construct(
        ObjectManagerInterface $objectManager
    ) {
        $this->objectManager = $objectManager;
    }

    /**
     * Get URL builder
     *
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
     * Get frontend URL builder
     *
     * @return UrlInterface
     * @throws Exception
     */
    public function getFrontendBuilder()
    {
        return $this->getUrlBuilder(Area::AREA_FRONTEND, \Magento\Framework\Url::class);
    }

    /**
     * Get backend URL builder
     *
     * @return UrlInterface
     * @throws Exception
     */
    public function getAdminhtmlBuilder()
    {
        return $this->getUrlBuilder(Area::AREA_ADMINHTML, \Magento\Backend\Model\Url::class);
    }
}
