<?php

namespace Common\Base\Setup\Patch;

use Magento\Eav\Setup\EavSetup;
use Magento\Eav\Setup\EavSetupFactory;
use Magento\Framework\App\State;
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
     * @var State
     */
    protected $state;

    /**
     * @param EavSetupFactory          $eavSetupFactory
     * @param ModuleDataSetupInterface $moduleDataSetup
     * @param ObjectManagerInterface   $objectManager
     * @param State                    $state
     */
    public function __construct(
        EavSetupFactory $eavSetupFactory,
        ModuleDataSetupInterface $moduleDataSetup,
        ObjectManagerInterface $objectManager,
        State $state
    ) {
        $this->eavSetup = $eavSetupFactory->create(['setup' => $moduleDataSetup]);
        $this->moduleDataSetup = $moduleDataSetup;
        $this->objectManager = $objectManager;
        $this->state = $state;
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
