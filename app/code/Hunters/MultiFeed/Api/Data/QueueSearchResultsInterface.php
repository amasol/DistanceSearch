<?php


namespace Hunters\MultiFeed\Api\Data;

interface QueueSearchResultsInterface extends \Magento\Framework\Api\SearchResultsInterface
{

    /**
     * Get Queue list.
     * @return \Hunters\MultiFeed\Api\Data\QueueInterface[]
     */
    public function getItems();

    /**
     * Set queue_id list.
     * @param \Hunters\MultiFeed\Api\Data\QueueInterface[] $items
     * @return $this
     */
    public function setItems(array $items);
}
