<?php
namespace Hunters\SearchShopMap\Helper;

class HelpPatch
{
    /**
     * @var \Hunters\SearchShopMap\Service\SearchZip
     */
    public $searchZip;

    /**
     * @var \Magento\Framework\App\ResourceConnection
     */
    public $connection;

    /**
     * @var \Hunters\SearchShopMap\Model\ResourceModel\Collection\Friends
     */
    public $friendsCollectionFactory;

    /**
     * HelpPatch constructor
     */
    public function __construct(
        \Magento\Framework\App\ResourceConnection $connection,
        \Hunters\SearchShopMap\Service\SearchZip $searchZip,
        \Hunters\SearchShopMap\Model\ResourceModel\Collection\FriendsFactory $friendsCollectionFactory
    ) {
        $this->searchZip = $searchZip;
        $this->connection = $connection;
        $this->friendsCollectionFactory = $friendsCollectionFactory;
    }

    public function coordinate($zip)
    {
        sleep(0.2);
        $address = $this->searchZip->total($zip);
        if ($address != NULL){
            $result = json_decode($address, true);
            return $result;
        }
        else {
            return NULL;
        }
    }

    public function validData()
    {
        $model = $this->friendsCollectionFactory->create();
        $model->getItems();
        $allZipArray = $model->getColumnValues("postcode");
//        удалить ограничение
        $allZipArray = array_slice($allZipArray, 0, 10);
        $resultIncorrectZip = array_map(array($this, 'coordinate'), $allZipArray);

//        echo '<pre>';
//        print_r($resultIncorrectZip);
//        echo "test";
//        echo '</pre>';
//        exit();


        $resultIncorrectArray = array_filter($resultIncorrectZip, function($element) {
            if ($element != NULL) {
                return $element;
            }
        });
        $result = array_values($resultIncorrectArray);
        return $result;
    }

    public function addDataTable($zipArr)
    {
        $arr  = array();
        $count = count($zipArr);

        for ($i = 0; $i < $count; $i++) {
            $arr[$i]['postcode'] = $zipArr[$i]['postcode'];
            $arr[$i]['state'] = $zipArr[$i]['state'];
            $arr[$i]['coordinate'] = $zipArr[$i]['coordinate'];
        }
        return $arr;
    }
}