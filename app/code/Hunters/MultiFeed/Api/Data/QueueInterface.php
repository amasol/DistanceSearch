<?php


namespace Hunters\MultiFeed\Api\Data;

interface QueueInterface extends \Magento\Framework\Api\ExtensibleDataInterface
{

    const CREATED_AT = 'created_at';
    const ENTITY_ID = 'entity_id';
    const UPDATED_AT = 'updated_at';
    const ENTITY_TYPE = 'entity_type';
    const QUEUE_ID = 'queue_id';

    /**
     * Get queue_id
     * @return string|null
     */
    public function getQueueId();

    /**
     * Set queue_id
     * @param string $queueId
     * @return \Hunters\MultiFeed\Api\Data\QueueInterface
     */
    public function setQueueId($queueId);

    /**
     * Retrieve existing extension attributes object or create a new one.
     * @return \Hunters\MultiFeed\Api\Data\QueueExtensionInterface|null
     */
    public function getExtensionAttributes();

    /**
     * Set an extension attributes object.
     * @param \Hunters\MultiFeed\Api\Data\QueueExtensionInterface $extensionAttributes
     * @return $this
     */
    public function setExtensionAttributes(
        \Hunters\MultiFeed\Api\Data\QueueExtensionInterface $extensionAttributes
    );

    /**
     * Get created_at
     * @return string|null
     */
    public function getCreatedAt();

    /**
     * Set created_at
     * @param string $createdAt
     * @return \Hunters\MultiFeed\Api\Data\QueueInterface
     */
    public function setCreatedAt($createdAt);

    /**
     * Get updated_at
     * @return string|null
     */
    public function getUpdatedAt();

    /**
     * Set updated_at
     * @param string $updatedAt
     * @return \Hunters\MultiFeed\Api\Data\QueueInterface
     */
    public function setUpdatedAt($updatedAt);

    /**
     * Get entity_id
     * @return string|null
     */
    public function getEntityId();

    /**
     * Set entity_id
     * @param string $entityId
     * @return \Hunters\MultiFeed\Api\Data\QueueInterface
     */
    public function setEntityId($entityId);

    /**
     * Get entity_type
     * @return string|null
     */
    public function getEntityType();

    /**
     * Set entity_type
     * @param string $entityType
     * @return \Hunters\MultiFeed\Api\Data\QueueInterface
     */
    public function setEntityType($entityType);
}
