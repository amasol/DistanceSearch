<?php
namespace Hunters\MultiFeed\Helper;

class UpdateProjectMedia extends \Magento\Framework\App\Helper\AbstractHelper
{
    protected $couponModel;
    protected $ruleModel;
    protected $_resource;
    protected $_connection;

    const MSITE_TABLE = 'microsite_order_detail';

    public function __construct(
        \Magento\SalesRule\Model\Coupon $couponModel,
        \Magento\SalesRule\Model\Rule $ruleModel,
        \Magento\Framework\App\ResourceConnection $resource
    ) {
        $this->couponModel = $couponModel;
        $this->ruleModel = $ruleModel;
        $this->_resource = $resource;
        $this->_connection = $this->_resource->getConnection(\Magento\Framework\App\ResourceConnection::DEFAULT_CONNECTION);
    }


    public function update($order_ids)
    {
        foreach ($order_ids as $order_id) {

            $coupon_code = $this->getCouponFromOrder($order_id);

            if (!$coupon_code)
                continue;

            $sf_code = $this->getSfCampaignFromCoupon($coupon_code);

            if (!$sf_code)
                continue;

            $this->updateProjectMediaCode($order_id, $sf_code, $coupon_code);
        }
    }

    public function getSfCampaignFromCoupon($coupon_code)
    {
        $coupon = $this->couponModel->loadByCode($coupon_code);
        $rule_id = $coupon->getRuleId();

        if ($rule_id) {
            $rule = $this->ruleModel->load($rule_id);
            if ($rule && $rule->getSfCampaignCode())
                return $rule->getSfCampaignCode();
        }

        return null;
    }

    public function getCouponFromOrder($order_id)
    {
        $sql = sprintf("SELECT coupon_code from %s where entity_id = $order_id",
            $this->_resource->getTableName('sales_order'));

        return $this->_connection->fetchOne($sql);
    }

    public function updateProjectMediaCode($order_id, $project_code, $media_code)
    {
        if ($this->checkIfExists($order_id)) {
            $sql = sprintf("UPDATE %s SET project_code = '$project_code', media_code = '$media_code' WHERE order_id = $order_id",
                $this->_resource->getTableName(self::MSITE_TABLE));

            $conn = $this->_connection->prepare($sql);
        }
        else {
            $sql = sprintf("INSERT INTO %s (order_id, project_code, media_code) VALUES (:order_id, :project_code, :media_code)",
                $this->_resource->getTableName(self::MSITE_TABLE));

            $conn = $this->_connection->prepare($sql);
            $conn->bindParam(':order_id', $order_id);
            $conn->bindParam(':project_code', $project_code);
            $conn->bindParam(':media_code', $media_code);
        }

        $conn->execute();
    }

    private function checkIfExists($order_id) {
        $result = false;

        $count = $this->_connection->fetchOne(
            sprintf("SELECT count(*) AS CNT FROM %s WHERE %s",
            $this->_resource->getTableName(self::MSITE_TABLE),
            $this->_connection->quoteInto('order_id = ?', $order_id))
        );

        if ($count > 0) {
            $result = true;
        }

        return $result;
    }
}
