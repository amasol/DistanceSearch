<?php

namespace Hunters\AddAdmin\Setup;

use Magento\Framework\Setup\UpgradeSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;

class UpgradeSchema implements UpgradeSchemaInterface
{
    public function upgrade(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $setup->startSetup();

        if (version_compare($context->getVersion(), '0.0.1') < 0) {

                $setup->getConnection()->addColumn($setup->getTable('sales_creditmemo_grid'),
                    'admin_refund',
                    [
                        'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                        'length' => 50,
                        'nullable' => true,
                        'comment' => 'Admin name'
                    ]
                );

                $setup->getConnection()->addColumn($setup->getTable('sales_creditmemo'),
                    'admin_refund',
                    [
                        'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                        'length' => 50,
                        'nullable' => true,
                        'comment' => 'Admin name'
                    ]
                );

        }

        $setup->endSetup();
    }
}
