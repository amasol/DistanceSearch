<?php
/**
 * Created by PhpStorm.
 * User: vbudnik
 * Date: 03.05.19
 * Time: 16:25
 */

namespace Hunters\CheckOrder\Setup;

use Magento\Framework\Setup\UpgradeSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;

class UpgradeSchema implements UpgradeSchemaInterface
{
    public function upgrade(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $setup->startSetup();

        if (version_compare($context->getVersion(), '1.0.1') < 0)
        {
            $tableName = $setup->getTable('sales_order');
            if ($setup->getConnection()->isTableExists($tableName) == true)
            {
                $columns = [
                    'is_downloadable' => [
                        'type'      => \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                        'length'    => '1',
                        'nullable'  => true,
                        'comment'   => 'Is type order downloadable',
                    ],
                ];
                $connection = $setup->getConnection();
                foreach ($columns as $col_name => $col_array){
                    $connection->addColumn($tableName, $col_name, $col_array);
                }
            }
        }
        $setup->endSetup();
    }
}