<?php

namespace Hunters\SearchShopMap\Setup\Patch\Schema;

use Magento\Framework\DB\Ddl\Table;
use Magento\Framework\Setup\Patch\SchemaPatchInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;

use Hunters\SearchShopMap\Helper;


class AddAddressDatabase implements SchemaPatchInterface
{
    /**
     * @var \Hunters\SearchShopMap\Helper
     */
    protected $helpPatch;

    /**
     * @var \Magento\Framework\App\ResourceConnection
     */
    protected $connection;

    /**
     * @var \Hunters\SearchShopMap\Model\ResourceModel\Collection\Friends
     */
    protected $friendsCollectionFactory;

    /**
     * AddAddressDatabase constructor.
     * @param \Magento\Framework\Setup\ModuleDataSetupInterface $moduleDataSetup
     */
    public function __construct(
        \Magento\Framework\App\ResourceConnection $connection,
        \Magento\Framework\Setup\ModuleDataSetupInterface $moduleDataSetup,
        \Hunters\SearchShopMap\Model\ResourceModel\Collection\FriendsFactory $friendsCollectionFactory,

        \Hunters\SearchShopMap\Helper\HelpPatch $helpPatch
    ) {
        $this->moduleDataSetup = $moduleDataSetup;
        $this->connection = $connection;
        $this->friendsCollectionFactory = $friendsCollectionFactory;
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
        $zipArr = array_values($this->helpPatch->validData());
        $arr = $this->helpPatch->addDataTable($zipArr);
        $connection->insertMultiple($table, $arr);
        $this->moduleDataSetup->endSetup();
    }
}