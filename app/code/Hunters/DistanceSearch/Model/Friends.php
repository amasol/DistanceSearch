<?php
namespace Hunters\DistanceSearch\Model;

class Friends extends \Magento\Framework\Model\AbstractModel
{
    protected function _construct()
    {
        $this->_init(\Hunters\DistanceSearch\Model\ResourceModel\Friends::class);
    }
}





// namespace Hunters\DistanceSearch\Model;

// use \Hunters\DistanceSearch\Api\Data\FriendInterface;

// class Friends extends \Magento\Framework\Model\AbstractModel implements FriendInterface
// {
//     protected $_eventPrefix = 'sadoighwqjeghwqegjkwqheszgdfhasdjkashekhwq213213321';

//     protected $_eventObject = 'popototot';

//     // friend_model_event_stuff_load_before

//     /**
//      * @inheritDoc
//      */
//     public function getId()
//     {
//         return $this->getData(self::FIELD_NAME_ID);
//     }

//     /**
//      * @inheritDoc
//      */
//     public function getAge()
//     {
//         return $this->getData(self::FIELD_NAME_AGE);
//     }

//     /**
//      * @inheritDoc
//      */
//     public function setAge($age)
//     {
//         return $this->setData($age, self::FIELD_NAME_AGE);
//     }

//     /**
//      * @inheritDoc
//      */
//     public function getName()
//     {
//         return $this->getData(self::FIELD_NAME_NAME);
//     }

//     /**
//      * @inheritDoc
//      */
//     public function setName($name)
//     {
//         return $this->setData($name, self::FIELD_NAME_NAME);
//     }

//     /**
//      * @inheritDoc
//      */
//     public function getComment()
//     {
//         return $this->getData(self::FIELD_NAME_COMMENT);
//     }

//     /**
//      * @inheritDoc
//      */
//     public function setComment($comment)
//     {
//         return $this->setData($comment, self::FIELD_NAME_COMMENT);
//     }

//     /**
//      * @inheritDoc
//      */
//     public function getCreatedAt()
//     {
//         return $this->getData(self::FIELD_NAME_CREATED_AT);
//     }

//     /**
//      * @inheritDoc
//      */
//     public function getUpdatedAt()
//     {
//         return $this->getData(self::FIELD_NAME_UPDATED_AT);
//     }

//     /**
//      * @inheritDoc
//      */
//     protected function _construct()
//     {
//         $this->_init(\Hunters\DistanceSearch\Model\ResourceModel\Friends::class);
//     }
// }

