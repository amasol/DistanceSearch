<?php
namespace Hunters\SearchShopMap\ViewModel;

use Magento\Framework\View\Element\Block\ArgumentInterface;
use Hunters\SearchShopMap\Service\SearchZip;

class Search implements \Magento\Framework\View\Element\Block\ArgumentInterface
{

    private $searchZip;

    public $model = null;
//
//    /**
//     * @var \Hunters\SearchShopMap\Model\Friends
//     */
//    protected $friendsFactory;
//
//    /**
//     * @var \Hunters\SearchShopMap\Model\ResourceModel\Friends $friendsResourceModel;
//     */
//    protected $friendsResourceModel;

    /**
     * @var \Hunters\SearchShopMap\Model\ResourceModel\Collection\Friends
     */
    protected $friendsCollectionFactory;

    public function __construct(
//        \Hunters\SearchShopMap\Model\FriendsFactory $friendsFactory,
//        \Hunters\SearchShopMap\Model\ResourceModel\Friends $friendsResourceModel,
        \Hunters\SearchShopMap\Model\ResourceModel\Collection\FriendsFactory $friendsCollectionFactory,

        \Hunters\SearchShopMap\Service\SearchZip $searchZip
    ) {
//        $this->friendsFactory = $friendsFactory;
//        $this->friendsResourceModel = $friendsResourceModel;
        $this->friendsCollectionFactory = $friendsCollectionFactory;

        $this->searchZip = $searchZip;
    }

    public function lessonThree()
    {

//
//         $model = $this->friendsCollectionFactory->create();
//         $model->getColumnValues('postcode');
//         return $model;

//        $model = $this->getFriendModel($id)->getData('postcode');
//        $zip = $this->searchZip->total($model);
//         $zip = $this->searchZip->total($this->getFriendModel($id)->getData('postcode'));
//        return $zip;




//         $myPackFriends = $this->friendsFactory->create();
//         $myPackFriends->getData('postcode','1');
//         return $myPackFriends;

         $model = $this->friendsCollectionFactory->create();
         $model->getItems();
//         $zipArray = $model->getColumnValues("postcode");
         $allZipArray = $model->getColumnValues("postcode");
//         $allZipArray;

         return $allZipArray;
//         return $zipArray;
    }

//    public function getFriendName($id)
//    {
//        return $this->getFriendModel($id)->getData('postcode');
//    }
//
//    public function getFriendModel($id)
//    {
//        if ($this->model === null ) {
//            // Creates new instance of the 'friends object'. A.K.A. individual line/row from mysql table.
//            $model = $this->friendsFactory->create();
//
//            // Loads the data from the database and puts it to the $model variable.
//            $this->friendsResourceModel->load($model, $id);
//
//            // Save model to the class, so that same model won't be loaded numerous times.
//            $this->model = $model;
//        }
//
//        return $this->model;
//    }


}