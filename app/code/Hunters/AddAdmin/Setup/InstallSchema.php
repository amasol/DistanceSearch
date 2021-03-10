<?php

namespace Hunters\AddAdmin\Setup;

use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\Setup\ModuleContextInterface;

class InstallSchema implements InstallSchemaInterface
{
    public function install(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $setup->startSetup();
        $setup->getConnection()->addColumn(
            $setup->getTable('sales_creditmemo'),
            'admin_refund',
            [
                'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                'length' => 50,
                'nullable' => true,
                'comment' => 'Admin name'
            ]
        );
        $setup->getConnection()->addColumn(
            $setup->getTable('sales_creditmemo_grid'),
            'admin_refund',
            [
                'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                'length' => 50,
                'nullable' => true,
                'comment' => 'Admin name'
            ]
        );
        $setup->endSetup();
    }
}
