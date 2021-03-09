<?php

namespace Hunters\SearchShopMap\Model\ResourceModel;

class SaveZipCode extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
    protected function _construct()
    {
        $this->_init('AddressZipCode', 'entity_id');
    }
}