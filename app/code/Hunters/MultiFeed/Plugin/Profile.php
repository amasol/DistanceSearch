<?php

namespace Hunters\MultiFeed\Plugin;

class Profile
{

  protected $queue;

  public function __construct(
    \Hunters\MultiFeed\Helper\Queue $queue
  ) {
    $this->queue = $queue;
  }

	public function afterSave(\Toppik\Subscriptions\Model\Profile $subject, $result)
	{
    $status = $subject->getStatus();
    $storeId = $subject->getStoreId();
    $oldStatus = $subject->getOrigData('status');

    if ($status != $oldStatus) {
      switch ($status) {
        case 'active':
          $this->queue->add($subject->getProfileId(), 'active_subs', $storeId);
          break;

        case 'cancelled':
          $this->queue->add($subject->getProfileId(), 'cancelled_subs', $storeId);
          break;

        case 'suspended':
          $this->queue->add($subject->getProfileId(), 'suspended_subs', $storeId);
          break;
      }
    }

    return $result;
	}

}
