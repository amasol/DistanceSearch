<?php

namespace Hunters\AddAdmin\Model;

class Display
{
    protected $_resourceConnection;
    protected $_incrementId;

    public function __construct(
        \Magento\Framework\App\ResourceConnection $resourceConnection,
        \Hunters\AddAdmin\Helper\GetIdRefund $getIdRefund,
        array $data = []
    )
    {
        $this->_resourceConnection = $resourceConnection;
        $this->_incrementId = $getIdRefund;
    }

    public function getNameAdmin()
    {
        $incrementId = $this->_incrementId->getNameAdmin();
        $connection = $this->_resourceConnection->getConnection();
        $tableName = $this->_resourceConnection->getTableName('sales_creditmemo');
        $sql = 'SELECT admin_refund FROM ' . $tableName . ' WHERE entity_id = ' . $incrementId;
        $result = $connection->fetchOne($sql);
        if ($result == NULL)
            $result = "unknown";
        return $result;
    }
}