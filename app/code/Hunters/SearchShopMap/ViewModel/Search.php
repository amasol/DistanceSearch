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


    private $infoView;

    public function __construct(
        \Hunters\SearchShopMap\Model\ResourceModel\Collection\SaveZipCodeFactory $saveZipCodeCollectionFactory,

        \Hunters\SearchShopMap\Setup\Patch\Schema\AddAddressDatabase $shema,
        \Hunters\SearchShopMap\Controller\Page\Coordinate $infoView
    ) {
        $this->saveZipCodeCollectionFactory = $saveZipCodeCollectionFactory;

        $this->shema = $shema;
        $this->infoView = $infoView;
    }
    
    public function coordinateArray()
    {
//        удалить это не с моего модуля
//        $this->shema->apply();
//        $this->infoView->execute();

        $model = $this->saveZipCodeCollectionFactory->create();

        $coordinate = $model->getData();
//        echo "<pre>";
//        print_r($coordinate);
//        echo "</pre>";


//       на выходе мы имеем
//        https://i.imgur.com/Cy23d1W.png
        return $coordinate;
    }
}