<?php


namespace Hunters\MultiFeed\Model;

use Magento\Framework\Api\DataObjectHelper;
use Hunters\MultiFeed\Api\Data\QueueInterface;
use Hunters\MultiFeed\Api\Data\QueueInterfaceFactory;

class Queue extends \Magento\Framework\Model\AbstractModel
{

    protected $queueDataFactory;

    protected $_eventPrefix = 'hunters_multifeed_queue';
    protected $dataObjectHelper;


    /**
     * @param \Magento\Framework\Model\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param QueueInterfaceFactory $queueDataFactory
     * @param DataObjectHelper $dataObjectHelper
     * @param \Hunters\MultiFeed\Model\ResourceModel\Queue $resource
     * @param \Hunters\MultiFeed\Model\ResourceModel\Queue\Collection $resourceCollection
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        QueueInterfaceFactory $queueDataFactory,
        DataObjectHelper $dataObjectHelper,
        \Hunters\MultiFeed\Model\ResourceModel\Queue $resource,
        \Hunters\MultiFeed\Model\ResourceModel\Queue\Collection $resourceCollection,
        array $data = []
    ) {
        $this->queueDataFactory = $queueDataFactory;
        $this->dataObjectHelper = $dataObjectHelper;
        parent::__construct($context, $registry, $resource, $resourceCollection, $data);
    }

    /**
     * Retrieve queue model with queue data
     * @return QueueInterface
     */
    public function getDataModel()
    {
        $queueData = $this->getData();
        
        $queueDataObject = $this->queueDataFactory->create();
        $this->dataObjectHelper->populateWithArray(
            $queueDataObject,
            $queueData,
            QueueInterface::class
        );
        
        return $queueDataObject;
    }
}
