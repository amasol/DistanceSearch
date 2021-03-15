<?php

namespace Hunters\SearchShopMap\Setup\Patch\Schema;

use Magento\Framework\DB\Ddl\Table;
use Magento\Framework\Setup\Patch\SchemaPatchInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Hunters\SearchShopMap\Helper;

class AddAddressDatabase implements SchemaPatchInterface
{
    /**
     * @var \Hunters\SearchShopMap\Helper\HelpPatch
     */
    public $helpPatch;

    /**
     * @var \Magento\Framework\App\ResourceConnection
     */
    public $connection;

    /**
     * AddAddressDatabase constructor.
     * @param \Magento\Framework\Setup\ModuleDataSetupInterface $moduleDataSetup
     * @param \Magento\Framework\App\ResourceConnection $connection
     */
    public function __construct(
        \Magento\Framework\App\ResourceConnection $connection,
        \Magento\Framework\Setup\ModuleDataSetupInterface $moduleDataSetup,
        \Hunters\SearchShopMap\Helper\HelpPatch $helpPatch
    ) {
        $this->moduleDataSetup = $moduleDataSetup;
        $this->connection = $connection;
        $this->helpPatch = $helpPatch;
    }

    public static function getDependencies()
    {
        // TODO: Implement getDependencies() method.
        return [];
    }

    public function getAliases()
    {
        // TODO: Implement getAliases() method.
        return [];
    }

    public function apply()
    {
        $this->moduleDataSetup->startSetup();
        $connection = $this->connection->getConnection();
        $table = $this->connection->getTableName('AddressZipCode');


//      проверка функции validData на google API
        $zipArr = array_values($this->helpPatch->validData());

//        $arr = $this->helpPatch->addDataTable($zipArr);


        $connection->insertMultiple($table, $zipArr);
        $this->moduleDataSetup->endSetup();
    }
}