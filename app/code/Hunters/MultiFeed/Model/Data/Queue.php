<?php


namespace Hunters\MultiFeed\Model\Data;

use Hunters\MultiFeed\Api\Data\QueueInterface;

class Queue extends \Magento\Framework\Api\AbstractExtensibleObject implements QueueInterface
{

    /**
     * Get queue_id
     * @return string|null
     */
    public function getQueueId()
    {
        return $this->_get(self::QUEUE_ID);
    }

    /**
     * Set queue_id
     * @param string $queueId
     * @return \Hunters\MultiFeed\Api\Data\QueueInterface
     */
    public function setQueueId($queueId)
    {
        return $this->setData(self::QUEUE_ID, $queueId);
    }

    /**
     * Retrieve existing extension attributes object or create a new one.
     * @return \Hunters\MultiFeed\Api\Data\QueueExtensionInterface|null
     */
    public function getExtensionAttributes()
    {
        return $this->_getExtensionAttributes();
    }

    /**
     * Set an extension attributes object.
     * @param \Hunters\MultiFeed\Api\Data\QueueExtensionInterface $extensionAttributes
     * @return $this
     */
    public function setExtensionAttributes(
        \Hunters\MultiFeed\Api\Data\QueueExtensionInterface $extensionAttributes
    ) {
        return $this->_setExtensionAttributes($extensionAttributes);
    }

    /**
     * Get created_at
     * @return string|null
     */
    public function getCreatedAt()
    {
        return $this->_get(self::CREATED_AT);
    }

    /**
     * Set created_at
     * @param string $createdAt
     * @return \Hunters\MultiFeed\Api\Data\QueueInterface
     */
    public function setCreatedAt($createdAt)
    {
        return $this->setData(self::CREATED_AT, $createdAt);
    }

    /**
     * Get updated_at
     * @return string|null
     */
    public function getUpdatedAt()
    {
        return $this->_get(self::UPDATED_AT);
    }

    /**
     * Set updated_at
     * @param string $updatedAt
     * @return \Hunters\MultiFeed\Api\Data\QueueInterface
     */
    public function setUpdatedAt($updatedAt)
    {
        return $this->setData(self::UPDATED_AT, $updatedAt);
    }

    /**
     * Get entity_id
     * @return string|null
     */
    public function getEntityId()
    {
        return $this->_get(self::ENTITY_ID);
    }

    /**
     * Set entity_id
     * @param string $entityId
     * @return \Hunters\MultiFeed\Api\Data\QueueInterface
     */
    public function setEntityId($entityId)
    {
        return $this->setData(self::ENTITY_ID, $entityId);
    }

    /**
     * Get entity_type
     * @return string|null
     */
    public function getEntityType()
    {
        return $this->_get(self::ENTITY_TYPE);
    }

    /**
     * Set entity_type
     * @param string $entityType
     * @return \Hunters\MultiFeed\Api\Data\QueueInterface
     */
    public function setEntityType($entityType)
    {
        return $this->setData(self::ENTITY_TYPE, $entityType);
    }
}
