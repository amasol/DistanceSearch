<?php
namespace Hunters\SearchShopMap\ViewModel;

use Magento\Framework\View\Element\Block\ArgumentInterface;

use Hunters\SearchShopMap\Setup\Patch\Schema;

class Search implements \Magento\Framework\View\Element\Block\ArgumentInterface
{
    /**
     * @var null|\Hunters\SearchShopMap\Model\SaveZipCode
     */
    public $model = null;

    /**
     * @var \Hunters\SearchShopMap\Model\ResourceModel\Collection\SaveZipCode
     */
    public $saveZipCodeCollectionFactory;



    public $helper;
    public $patch;

    public function __construct(
        \Hunters\SearchShopMap\Model\ResourceModel\Collection\SaveZipCodeFactory $saveZipCodeCollectionFactory,


        \Hunters\SearchShopMap\Setup\Patch\Schema\AddAddressDatabase $patch,
        \Hunters\SearchShopMap\Helper\HelpPatch $helper
    ) {
        $this->saveZipCodeCollectionFactory = $saveZipCodeCollectionFactory;


        $this->patch = $patch;
        $this->helper = $helper;
    }

    public function coordinateArray()
    {
//        $param = $this->helper->addDataTable('10001');
//        $param = $this->patch->apply();


        $model = $this->saveZipCodeCollectionFactory->create();
        $model->getItems();
        $noSortArray = $model->getColumnValues("coordinate");
        $array = array_values(array_unique($noSortArray, SORT_REGULAR));
        $allZipArray = array_map('json_decode', $array);
        return $allZipArray;


//        return $param;
    }
}