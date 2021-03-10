<?php

namespace Hunters\Smtp\Helper;

class Queue extends \Magento\Framework\App\Helper\AbstractHelper
{

    protected $_scopeConfig;
    protected $_dir;
    protected $_dataHelper;
    protected $_logger;


    public function __construct(
      \Magento\Framework\App\Helper\Context $context,
      \Hunters\Smtp\Model\Queue $queue,
      \Hunters\Smtp\Model\QueueDesc $queueDesc,
      \Magento\Framework\Filesystem\DirectoryList $dir,
      \Hunters\Smtp\Helper\Data $dataHelper,
      \Hunters\Smtp\Helper\Logger $logger
    ){
        parent::__construct($context);
        $this->queue = $queue;
        $this->queueDesc = $queueDesc;
        $this->_dir = $dir;
        $this->_dataHelper = $dataHelper;
        $this->_logger = $logger;
    }

    public function addToQueue($message)
    {
        $file = $this->_dir->getPath('var') . '/' . $this->_dataHelper->getLogFile();
        $msg = 'default';
        try {
            //Data to insert
            //$email = implode(',', $message->getRecipients());
            $emailToList = $message->getTo();
            /*
            //AddressList return, but no 'next'?!
            while($emailO = $emailToList->next()) {
                $emailTo[] = $emailO;
            }
            */
            //quick and dirty for now @todo rewrite it
            $emailTo = array();
            foreach($emailToList as $emailO) {
                $emailTo[] = $emailO->getEmail();
            }
            //introduce validation count > 0.
            $email = implode(',', $emailTo);
            //$body = $message->getRawMessage();
            $body = quoted_printable_decode($message->getBodyText());

	    $storeIdByBaseUrl = null;
            $storeUrl = $this->_dataHelper->getBaseUrlByMessage($body);
            if ($storeUrl !== null) {
                $storeIdByBaseUrl = $this->_dataHelper->getStoreIdByBaseUrl($storeUrl);
            }

            $storeId = $storeIdByBaseUrl !== null ? $storeIdByBaseUrl : $this->_dataHelper->getStoreId();
            $subject =  $message->getSubject();

            $emailFromList = $message->getFrom();
            $emailFrom = array();
            foreach($emailFromList as $emailO) {
                $emailFrom[] = $emailO->getEmail();
            }

            $sent_from = implode(',', $emailFrom);
            $queueModelDesc = $this->queueDesc;
            $queueModel = $this->queue;

            $queueModelDesc->setData('email', $email);
            $queueModelDesc->setData('body', $body);
            $queueModelDesc->setData('subject', $subject);
            $queueModelDesc->setData('sender', $sent_from);
            $queueModelDesc->save();

            $queueModel->setData('mail_desc_id', $queueModelDesc->getMailDescId());
            $queueModel->setData('store_id', $storeId);
            $queueModel->save();


            $queueModel->unsetData();
            $queueModelDesc->unsetData();

            $msg =  'Message added to queue: ' . json_encode([
                    'email' => $email,
                    'subject' => $subject,
                    'time' => date('Y/m/d H:i:s')
            ]);
        } catch (\Exception $e) {
            $msg = 'Queue Error: ' . $e->getMessage();
            $msg.=json_encode([
            'email' => $email,
            'body' => $body,
            'sent_from' => $sent_from,
            'subject' => $subject
            ]);
        } finally {
            $this->_logger->log(
                $file,
                $msg
            );
        }
      }
}

