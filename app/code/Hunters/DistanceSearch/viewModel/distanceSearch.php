<?php

namespace Hunters\DistanceSearch\viewModel;

class DistanceSearch implements \Magento\Framework\View\Element\Block\ArgumentInterface
{
      /**
    * @var \Hunters\DistanceSearch\Service\AddProductDatabase
    */

   private $AddProductDatabase;

   public $model = null;

   protected $friendsFactory;
   protected $friendsResourceModel;
   protected $friendsCollectionFactory;

   public function __construct(
       \Hunters\DistanceSearch\Model\FriendsFactory $friendsFactory,
       \Hunters\DistanceSearch\Model\ResourceModel\Friends $friendsResourceModel,
       \Hunters\DistanceSearch\Model\ResourceModel\Collection\FriendsFactory $friendsCollectionFactory,
       \Hunters\DistanceSearch\Service\AddProductDatabase $AddProductDatabase

   ) {
       $this->AddProductDatabase = $AddProductDatabase;
       $this->friendsFactory = $friendsFactory;
       $this->friendsResourceModel = $friendsResourceModel;
       $this->friendsCollectionFactory = $friendsCollectionFactory;
   }

    public function Test_function()
    {
      return $this->AddProductDatabase->apibazaarvoice();
//      return $this->AddProductDatabase->total();
    }




   // public function lessonThree()
   // {
   //     $myPackFriends = $this->friendsCollectionFactory->create();

   //     return $myPackFriends->getItems();
   // }


   // public function getMyFriendModel($id, $name)
   // {
   //     if ($this->model === null){

   //         $model = $this->friendsFactory->create();

   //         $this->friendsResourceModel->load($model,$id);

   //         $this->model = $model;
   //     }
   //     return $this->model;
   // }


}
