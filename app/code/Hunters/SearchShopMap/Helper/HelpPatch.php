<?php
namespace Hunters\SearchShopMap\Helper;

class HelpPatch
{
    /**
     * @var \Hunters\SearchShopMap\Service\SearchZip
     */
    public $searchZip;

    /**
     * @var \Magento\Company\Model\ResourceModel\Company\Collection
     */
    public $companyCollectionFactory;

    /**
     * HelpPatch constructor
     * @param \Hunters\SearchShopMap\Service\SearchZip $searchZip
     * @param \Magento\Company\Model\ResourceModel\Company\Collection $companyCollectionFactory
     */
    public function __construct(
        \Hunters\SearchShopMap\Service\SearchZip $searchZip,
        \Magento\Company\Model\ResourceModel\Company\Collection $companyCollectionFactory
    ) {
        $this->searchZip = $searchZip;
        $this->companyCollectionFactory = $companyCollectionFactory;
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
        $allZipArray = $this->companyCollectionFactory->getColumnValues('postcode');
//        удалить ограничение
        $allZipArray = array_slice($allZipArray, 0, 7);
        $resultIncorrectZip = array_map(array($this, 'coordinate'), $allZipArray);

//        echo '<pre>';
//        print_r($model);
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