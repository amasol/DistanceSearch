<?php


namespace Hunters\MultiFeed\Controller\Adminhtml\Queue;
use \Magento\Framework\Controller\ResultFactory;

class Generate extends \Magento\Backend\App\Action
{

    protected $resultPageFactory;
    protected $worker;
    protected $resultRawFactory;
    protected $fileFactory;
    protected $zipArchive;
    protected $csvHelper;

    /**
     * Constructor
     *
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Magento\Framework\View\Result\PageFactory $resultPageFactory
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        \Magento\Framework\Message\ManagerInterface $messageManager,
        \Hunters\MultiFeed\Helper\Queue $worker,
        \Magento\Framework\Controller\Result\RawFactory $resultRawFactory,
        \Magento\Framework\App\Response\Http\FileFactory $fileFactory,
        \Magento\Framework\Archive\Zip $zipArchive,
        \Hunters\MultiFeed\Helper\Csv $csvHelper
    ) {
        $this->resultPageFactory = $resultPageFactory;
        $this->messageManager = $messageManager;
        $this->worker = $worker;
        $this->resultRawFactory = $resultRawFactory;
        $this->fileFactory = $fileFactory;
        $this->zipArchive = $zipArchive;
        $this->csvHelper = $csvHelper;

        parent::__construct($context);
    }

    /**
     * Index action
     *
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {

      $files = $this->worker->generateFeeds();

      try {
        if ( empty($files) ) {
          $this->messageManager->addSuccessMessage('Generated Successfully');

          $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
          $resultRedirect->setUrl($this->_redirect->getRefererUrl());

          return $resultRedirect;
        }
        $this->csvHelper->getPackFile($files);
      } catch (\Exception $e) {
        $this->messageManager->addSuccessMessage('Generated Successfully');
        $this->messageManager->addErrorMessage($e->getMessage());

        $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
        $resultRedirect->setUrl($this->_redirect->getRefererUrl());

        return $resultRedirect;
      }
    }
}
