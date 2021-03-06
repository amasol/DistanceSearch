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
    public $ajax;
    public $data;

    public function __construct(
        \Hunters\SearchShopMap\Model\ResourceModel\Collection\SaveZipCodeFactory $saveZipCodeCollectionFactory,

        \Hunters\SearchShopMap\Service\SearchZip $searchZip,
        \Hunters\SearchShopMap\Helper\HelpPatch $helpPatch,
        \Hunters\SearchShopMap\Setup\Patch\Schema\AddAddressDatabase $shema,
        \Hunters\SearchShopMap\Controller\Page\Ajax $ajax,
        \Hunters\SearchShopMap\Helper\Data $data
    ) {
        $this->saveZipCodeCollectionFactory = $saveZipCodeCollectionFactory;
        $this->searchZip = $searchZip;
        $this->helpPatch = $helpPatch;
        $this->shema = $shema;
        $this->ajax = $ajax;
        $this->data= $data;
    }
    
    public function coordinateArray()
    {
        $model = $this->saveZipCodeCollectionFactory->create();

//      мы достаем все магазины в том числе и с повторяющеймс язипками .
        $array = $model->getColumnValues("coordinate");

//        $array = $model->getColumnValues("street");
//        $this->helpPatch->validData();
//        $this->shema->apply();
//        $this->ajax->execute();
//        $this->data->getCompanyIdByCoordinate();

//        $this->data->getCompanyIdByCoordinate(30.1309314,-95.4430982);

//        $array = array_values(array_unique($array, SORT_REGULAR));

        $allZipArray = array_map('json_decode', $array);

        return $allZipArray;
    }
}