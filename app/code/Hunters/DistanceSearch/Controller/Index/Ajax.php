<?php

namespace Hunters\DistanceSearch\Controller\Index;

use Magento\Framework\Controller\ResultFactory;
//use Hunters\DistanceSearch\Service\AddProductDatabase;
use Hunters\DistanceSearch\Service\GoogleApiSearchZip;

class Ajax extends \Magento\Framework\App\Action\Action
{
    private $searchZip;

    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Framework\Json\Helper\Data $jsonHelper,
        \Magento\Framework\View\Result\PageFactory $resultFactory,
        SearchZip $searchZip
    ) {
        $this->jsonHelper = $jsonHelper;
        $this->resultFactory = $resultFactory;
        $this->searchZip = $searchZip;
        parent::__construct($context);
    }

    public function execute()
    {
        file_put_contents('/var/www/html/magento224/test.log', "data: ". json_encode("2") . "\n", FILE_APPEND | LOCK_EX);


        /** @var \Magento\Framework\View\Result\Page $resultPage */
        $resultPage = $this->resultFactory->create(ResultFactory::TYPE_PAGE);

        /** @var \Magento\Framework\Controller\Result\Raw $response */
        $response = $this->resultFactory->create(ResultFactory::TYPE_RAW);

        $response->setHeader('Content-type', 'text/plain');
        if ($this->getRequest()->isAjax()) {
            $post = $this->getRequest()->getPost();
            $addres = $this->searchZip->total2($post['zip']);
            $response->setContents(
                $this->jsonHelper->jsonEncode(
                    [
                        'loc' => $addres,
                    ]
                )
            );
        }
        return $response;
    }
}
