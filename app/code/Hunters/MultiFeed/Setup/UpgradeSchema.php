<?php

namespace Hunters\MultiFeed\Setup;


use Magento\Framework\DB\Adapter\AdapterInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\Setup\UpgradeSchemaInterface;
use Magento\Framework\DB\Ddl\Table;


class UpgradeSchema implements UpgradeSchemaInterface
{
  public function upgrade(SchemaSetupInterface $setup, ModuleContextInterface $context)
  {
      $setup->startSetup();

      if(version_compare($context->getVersion(), '1.0.1') < 0) {
          $this->upgradeTo_1_0_1($setup, $context);
      }

      if(version_compare($context->getVersion(), '1.0.2') < 0) {
          $this->upgradeTo_1_0_2($setup, $context);
      }

      if(version_compare($context->getVersion(), '1.0.7') < 0) {
          $this->upgradeTo_1_0_7($setup, $context);
      }

      if(version_compare($context->getVersion(), '1.0.8') < 0) {
          $this->upgradeTo_1_0_8($setup, $context);
      }
  }

  public function upgradeTo_1_0_1(SchemaSetupInterface $setup, ModuleContextInterface $context)
  {
    $setup->getConnection()
        ->addColumn(
            $setup->getTable('hunters_multifeed_queue'),
            'store_id',
            [
                'type' => 'smallint',
                'length' => 6,
                'default'   =>  null,
                'comment' => 'Store Id'
            ]

//            'smallint(6) default null'
        );
  }

  public function upgradeTo_1_0_2(SchemaSetupInterface $setup, ModuleContextInterface $context)
  {
    $setup->getConnection()->addColumn(
      $setup->getTable('salesrule'),
      'sf_campaign_code',
      [
          'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
          'length' => 200,
          'nullable' => true,
          'default' => '',
          'comment' => 'SalesForce Campaign Code'
      ]
    );
  }

    private function upgradeTo_1_0_7(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $setup->startSetup();
        $setup->getConnection()->changeColumn(
            $setup->getTable('hunters_multifeed_queue'),
            'store_id',
            'store_id',
            [
                'type' => 'smallint',
                'length' => 6,
                'nullable'  =>  false,
                'default'   =>  "1",
                'comment' => 'Store Id'
            ]
        );

        $setup->endSetup();
    }

    /**
     * @param SchemaSetupInterface $setup
     * @param ModuleContextInterface $context
     * @return void
     */
    private function upgradeTo_1_0_8(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {

        $setup->startSetup();

        $conn = $setup->getConnection();

        /*
             Name due to backwards compatibility.
             Used to store additional marketing attributes used for SF exports.
        */

        $tableName = $setup->getTable('microsite_order_detail');
        if($conn->isTableExists($tableName) != true) {

            $table = $conn->newTable($tableName)
                            ->addColumn(
                                'id',
                                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                                null,
                                ['unsigned' => true, 'nullable' => false, 'auto_increment' => true, 'primary' => true]
                            )
                            ->addColumn(
                                'order_id',
                                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                                null,
                                ['unsigned' => true, 'nullable' => false, 'default' => 0]
                            )
                            ->addColumn(
                                'project_code',
                                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                                null,
                                ['nullable' => false, 'default' => '']
                            )
                            ->addColumn(
                                'media_code',
                                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                                null,
                                ['nullable' => false, 'default' => '']
                            )
                            ->addColumn(
                                'campaign_description',
                                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                                null,
                                ['nullable' => false, 'default' => '']
                            )
                            ->addIndex(
                                $installer->getIdxName($setup->getTable('microsite_order_detail'), ['order_id']),
                                ['order_id']
                            );

            $conn->createTable($table);

        }
        $setup->endSetup();
    }
}
