<?php
namespace Hunters\SearchShopMap\Model\ResourceModel\Collection;

class SaveZipCode extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
    protected function _construct()
    {
        $this->_init(
            \Hunters\SearchShopMap\Model\SaveZipCode::class,
            \Hunters\SearchShopMap\Model\ResourceModel\SaveZipCode::class
        );
    }
}