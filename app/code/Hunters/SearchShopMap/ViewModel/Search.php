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
    private $saveZipCodeCollectionFactory;

    public function __construct(
        \Hunters\SearchShopMap\Model\ResourceModel\Collection\SaveZipCodeFactory $saveZipCodeCollectionFactory
    ) {
        $this->saveZipCodeCollectionFactory = $saveZipCodeCollectionFactory;
    }

    public function coordinateArray()
    {
        $model = $this->saveZipCodeCollectionFactory->create();
        $model->getItems();
        $noSortArray = $model->getColumnValues("coordinate");
        $array = array_values(array_unique($noSortArray, SORT_REGULAR));
        $allZipArray = array_map('json_decode', $array);
        return $allZipArray;
    }
}