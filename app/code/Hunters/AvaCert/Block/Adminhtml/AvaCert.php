<?php
namespace Hunters\AvaCert\Block\Adminhtml;

class AvaCert extends \Magento\Backend\Block\Template {


    protected $dataHelper;
    protected $request;
    protected $companyFactory;
    protected $resource;
    protected $session;


    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Hunters\AvaCert\Helper\Data $dataHelper,
 	    \Magento\Framework\App\Request\Http $request,
        \Magento\Company\Model\ResourceModel\Company\CollectionFactory $companyFactory,
        \Magento\Framework\App\ResourceConnection $resource,
        \Magento\Customer\Model\Session $session,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->request = $request;
        $this->dataHelper = $dataHelper;
        $this->companyFactory = $companyFactory;
        $this->resource = $resource;
        $this->session = $session;
    }

    public function getSelectHtml($name, $id, $options = [], $value = null, $class = '') {
        $select = $this->getLayout()->createBlock(
            'Magento\Framework\View\Element\Html\Select'
        )->setName(
            $name
        )->setId(
            $id
        )->setClass(
            'select admin__control-select ' . $class
        )->setValue(
            $value
        )->setOptions(
            $options
        );
        return $select->getHtml();
    }


    public function getZonesHtmlSelect($name, $id, $value = null, $class = '') {
        $options = $this->dataHelper->getExposureZonesOptions();

        return $this->getSelectHtml($name, $id, $options, $value, $class);
    }

    public function getExemptReasonsSelect($name, $id, $value = null, $class = '') {
        $options = $this->dataHelper->getExemptReasonsOptions();

        return $this->getSelectHtml($name, $id, $options, $value, $class);
    }

    public function getCustomerId()
    {
        return $this->request->getParam('id');
    }

    public function getCompanyCustomerIds()
    {
        $companyId = $this->request->getParam('id');
        $customerIds = '';

        if (!$companyId) return;

        $connection = $this->resource->getConnection(\Magento\Framework\App\ResourceConnection::DEFAULT_CONNECTION);
        $table = $connection->getTableName('company_advanced_customer_entity');
        $query = "select customer_id from $table where company_id = $companyId";
        $res = $connection->fetchAll($query);


        foreach ($res as $item) {
            $customerIds .= $item['customer_id'] . ',';
        }

        return trim($customerIds, ',');

    }

    public function getFrontendCustomerId()
    {

        return $this->session->getCustomerId();
    }



}
