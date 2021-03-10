<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Hunters\Smtp\Setup;

use Magento\Framework\Setup\UpgradeSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;

class UpgradeSchema implements UpgradeSchemaInterface
{
    public function upgrade(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
      if(version_compare($context->getVersion(), '1.0.0') < 0) {

        $installer = $setup;

        $installer->startSetup();

        $table = $installer->getConnection()->newTable(
            $installer->getTable('mail_queue')
        )
        ->addColumn(
            'queue_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
            'ID'
        )
        ->addColumn(
            'created_at',
            \Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP,
            null,
            ['nullable' => false, 'default' => \Magento\Framework\DB\Ddl\Table::TIMESTAMP_INIT],
            'Created At'
        )
        ->addColumn(
            'updated_at',
            \Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP,
            null,
            ['nullable' => false, 'default' => \Magento\Framework\DB\Ddl\Table::TIMESTAMP_UPDATE],
            'Created At'
        )
        ->addColumn(
            'mail_desc_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['unsigned' => true, 'nullable' => false],
            'Mail Description ID'
        )
        ->addColumn(
            'status',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['nullable' => false, 'default' => 1],
            'Status'
        );

        $table2 = $installer->getConnection()->newTable(
            $installer->getTable('mail_queue_desc')
        )
        ->addColumn(
            'mail_desc_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
            'Mail Desc ID'
        )
        ->addColumn(
            'email',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            null,
            [],
            'Email'
        )
        ->addColumn(
            'subject',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            null,
            [],
            'Subject'
        )
        ->addColumn(
            'body',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            null,
            [],
            'Body'
        )
        ->addColumn(
            'sender',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            null,
            [],
            'Sender'
        );

        $installer->getConnection()->createTable($table);
        $installer->getConnection()->createTable($table2);

        $installer->endSetup();
      }

        if(version_compare($context->getVersion(), '1.0.1') < 0) {
            $installer = $setup;
            $installer->startSetup();
            $installer->getConnection()->addColumn(
                $installer->getTable('mail_queue'),
                'store_id',
                [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT, //to redefine to tinyint or better enum once either supported
                    'length' => 1,
                    'nullable' => false,
                    'default' => 1,
                    'comment' => 'store_id_reference'
                ]
            );
        }
    }

}
