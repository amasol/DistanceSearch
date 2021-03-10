<?php

namespace Hunters\Smtp\Plugin\Magento\Framework\Mail;

class Transport extends \Zend_Mail_Transport_Smtp
{
    /**
     * @var \Hunters\Smtp\Helper\Data
     */
    protected $_dataHelper;
    protected $_queue;

    /**
     * @param \Magento\Smtp\Helper\Data $dataHelper
     */
    public function __construct(
        \Hunters\Smtp\Helper\Data $dataHelper,
        \Hunters\Smtp\Helper\Queue $queue
    )
    {
        $this->_dataHelper = $dataHelper;
        $this->_queue = $queue;
    }

    /**
     * @param \Magento\Framework\Mail\TransportInterface $subject
     * @param \Closure $proceed
     */
    public function aroundSendMessage(\Magento\Framework\Mail\TransportInterface $subject, \Closure $proceed)
    {
        if ($this->_dataHelper->isConfigurationEnabled() && !$subject->getMessage()->getBody()->isMultiPart()) {
            $this->_queue->addToQueue(
                $subject->getMessage()
            );
        } else {
            $proceed();
        }
    }
}
