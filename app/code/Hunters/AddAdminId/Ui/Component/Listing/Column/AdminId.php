<?php

namespace Hunters\AddAdminId\Ui\Component\Listing\Column;

use Magento\Sales\Api\OrderRepositoryInterface;
use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Ui\Component\Listing\Columns\Column;
use Magento\User\Model\UserFactory;
use Magento\Backend\Model\UrlInterface;
use \Psr\Log\LoggerInterface;
use Magento\Framework\App\ResourceConnection;

class AdminId extends Column
{
    protected $_orderRepository;
    protected $_adminUserFactory;
    protected $urlBuilder;
    private $_logger;
    protected $_resourceConnection;

    public function __construct(
        ContextInterface            $context,
        UiComponentFactory          $uiComponentFactory,
        OrderRepositoryInterface    $orderRepository,
        UserFactory                 $adminUserFactory,
        UrlInterface                $urlBuilder,
        LoggerInterface             $logger,
        array $components           = [],
        array $data                 = [],
        ResourceConnection $resourceConnection
    )
    {
        $this->_orderRepository     =   $orderRepository;
        $this->urlBuilder           =   $urlBuilder;
        $this->_adminUserFactory    =   $adminUserFactory->create();
        $this->_logger              =   $logger;

        $this->_resourceConnection  =   $resourceConnection;
        parent::__construct($context, $uiComponentFactory, $components, $data);
    }

    public function prepareDataSource(array $dataSource)
    {
        if (isset($dataSource['data']['items'])) {
            foreach ($dataSource['data']['items'] as & $item) {
                $admin_id = $this->getNameAdmin($item['increment_id']);
                if ($admin_id) {
                    try {
                        $viewUrlPath        =   $this->getData('config/viewUrlPath') ?: '#';
                        $urlEntityParamName =   $this->getData('config/urlEntityParamName') ?: 'user_id';
                        $_user              =   $this->_adminUserFactory->load($admin_id);

                        if($_user && $_user->getId()){
                            $item[$this->getData('name')] = '<a href=" ' . $this->urlBuilder->getUrl(
                                    $viewUrlPath,
                                    [
                                        $urlEntityParamName => $admin_id
                                    ]
                                ) . '" target="_blank">' . $_user->getFirstName() . ' ' . $_user->getLastName() . '</a>';
                        } else {
                            $item[$this->getData('name')] = __('User was removed');
                        }

                    } catch (\Exception $e) {
                        $this->_logger->critical('Error message', ['exception' => $e]);
                    }
                } else {
                    $item[$this->getData('name')] = __("No");
                }
            }
        }
        return $dataSource;
    }

    private function getNameAdmin($incrementId) {
        $connection = $this->_resourceConnection->getConnection();
        $tableName = $this->_resourceConnection->getTableName('sales_order');
        $sql = $connection->select()->from(
            $tableName, 'admin_id'
        )->where(
            'increment_id = :incrementId'
        );
        $bind = [
            'incrementId' => $incrementId,
        ];
        $result = $connection->fetchOne($sql, $bind);
        return $result;
    }
}