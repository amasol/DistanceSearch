<?php

namespace Hunters\Smtp\Helper;

class Sender extends \Magento\Framework\App\Helper\AbstractHelper
{

    protected $_scopeConfig;
    protected $_dir;
    protected $_logger;

    const STATUS_NEW = 1;
    const STATUS_SENT = 2;
    const STATUS_FAILED = -1;

    const SEND_LIMIT = 1;


    public function __construct(
      \Magento\Framework\App\Helper\Context $context,
      \Magento\Framework\Filesystem\DirectoryList $dir,
      \Hunters\Smtp\Model\QueueFactory $queueFactory,
      \Hunters\Smtp\Helper\Data $dataHelper,
      \Hunters\Smtp\Helper\Logger $logger
    ){
        parent::__construct($context);
        $this->queueFactory = $queueFactory;
        $this->_dir = $dir;
        $this->_scopeConfig = $context->getScopeConfig();
        $this->_dataHelper = $dataHelper;
        $this->_logger = $logger;
    }

    public function sendMail()
    {
          $file = $this->_dir->getPath('var') . '/' . $this->_dataHelper->getLogFile();

          try {
            $queue = $this->queueFactory->create();
        		$collection = $queue->getCollection();

        		$collection->filterOrder(
              self::STATUS_NEW,
              $this->_dataHelper->getSendLimit() ?? self::SEND_LIMIT
            );

        	foreach($collection as $message){
              if (empty($message->getEmail())) {
		$message->setStatus(self::STATUS_FAILED);
                $message->save();
                continue;
              }

              $builder = new \Phpforce\SoapClient\ClientBuilder(
                $this->_dir->getPath('var') . '/' . $this->_dataHelper->getWsdlFilePath($message->getStoreId()),
                $this->_dataHelper->getSfUsername($message->getStoreId()),
                $this->_dataHelper->getSfPassword($message->getStoreId()),
                $this->_dataHelper->getSfToken($message->getStoreId())
              );

              $SfMessage = new \Phpforce\SoapClient\Request\SingleEmailMessage;

              // to array
              $SfMessage->bccAddresses = explode(
                ',',
                $message->getEmail()
              );
              
              $subject = $message->getSubject();
              $SfMessage->subject = $message->getSubject() ?? $subject['Subject'];
              $SfMessage->bccSender = true;

              $SfMessage->htmlBody = $message->getBody();
              $SfMessage->toAddresses = $message->getFrom();

              if ($this->_dataHelper->getSfEmailObjectId()) {
                  $SfMessage->orgWideEmailAddressId = $this->_dataHelper->getSfEmailObjectId($message->getStoreId());
              };

              $emails = [
                $SfMessage
              ];

              $client = $builder->build();
              $result = $client->sendEmail($emails);


              // Update status
              $message->setStatus(self::STATUS_SENT);
              $message->save();

              $msg = 'Message sent: ' . json_encode([
                'email' => $message->getEmail(),
                'subject' => $message->getSubject(),
                'message' => $message->getBody(),
                'time' => date('Y/m/d H:i:s'),
                'storeId' => $message->getStoreId()
              ]);

              $this->_logger->log(
                $file,
                $msg
              );

            }

          } catch (\Exception $e){
            $msg = 'Send Error: ' . $e->getMessage();
            $file = $this->_dir->getPath('var') . '/' . $this->_dataHelper->getErrorLogFile();

            $this->_logger->log(
              $file,
              $msg
            );

          }

      }
}
