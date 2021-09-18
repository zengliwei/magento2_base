<?php
/**
 * Copyright (c) Zengliwei. All rights reserved.
 * Each source file in this distribution is licensed under OSL 3.0, see LICENSE for details.
 */

namespace CrazyCat\Base\Setup\Patch;

use Exception;
use Magento\Cms\Model\Block;
use Magento\Cms\Model\Page;
use Magento\Cms\Model\ResourceModel\Block as ResourceBlock;
use Magento\Cms\Model\ResourceModel\Block\Collection as BlockCollection;
use Magento\Cms\Model\ResourceModel\Page as ResourcePage;
use Magento\Cms\Model\ResourceModel\Page\Collection as PageCollection;

/**
 * @package CrazyCat\Base
 * @author  Zengliwei <zengliwei@163.com>
 * @url https://github.com/zengliwei/magento2_base
 */
trait TraitCmsData
{
    /**
     * @var ResourceBlock
     */
    private $resourceBlock;

    /**
     * @var ResourcePage
     */
    private $resourcePage;

    /**
     * @return BlockCollection
     */
    private function createBlockCollection()
    {
        return $this->objectManager->create(BlockCollection::class);
    }

    /**
     * @return PageCollection
     */
    private function createPageCollection()
    {
        return $this->objectManager->create(PageCollection::class);
    }

    /**
     * @param Block $block
     * @return void
     * @throws Exception
     */
    private function saveBlock($block)
    {
        $this->getResourceBlock()->save($block);
    }

    /**
     * @return ResourceBlock
     */
    private function getResourceBlock()
    {
        if ($this->resourceBlock === null) {
            $this->resourceBlock = $this->objectManager->get(ResourceBlock::class);
        }
        return $this->resourceBlock;
    }

    /**
     * @param Page $page
     * @return void
     * @throws Exception
     */
    private function savePage($page)
    {
        $this->getResourcePage()->save($page);
    }

    /**
     * @return ResourcePage
     */
    private function getResourcePage()
    {
        if ($this->resourcePage === null) {
            $this->resourcePage = $this->objectManager->get(ResourcePage::class);
        }
        return $this->resourcePage;
    }

    /**
     * @param array $data
     * @return Block
     * @throws Exception
     */
    private function createBlock(array $data)
    {
        $block = $this->objectManager->create(Block::class);
        $this->getResourceBlock()->save($block->setData($data));
        return $block;
    }

    /**
     * @param array $data
     * @return Page
     * @throws Exception
     */
    private function createPage(array $data)
    {
        $page = $this->objectManager->create(Page::class);
        $this->getResourcePage()->save($page->setData($data));
        return $page;
    }
}
