<?php
namespace Hunters\SearchShopMap\ViewModel;

use Magento\Framework\View\Element\Block\ArgumentInterface;
use Hunters\SearchShopMap\Service\SearchZip;
use Hunters\SearchShopMap\Helper\HelpPatch;

use Hunters\SearchShopMap\Setup\Patch\Schema;

class Search implements \Magento\Framework\View\Element\Block\ArgumentInterface
{
    /**
     * @var
     */
    private $saveZipCodeCollectionFactory;
    private $Schema;
    private $helpPatch;
    private $searchZip;

    public $model = null;
    /**
     * @var \Hunters\SearchShopMap\Model\ResourceModel\Collection\Friends
     */
    protected $friendsCollectionFactory;

    public function __construct(
        \Hunters\SearchShopMap\Model\ResourceModel\Collection\FriendsFactory $friendsCollectionFactory,
        \Hunters\SearchShopMap\Model\ResourceModel\Collection\SaveZipCodeFactory $saveZipCodeCollectionFactory,
        \Hunters\SearchShopMap\Service\SearchZip $searchZip,
        \Hunters\SearchShopMap\Helper\HelpPatch $helpPatch,

        \Hunters\SearchShopMap\Setup\Patch\Schema\AddAddressDatabase $Schema
    ) {
        $this->friendsCollectionFactory = $friendsCollectionFactory;
        $this->saveZipCodeCollectionFactory = $saveZipCodeCollectionFactory;
        $this->searchZip = $searchZip;
        $this->helpPatch = $helpPatch;
        $this->Schema = $Schema;
    }

    public function reversJson($allZipJson)
    {
//
        $stack = array();
        $count = count($allZipJson);

        for ($i = 0; $i < $count; $i++) {
            $stack[$i] = json_decode($allZipJson[$i], true);
        }
        return $allZipJson;
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