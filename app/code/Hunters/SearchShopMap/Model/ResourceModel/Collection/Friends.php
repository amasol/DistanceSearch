<?php
namespace Hunters\SearchShopMap\Model\ResourceModel\Collection;

class Friends extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
    protected function _construct()
    {
        $this->_init(
            \Hunters\SearchShopMap\Model\Friends::class,
            \Hunters\SearchShopMap\Model\ResourceModel\Friends::class
        );
    }
}