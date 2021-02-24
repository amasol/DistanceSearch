<?php
namespace Hunters\DistanceSearch\Model\ResourceModel\Collection;

class Friends extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
    protected function _construct()
    {
        $this->_init(
            \Hunters\DistanceSearch\Model\Friends::class,
            \Hunters\DistanceSearch\Model\ResourceModel\Friends::class
        );
    }
}
