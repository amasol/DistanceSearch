<?php
/**
 * Created by PhpStorm.
 * User: admin
 * Date: 8/30/16
 * Time: 3:33 PM
 */

namespace Hunters\MultiFeed\Setup;


use Magento\Framework\ObjectManagerInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\UpgradeDataInterface;

class UpgradeData implements UpgradeDataInterface
{

    /**
    * @var \Magento\Eav\Setup\EavSetupFactory
    */
    private $eavSetupFactory;

    public function __construct(
        \Magento\Eav\Setup\EavSetupFactory $eavSetupFactory
    ) {
        $this->eavSetupFactory = $eavSetupFactory;
    }

    /**
     * Upgrades data for a module
     *
     * @param ModuleDataSetupInterface $setup
     * @param ModuleContextInterface $context
     * @return void
     */
    public function upgrade(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
    {
        $setup->startSetup();

        if(version_compare($context->getVersion(), '1.0.8') < 0) {
            $this->upgradeTo_1_0_8($setup, $context);
        }

        $setup->endSetup();
    }

    /**
     * @param ModuleDataSetupInterface $setup
     * @param ModuleContextInterface $context
     */
    private function upgradeTo_1_0_8(ModuleDataSetupInterface $setup, ModuleContextInterface $context) {
    //do NOT uncomment the below unless you REALLY KNOW what you are doing
    //    $eavSetup = $this->eavSetupFactory->create(['setup' => $setup]);

    //    $eavSetup->removeAttribute(
    //        \Magento\Customer\Model\Customer::ENTITY,
    //        'customer_source'
    //    );
    }

}
