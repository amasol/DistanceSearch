<?php
namespace Hunters\SearchShopMap\Model;

class Friends extends \Magento\Framework\Model\AbstractModel
{
    protected function _construct()
    {
        $this->_init(\Hunters\SearchShopMap\Model\ResourceModel\Friends::class);
    }
}