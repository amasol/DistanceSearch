<?php

namespace Hunters\SearchShopMap\Model\ResourceModel;

class Friends extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
    protected function _construct()
    {
        $this->_init('company', 'entity_id');
    }
}