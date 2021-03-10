<?php
namespace Hunters\AddAdminId\Setup;

use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;

class InstallSchema implements InstallSchemaInterface {
	
    /**
     * Installs DB schema for a module
     *
     * @param SchemaSetupInterface $setup
     * @param ModuleContextInterface $context
     * @return void
     */
    public function install(SchemaSetupInterface $setup, ModuleContextInterface $context) {
        $setup->startSetup();

        $salesOrderTable    =   $setup->startSetup()->getTable('sales_order');
        $adminIdColunmName  =   "admin_id";
        $connection         =   $setup->getConnection();

        if ($connection->tableColumnExists($salesOrderTable, $adminIdColunmName) === false) {
            $connection->addColumn(
                $salesOrderTable,
                $adminIdColunmName,
                [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                    'length' => 10,
                    'nullable' => true,
                    'comment' => 'Admin User ID'
                ]);
        }
        $setup->endSetup();
    }
	
}
