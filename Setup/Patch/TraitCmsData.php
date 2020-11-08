<?php

namespace Common\Base\Setup\Patch;

use Exception;
use Magento\Cms\Model\Block;
use Magento\Cms\Model\Page;
use Magento\Cms\Model\ResourceModel\Block as ResourceBlock;
use Magento\Cms\Model\ResourceModel\Page as ResourcePage;

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
     * @param Block $block
     * @return void
     * @throws Exception
     */
    private function saveBlock($block)
    {
        $this->getResourceBlock()->save($block);
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
}
