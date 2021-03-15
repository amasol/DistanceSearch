<?php
namespace Hunters\SearchShopMap\ViewModel;

use Magento\Framework\View\Element\Block\ArgumentInterface;

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


    public $searchZip;
    public $helpPatch;

    public function __construct(
        \Hunters\SearchShopMap\Model\ResourceModel\Collection\SaveZipCodeFactory $saveZipCodeCollectionFactory,

        \Hunters\SearchShopMap\Service\SearchZip $searchZip,
        \Hunters\SearchShopMap\Helper\HelpPatch $helpPatch
    ) {
        $this->saveZipCodeCollectionFactory = $saveZipCodeCollectionFactory;
        $this->searchZip = $searchZip;
        $this->helpPatch = $helpPatch;
    }
    
    public function coordinateArray()
    {
        $model = $this->saveZipCodeCollectionFactory->create();
        $array = $model->getColumnValues("coordinate");

//        $this->searchZip->total();
//        $this->helpPatch->validData();


        $allZipArray = array_map('json_decode', $array);
        return $allZipArray;
    }
}