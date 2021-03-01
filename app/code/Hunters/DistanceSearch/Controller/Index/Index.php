<?php

namespace Hunters\DistanceSearch\Controller\Index;

use Magento\Framework\Controller\ResultFactory;
use Hunters\DistanceSearch\Service\AddProductDatabase;

class Index extends \Magento\Framework\App\Action\Action
{
    protected $resultJsonFactory;
    /**
     * @var AddProductDatabase
     */
    private $AddProductDatabase;

    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory,
        AddProductDatabase $AddProductDatabase
    )
    {
        $this->resultJsonFactory = $resultJsonFactory;
        $this->AddProductDatabase = $AddProductDatabase;
        parent::__construct($context);
    }


    public function execute()
    {
//        $result = $this->resultJsonFactory->create();
//        if ($this->getRequest()->isAjax()) {
//            $post = $this->getRequest()->getPost();
//            $this->AddProductDatabase->total($this->getRequest()->getParam('zip'), $this->getRequest()->getParam('address'));
//            $this->AddProductDatabase->total();


//            $total = $this->AddProductDatabase->total();
//            file_put_contents('/var/www/html/viviscal/test.log', "data: ". json_encode($total) . "\n", FILE_APPEND | LOCK_EX);
//            file_put_contents(BP. '/var/log/data.log', print_r($this->getRequest()->getParams('zip'), true) ."\n", FILE_APPEND | LOCK_EX);

//            file_put_contents('/var/www/html/viviscal/test.log', print_r($this->getRequest()->getParam('address'), true) ."\n", FILE_APPEND | LOCK_EX);

//            file_put_contents('/var/www/html/viviscal/test.log', print_r($this->getRequest()->getParams(), true) ."\n", FILE_APPEND | LOCK_EX);
//            file_put_contents('/var/www/html/viviscal/test.log', print_r($this->getRequest()->getParam('zip'), true) ."\n", FILE_APPEND | LOCK_EX);
//        }

        $page = $this->resultFactory->create(ResultFactory::TYPE_PAGE);
        return $page;
    }
}

