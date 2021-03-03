<?php
namespace Hunters\SearchShopMap\Controller\Page;

use Magento\Framework\Controller\ResultFactory;
use Hunters\SearchShopMap\Service\SearchZip;

class Ajax extends \Magento\Framework\App\Action\Action
{

    private $searchZip;

    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Framework\Json\Helper\Data $jsonHelper,
        \Magento\Framework\View\Result\PageFactory $resultFactory,
        \Hunters\SearchShopMap\Service\SearchZip $searchZip
    ) {
        $this->jsonHelper = $jsonHelper;
        $this->resultFactory = $resultFactory;
        $this->searchZip = $searchZip;
        parent::__construct($context);
    }

    public function execute()
    {
        /**
         * @var \Magento\Framework\Controller\Result\Raw $response
         **/

        $response = $this->resultFactory->create(ResultFactory::TYPE_RAW);

        $response->setHeader('Content-type', 'text/plain');
        if ($this->getRequest()->isAjax()) {

            $post = $this->getRequest()->getPost();
            $zip = $this->searchZip->total($post['zip']);

            $response->setContents(
                $this->jsonHelper->jsonEncode(
                    [
                        'loc' => $zip,
                    ]
                )
            );
        }
        return $response;
    }
}