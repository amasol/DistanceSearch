<?php

namespace Hunters\AddAdminId\Plugin\Magento\Sales\Order;


class Grid
{
    const MAIN_TABLE = 'sales_order_grid';

    public function afterSearch($intercepter, $collection)
    {
        if ($collection->getMainTable() === $collection->getConnection()->getTableName(self::MAIN_TABLE)) {
            $collection
                ->getSelect()
                ->joinLeft(
                    ['orders' => 'sales_order'],
                    "(main_table.entity_id = orders.entity_id)",
                    [
                        'orders.admin_id as admin_id',
                        ]
                );
        }
        return $collection;
    }
}