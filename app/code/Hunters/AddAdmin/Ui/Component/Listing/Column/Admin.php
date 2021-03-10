<?php

namespace Hunters\AddAdmin\Ui\Component\Listing\Column;

use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Ui\Component\Listing\Columns\Column;

class Admin extends Column
{
    protected $_resourceConnection;

    public function __construct(
        ContextInterface $context,
        UiComponentFactory $uiComponentFactory,
        \Magento\Framework\App\ResourceConnection $resourceConnection,
        array $components = [],
        array $data = []
    ) {
        $this->_resourceConnection = $resourceConnection;
        parent::__construct($context, $uiComponentFactory, $components, $data);
    }

    private function getNameAdmin($incrementId) {
        $connection = $this->_resourceConnection->getConnection();
        $tableName = $this->_resourceConnection->getTableName('sales_creditmemo');
        $sql = $connection->select()->from(
            $tableName, 'admin_refund'
        )->where(
            'increment_id = :incrementId'
        );
        $bind = [
            'incrementId' => $incrementId,
        ];
        $result = $connection->fetchOne($sql, $bind);
        return $result ? $result : "unknown";
    }

    public function prepareDataSource(array $dataSource)
    {
        if (isset($dataSource['data']['items'])) {
            foreach ($dataSource['data']['items'] as & $item) {
                $item['admin_refund'] = $this->getNameAdmin($item['increment_id']);
            }
        }
        return $dataSource;
    }

}
