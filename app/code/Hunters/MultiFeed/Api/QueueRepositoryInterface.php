<?php


namespace Hunters\MultiFeed\Api;

use Magento\Framework\Api\SearchCriteriaInterface;

interface QueueRepositoryInterface
{

    /**
     * Save Queue
     * @param \Hunters\MultiFeed\Api\Data\QueueInterface $queue
     * @return \Hunters\MultiFeed\Api\Data\QueueInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function save(
        \Hunters\MultiFeed\Api\Data\QueueInterface $queue
    );

    /**
     * Retrieve Queue
     * @param string $queueId
     * @return \Hunters\MultiFeed\Api\Data\QueueInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getById($queueId);

    /**
     * Retrieve Queue matching the specified criteria.
     * @param \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
     * @return \Hunters\MultiFeed\Api\Data\QueueSearchResultsInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getList(
        \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
    );

    /**
     * Delete Queue
     * @param \Hunters\MultiFeed\Api\Data\QueueInterface $queue
     * @return bool true on success
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function delete(
        \Hunters\MultiFeed\Api\Data\QueueInterface $queue
    );

    /**
     * Delete Queue by ID
     * @param string $queueId
     * @return bool true on success
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function deleteById($queueId);
}
