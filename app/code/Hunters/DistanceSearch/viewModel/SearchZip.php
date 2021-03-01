<?php

namespace Hunters\DistanceSearch\viewModel;

class SearchZip implements \Magento\Framework\View\Element\Block\ArgumentInterface
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
//        echo $this->getRequest()->getParam('zip');
//      return var_dump($this->AddProductDatabase->total('asdfasg'));
      return var_dump($this->AddProductDatabase->total());

//        print_r($this->AddProductDatabase->total()['results'][0]['geometry']['location']);
//        echo "</br>";
//        $this->AddProductDatabase->print_my();
//        $result = print_r($this->AddProductDatabase->total()['results'][0]['geometry']['location']);
//      return ;
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
