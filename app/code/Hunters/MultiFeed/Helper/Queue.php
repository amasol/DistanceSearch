<?php

namespace Hunters\MultiFeed\Helper;

use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Hunters\MultiFeed\Model\QueueFactory;
use Hunters\MultiFeed\Helper\Feeds\Subs;
use Hunters\MultiFeed\Helper\Feeds\Order;
use Hunters\MultiFeed\Helper\Feeds\CancelOrder;
use Hunters\MultiFeed\Helper\UpdateProjectMedia;
use Magento\Framework\App\ResourceConnection;
use Hunters\MultiFeed\Helper\Feeds\Account;
use Hunters\MultiFeed\Helper\Feeds\Contact;

class Queue extends AbstractHelper
{
    const STATUS_NOT_SENT = 0;
    const STATUS_SENT = 1;

    protected $scopeConfig;
    protected $queueFactory;
    protected $subsHelper;
    protected $orderHelper;
    protected $cancelOrderHelper;
    protected $resource;
    protected $updateProjectMediaHelper;
    protected $accountHelper;
    protected $contactHelper;


    public function __construct(
        ScopeConfigInterface $scopeConfig,
        QueueFactory $queueFactory,
        Subs $subsHelper,
        Order $orderHelper,
        CancelOrder $cancelOrderHelper,
        UpdateProjectMedia $updateProjectMediaHelper,
        ResourceConnection $resource,
        Account $account,
        Contact $contact
    )
    {
        $this->queueFactory = $queueFactory;
        $this->subsHelper = $subsHelper;
        $this->orderHelper = $orderHelper;
        $this->cancelOrderHelper = $cancelOrderHelper;
        $this->resource = $resource;
        $this->updateProjectMediaHelper = $updateProjectMediaHelper;
        $this->accountHelper = $account;
        $this->contactHelper = $contact;
    }

    public function add($entity_id, $entity_type, $store_id = null)
    {
        $queue = $this->queueFactory->create();
        $queue->setData('entity_id', $entity_id);
        $queue->setData('entity_type', $entity_type);
        $queue->setData('status', self::STATUS_NOT_SENT);
        $queue->setData('store_id', $store_id);
        $queue->save();
    }

    public function generateFeeds()
    {
        $_ids = $this->getFilterIds();

        $files = [];
        foreach ($_ids as $storeId => $types) {
            foreach ($types as $type => $ids) {
                $this->updateProjectMediaHelper->update($ids);
                switch ($type) {
                    case 'complete_orders':
                        $files[] = $this->orderHelper->generate($ids, $storeId);
                        break;
                    case 'account':
                        $files[] = $this->accountHelper->generate($ids, $storeId);
                        break;
                    case 'contact':
                        $files[] = $this->contactHelper->generate($ids, $storeId);
                        break;
                    case 'closed_orders':
//                        $files[] = $this->orderHelper->generateReturnOrders($ids, $storeId);
                        break;
                    case 'canceled_orders':
//                        $files[] = $this->cancelOrderHelper->generate($ids, $storeId);
                        break;
                }
                $this->updateQueue($ids);
            }
        }
        return $files;
    }

    public function getFilterIds()
    {
        $queue = $this->queueFactory->create();
        $collection = $queue
            ->getCollection()
            ->addFieldToFilter('status', self::STATUS_NOT_SENT);
        $idsToChange = [];

        foreach ($collection as $item) {
            $storeId = $item->getStoreId();
            $type = $item->getEntityType();
            $entity_id = (int)$item->getEntityId();

        // get array with type -> [ids:int]
            isset($idsToChange[$storeId][$type]) ?
                (in_array($entity_id, $idsToChange[$storeId][$type])
                    ?: array_push($idsToChange[$storeId][$type], $entity_id)
                ) : ($idsToChange[$storeId][$type] = [$entity_id]);
        }
        return $idsToChange;
    }

    public function updateQueue($ids)
    {
        if (is_array($ids) && count($ids)) {
            $connection = $this->resource
                ->getConnection(
                    ResourceConnection::DEFAULT_CONNECTION
                );

            $sql = sprintf(
            'UPDATE %s SET %s WHERE %s',
            $this->resource->getTableName('hunters_multifeed_queue'),
            $connection->quoteInto('status = ?', self::STATUS_SENT),
            $connection->quoteInto('entity_id IN (?)', $ids)
            );

            $connection->query($sql);
        }
    }
}
