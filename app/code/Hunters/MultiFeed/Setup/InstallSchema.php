<?php


namespace Hunters\MultiFeed\Setup;

use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\Setup\SchemaSetupInterface;

class InstallSchema implements InstallSchemaInterface
{

    /**
     * {@inheritdoc}
     */
    public function install(
        SchemaSetupInterface $setup,
        ModuleContextInterface $context
    ) {
        $table_hunters_multifeed_queue = $setup->getConnection()->newTable($setup->getTable('hunters_multifeed_queue'));

        $table_hunters_multifeed_queue->addColumn(
            'queue_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['identity' => true,'nullable' => false,'primary' => true,'unsigned' => true,],
            'Entity ID'
        );

        $table_hunters_multifeed_queue->addColumn(
            'entity_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            [],
            'entity_id'
        );

        $table_hunters_multifeed_queue->addColumn(
            'entity_type',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            null,
            [],
            'entity_type'
        );

      $table_hunters_multifeed_queue->addColumn(
          'status',
          \Magento\Framework\DB\Ddl\Table::TYPE_BOOLEAN,
          null,
          [],
          'status'
        );

      $table_hunters_multifeed_queue->addColumn(
          'created_at',
          \Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP,
          null,
          ['nullable' => false, 'default' => \Magento\Framework\DB\Ddl\Table::TIMESTAMP_INIT],
          'Created At'
      )->addColumn(
          'updated_at',
          \Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP,
          null,
          ['nullable' => false, 'default' => \Magento\Framework\DB\Ddl\Table::TIMESTAMP_INIT_UPDATE],
          'Updated At'
      );

      $setup->getConnection()->createTable($table_hunters_multifeed_queue);
    }
}
