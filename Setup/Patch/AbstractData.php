<?php

namespace Common\Base\Setup\Patch;

use Magento\Eav\Setup\EavSetup;
use Magento\Eav\Setup\EavSetupFactory;
use Magento\Framework\ObjectManagerInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\Patch\DataPatchInterface;

abstract class AbstractData implements DataPatchInterface
{
    /**
     * @var EavSetup
     */
    protected $eavSetup;

    /**
     * @var ModuleDataSetupInterface
     */
    protected $moduleDataSetup;

    /**
     * @var ObjectManagerInterface
     */
    protected $objectManager;

    /**
     * @param EavSetupFactory          $eavSetupFactory
     * @param ModuleDataSetupInterface $moduleDataSetup
     * @param ObjectManagerInterface   $objectManager
     */
    public function __construct(
        EavSetupFactory $eavSetupFactory,
        ModuleDataSetupInterface $moduleDataSetup,
        ObjectManagerInterface $objectManager
    ) {
        $this->eavSetup = $eavSetupFactory->create(['setup' => $moduleDataSetup]);
        $this->moduleDataSetup = $moduleDataSetup;
        $this->objectManager = $objectManager;
    }

    /**
     * {@inheritdoc}
     */
    public static function getDependencies()
    {
        return [];
    }

    /**
     * {@inheritdoc}
     */
    public function getAliases()
    {
        return [];
    }
}
