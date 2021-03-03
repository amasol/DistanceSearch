<?php

namespace Hunters\SearchShopMap\Setup\Patch\Schema;


use Magento\Framework\DB\Ddl\Table;
use Magento\Framework\Setup\Patch\SchemaPatchInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Hunters\SearchShopMap\Service\SearchZip;


class AddAddressDatabase implements SchemaPatchInterface
{
    private $moduleDataSetup;
    /**
     * @var \Magento\Framework\App\ResourceConnection
     */
    protected $connection;
    private $searchZip;

    public function __construct(
        ModuleDataSetupInterface $moduleDataSetup,
        \Magento\Framework\App\ResourceConnection $connection,
        SearchZip $searchZip
    ) {
        $this->moduleDataSetup = $moduleDataSetup;
        $this->connection = $connection;
        $this->searchZip = $searchZip;
    }

    public function coordinate($zip) {
        sleep(0.2);
        $addres = $this->searchZip->total2($zip);
        if ($addres != NULL){
            $res = json_decode(json_encode(json_decode($addres)), true);
            return $res;
        }
        else {
            return "NULL";
        }
    }

    public function ziprevert(){

//        нужно здесь вытянуть наши данные
//        $zip = file_get_contents('/var/www/html/magento224/addres.txt');
//        $zip = json_decode($zip);

        $zip =
        $result = array_map(array($this, 'coordinate'), $zip);
        $new_array = array_filter($result, function($element) {
            if ($element != "NULL") {
                return $element;
            }
        });
        return $new_array;
    }

    public function apply()
    {
        $this->moduleDataSetup->startSetup();
        $arr  = array();
        $connection = $this->connection->getConnection();
        $table = $this->connection->getTableName('AddressZipCode');
        $zipArr = array_values($this->ziprevert());

        $count = count($zipArr);
        for ($i = 0; $i < $count; $i++) {
            $arr[$i]['postcode'] = $zipArr[$i]['postcode'];
            $arr[$i]['state'] = $zipArr[$i]['state'];
            $arr[$i]['coordinate'] = json_encode($zipArr[$i]['coordinate']);
        }
        $connection->insertMultiple($table, $arr);
        $this->moduleDataSetup->endSetup();
    }
}