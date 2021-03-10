<?php


namespace Hunters\MultiFeed\Helper\Feeds;

use Hunters\MultiFeed\Helper\FeedInterface;
use Hunters\MultiFeed\Helper\OrderConfiguration;

class Order extends OrderConfiguration implements FeedInterface
{
    public function generate($filterIds, $storeId = null)
    {
        $data = [];
        $data[] = $this->getHeaders();
        sort($filterIds);
        foreach ($filterIds as $id) {
            $order = $this->_order->load($id);

            if ($order->getStatus() == self::COMPLETE_ORDER_STATUS) {
                $adminUser = $this->_adminUser->load($this->getAdminId($order))->getName();
                $shipment = $this->_shipping->load($order->getEntityId(), 'order_id');
                $item = $this->_item->load($order->getEntityId(), 'order_id');

                $tax = $this->_getItemsTax($this->getEntityId($order));
                $itemPrice = $this->_getItemsPrice($this->getEntityId($order));
                $psnValue = $this->_getPsnValue($this->getEntityId($order));
                $qtyShipped = $this->_getQryShipped($this->getEntityId($order));
                $discountItems = $this->_getOrderDiscount($this->getEntityId($order));

                $orderMainData = $this->generatorOrderData($itemPrice, $tax, $psnValue, $qtyShipped, $discountItems);
                $orders = [];
                $itemRefund = [];
                while ($orderMainData->valid()) {
                    $parentSku = $this->getParenSku($this->getEntityId($order), $orderMainData->current()['itemMaterialNumber']);
                    $skipRow = '';
                    $freight = '';
                    $refund = '';
                    if  ($this->_helper->isAmountModeEnabled() && !empty($parentSku)
                            && !in_array($parentSku, $itemRefund)) {
                        $valRefund = $this->_getItemRefund($this->getEntityId($order), $parentSku);
                        $refund = $valRefund == 0 ? '' : $valRefund ;
                        $itemRefund[] = $parentSku;
                    }
                    if (!in_array($this->getEntityId($order), $orders)) {
                        $skipRow = 1;
                        $freight = $this->getFreight($order);
                        $orders[] = $this->getEntityId($order);
                    }
                    $data[] = [
                        $this->getIncrementId($order),
                        $this->convertDate($this->getCreateDate($order)),
                        $this->convertDate($this->getCreateDate($order)),
			$this->convertDate($shipment->getCreatedAt()),
                        $parentSku,
                        $orderMainData->current()['itemMaterialNumber'],
			intval($this->getQryOrderedByOrderId($this->getEntityId($order), $orderMainData->current()['itemMaterialNumber'])),
//                        intval($this->getQtyOrder($item)),
                        $orderMainData->current()['itemPrice'],
                        intval($orderMainData->current()['qtyShipped']) * floatval($orderMainData->current()['itemPrice']),
                        $orderMainData->current()['qtyShipped'],
                        $orderMainData->current()['itemTax'],
                        $this->getOrderGrandTotal($order),
			$this->getMediaCodeSql($order->getEntityId()),
			$this->getProjectCodeSql($order->getEntityId()),
//                        $this->getPromoCode($orderDetail),
//                        $this->getProjectCode($orderDetail),
                        $this->getOrderStatus($order),
                        $this->getCompanyNameByCustomerId($this->getCustomerId($order)), // Company Id
                        $freight,
                        '',
                        strval($this->getShippingAddress($order)->getData("street")),
                        $this->getShippingAddress($order)->getCity(),
                        $this->getShippingAddress($order)->getRegion(),
                        $this->getShippingAddress($order)->getPostcode(),
                        $this->getShippingAddress($order)->getCountryId(),
                        strval($this->getBillingAddress($order)->getData("street")),
                        $this->getBillingAddress($order)->getCity(),
                        $this->getBillingAddress($order)->getRegion(),
                        $this->getBillingAddress($order)->getPostcode(),
                        $this->getBillingAddress($order)->getCountryId(),
                        '',
                        $adminUser,
                        $skipRow,
                        $this->getCouponCode($order),
                        abs($orderMainData->current()['discount'])
//                        $refund
                    ];
                    $orderMainData->next();
                }
            }
        }
        return $this->csv->generate('multifeed_order_complete', $data, $storeId);
    }

    public function generateReturnOrders($filterIds, $storeId = null)
    {
      $collection = $this->collectionFactory
        ->create();

      $ids = implode(',', $filterIds);
      $collection->getSelect()
        ->where(new \Zend_Db_Expr("main_table.order_id in ($ids)"));

      $collection->getSelect()
        ->where(new \Zend_Db_Expr('o.total_paid - o.total_refunded = 0'));

      $collection->getSelect()->join(
              ['o' => 'sales_order'],
              new \Zend_Db_Expr('o.entity_id = main_table.order_id and o.status = "closed"'),
              []
          );

      $collection->getSelect()->joinLeft(
              ['s' => 'sales_shipment'],
              's.order_id = main_table.order_id',
              []
          );

      $scmi = new \Zend_Db_Expr('(select increment_id,order_id,count(*) as memo_qty from sales_creditmemo group by order_id having memo_qty = 1)');
      $collection->getSelect()->join(
              ['scmi' => $scmi],
              'scmi.order_id = main_table.order_id',
              []
      );

      $collection->getSelect()->joinLeft(
              ['i' => 'sales_order_item'],
              'i.item_id = main_table.item_id',
              []
          );

      $collection->getSelect()->joinLeft(
              ['mod' => 'microsite_order_detail'],
              'mod.order_id = o.entity_id',
              []
          );

      $collection->getSelect()->joinLeft(
                ['shipping' => 'sales_order_address'],
                new \Zend_Db_Expr('shipping.parent_id = main_table.order_id and shipping.address_type = "shipping"'),
                []
            );

      $collection->getSelect()->joinLeft(
                ['billing' => 'sales_order_address'],
                new \Zend_Db_Expr('billing.parent_id = main_table.order_id and billing.address_type = "billing"'),
                []
            );



      $profiles = new \Zend_Db_Expr('(select sp.frequency_length,spo.*, if (b.child_id is not null, b.child_id, sp.sku) as sku from subscriptions_profiles_orders as spo left join subscriptions_profiles as sp on sp.profile_id = spo.profile_id left join sap_sku_breakdown as b on b.parent_id = sp.sku)');


      $collection->getSelect()->joinLeft(
        ['profile' => $profiles],
        'profile.order_id = main_table.order_id and profile.sku = main_table.item_sku',
        []
      );

      // get media code from parent order
      $parentOrderByProfile = new \Zend_Db_Expr('(select profile_id, min(order_id) as parentOrderId from subscriptions_profiles_orders as spo2 group by profile_id )');
      $collection->getSelect()->joinLeft(
        ['parentOrder' => $parentOrderByProfile],
        'profile.profile_id = parentOrder.profile_id',
        []
      );
      $collection->getSelect()->joinLeft(
              ['mod2' => 'microsite_order_detail'],
              'mod2.order_id = parentOrder.parentOrderId',
              []
          );

          $collection
              ->getSelect()
              ->group(new \Zend_Db_Expr('main_table.order_id,i.sku,main_table.item_sku,main_table.item_price,profile.frequency_length'))
              ->order('o.increment_id DESC');

      $collection->getSelect()
            ->reset(\Zend_Db_Select::COLUMNS)
            ->columns([
                'entity_id' => 'o.entity_id',
                'increment_id' => 'o.increment_id',
                'created_at' => 'o.created_at',
                'coupon_code' => 'o.coupon_code',
                'source' => 'o.source',
                'grand_total' => 'o.grand_total',
                'shipping_amount' => 'o.shipping_amount',
                'shipping_tax_amount' => 'o.shipping_tax_amount',
                'customer_email' => 'o.customer_email',
                'ship_canceled_date' => 's.created_at',
                'media_code' => 'mod.media_code',
                'project_code' => 'mod.project_code',
                'parent_media_code' => 'mod2.media_code',
                'parent_project_code' => 'mod2.project_code',
                'psn_value' => 'main_table.item_material_number',
                'qty_ordered' => 'i.qty_ordered',
                // 'qty_shipped' => 'main_table.item_qty',
                'qty_shipped' => new \Zend_Db_Expr('SUM(main_table.item_qty)'),
                'parent_sku' => 'i.sku',
                'child_sku' => 'main_table.item_sku',
                'item_price' => new \Zend_Db_Expr('IF (main_table.item_price is not null, main_table.item_price, 0)'),
                'order_status' => new \Zend_Db_Expr('IF(s.order_id is not null and o.status = "closed","return", o.status)'),
                'item_tax' => 'main_table.item_tax',
                'child_sku' => 'main_table.item_sku',
                'profile_id' => 'profile.profile_id',
                'item_creditmemo_id' => 'scmi.increment_id',
                'sfirstname' => 'shipping.firstname',
                'slastname' => 'shipping.lastname',
                'sregion' => 'shipping.region',
                'spostcode' => 'shipping.postcode',
                'stelephone' => 'shipping.telephone',
                'scountry_id' => 'shipping.country_id',
                'scity' => 'shipping.city',
                'sstreet' => 'shipping.street',
                'bfirstname' => 'billing.firstname',
                'blastname' => 'billing.lastname',
                'bregion' => 'billing.region',
                'bpostcode' => 'billing.postcode',
                'btelephone' => 'billing.telephone',
                'bcountry_id' => 'billing.country_id',
                'bcity' => 'billing.city',
                'bstreet' => 'billing.street',
                'refund' => 'o.total_refunded',
		'prefix' => 'shipping.prefix'
            ]);

            $skuMaxPrice = [];
            // add array of orders with profile_id
            foreach ($collection as $item) {
              if ($item->getProfileId()) {
                $ordersWithProfiles[] = $item->getEntityId();

                //remove profile_id from not subscription item
                    $skuMaxPrice[$item->getEntityId()][$item->getPsnValue()] = $skuMaxPrice[$item->getEntityId()][$item->getPsnValue()] ?? [];
                    if (!in_array($item->getItemPrice(), $skuMaxPrice[$item->getEntityId()][$item->getPsnValue()])) {
                      $skuMaxPrice[$item->getEntityId()][$item->getPsnValue()][] = $item->getItemPrice();
                    }

                }
            }

            $orders = [];
            $data[] = $this->getHeaders();

            foreach ($collection as $item) {

              $skipRow = '';
              $freight = '';
	      $discount = '';
	      $refund = '';
              if (!in_array($item->getEntityId(), $orders)) {
		$discount = abs($this->_getOrderDiscount($item->getEntityId()));
		$refund = $item->getRefund();
                $skipRow = 1;
                $freight = $item->getShippingAmount() + $item->getShippingTaxAmount();
                $orders[] = $item->getEntityId();
              }

              $profileId = $item->getProfileId();
              if (isset($skuMaxPrice[$item->getEntityId()][$item->getPsnValue()]) &&
                count($skuMaxPrice[$item->getEntityId()][$item->getPsnValue()]) > 1 &&
                max($skuMaxPrice[$item->getEntityId()][$item->getPsnValue()]) == $item->getItemPrice()
              ){
                $profileId = '';
              }

              $data[] = [
                $item->getIncrementId(),
                $this->convertDate($item->getCreatedAt()),
                $this->convertDate($item->getShipCanceledDate()),
                $freight,
                $item->getSource(),
                $this->getParenSku($item->getEntityId(), $item->getPsnValue()),
                $item->getGrandTotal(),
                $item->getPsnValue(),
		intval($item->getQtyShipped()),
                intval($item->getQtyShipped()),
                $item->getItemPrice(),
		intval($item->getQtyOrdered()) * floatval($item->getItemPrice()),
                'Y',
                $item->getMediaCode() ?? $item->getParentMediaCode(),
                $item->getProjectCode() ?? $item->getParentProjectCode(),
                0,
                $item->getItemTax(),
                $item->getSfirstname(),
                $item->getSlastname(),
                $item->getSstreet(),
                '',
                $item->getScity(),
                $item->getSregion(),
                $item->getSpostcode(),
                $item->getStelephone(),
                $item->getScountryId(),

                $item->getBfirstname(),
                $item->getBlastname(),
                $item->getBstreet(),
                '',
                $item->getBcity(),
                $item->getBregion(),
                $item->getBpostcode(),
                // $item->getBtelephone(),
                // $item->getBcountryId(),
                $item->getItemCreditmemoId(),
                $item->getOrderStatus(),
                $item->getCustomerEmail(),
                $profileId,
                $skipRow,
		$discount,
		$refund
              ];

            }

            return $this->csv->generate('multifeed_order_return', $data, $storeId);
    }

    public function generateSubsOrders($filterIds, $storeId = null)
    {

      $collection = $this->collectionFactory
        ->create();

      $ids = implode(',', $filterIds);
      $collection->getSelect()
        ->where(new \Zend_Db_Expr("main_table.order_id in ($ids)"));

      // $collection->getSelect()
      //   ->where(new \Zend_Db_Expr('main_table.order_id in (select order_id from subscriptions_profiles_orders)'));
      $collection->getSelect()
        ->where(new \Zend_Db_Expr('main_table.order_id not in (select order_id from sales_creditmemo)'));

      $collection->getSelect()->join(
              ['o' => 'sales_order'],
              new \Zend_Db_Expr('o.entity_id = main_table.order_id and o.status = "complete"'),
              []
          );

      $collection->getSelect()->joinLeft(
              ['s' => 'sales_shipment'],
              's.order_id = main_table.order_id',
              []
          );
      $profiles = new \Zend_Db_Expr('(select sp.frequency_length, spo.*, if (b.child_id is not null, b.child_id, sp.sku) as sku from subscriptions_profiles_orders as spo left join subscriptions_profiles as sp on sp.profile_id = spo.profile_id left join sap_sku_breakdown as b on b.parent_id = sp.sku)');


      $collection->getSelect()->joinLeft(
        ['profile' => $profiles],
        'profile.order_id = main_table.order_id and profile.sku = main_table.item_sku',
        []
      );

      // get media code from parent order
      $parentOrderByProfile = new \Zend_Db_Expr('(select profile_id, min(order_id) as parentOrderId from subscriptions_profiles_orders as spo2 group by profile_id )');
      $collection->getSelect()->joinLeft(
        ['parentOrder' => $parentOrderByProfile],
        'profile.profile_id = parentOrder.profile_id',
        []
      );
      $collection->getSelect()->joinLeft(
              ['mod2' => 'microsite_order_detail'],
              'mod2.order_id = parentOrder.parentOrderId',
              []
          );


      $collection->getSelect()->joinLeft(
              ['i' => 'sales_order_item'],
              'i.item_id = main_table.item_id',
              []
          );

      $collection->getSelect()->joinLeft(
              ['mod' => 'microsite_order_detail'],
              'mod.order_id = o.entity_id',
              []
          );

      $collection->getSelect()->joinLeft(
                ['shipping' => 'sales_order_address'],
                new \Zend_Db_Expr('shipping.parent_id = main_table.order_id and shipping.address_type = "shipping"'),
                []
            );

      $collection->getSelect()->joinLeft(
                ['billing' => 'sales_order_address'],
                new \Zend_Db_Expr('billing.parent_id = main_table.order_id and billing.address_type = "billing"'),
                []
            );

      $collection
          ->getSelect()
          ->group(new \Zend_Db_Expr('main_table.order_id,i.sku,main_table.item_sku,main_table.item_price,profile.frequency_length'))
          ->order('o.increment_id DESC');

      $collection->getSelect()
            ->reset(\Zend_Db_Select::COLUMNS)
            ->columns([
                'entity_id' => 'o.entity_id',
                'increment_id' => 'o.increment_id',
                'created_at' => 'o.created_at',
                'coupon_code' => 'o.coupon_code',
                'source' => 'o.source',
                'grand_total' => 'o.grand_total',
                'shipping_amount' => 'o.shipping_amount',
                'shipping_tax_amount' => 'o.shipping_tax_amount',
                'customer_email' => 'o.customer_email',
                'ship_canceled_date' => 's.created_at',
                'media_code' => 'mod.media_code',
                'project_code' => 'mod.project_code',
                'parent_media_code' => 'mod2.media_code',
                'parent_project_code' => 'mod2.project_code',
                'psn_value' => 'main_table.item_material_number',
                'qty_ordered' => 'i.qty_ordered',
                // 'qty_shipped' => 'main_table.item_qty',
                'qty_shipped' => new \Zend_Db_Expr('SUM(main_table.item_qty)'),
                'parent_sku' => 'i.sku',
                'child_sku' => 'main_table.item_sku',
                'item_price' => new \Zend_Db_Expr('IF (main_table.item_price is not null, main_table.item_price, 0)'),
                'order_status' => new \Zend_Db_Expr('IF(s.order_id is not null and o.status = "closed","return", o.status)'),
                'item_tax' => 'main_table.item_tax',
                'child_sku' => 'main_table.item_sku',
                'profile_id' => 'profile.profile_id',
                // 'item_creditmemo_id' => 'scmi.increment_id',
                'sfirstname' => 'shipping.firstname',
                'slastname' => 'shipping.lastname',
                'sregion' => 'shipping.region',
                'spostcode' => 'shipping.postcode',
                'stelephone' => 'shipping.telephone',
                'scountry_id' => 'shipping.country_id',
                'scity' => 'shipping.city',
                'sstreet' => 'shipping.street',
                'bfirstname' => 'billing.firstname',
                'blastname' => 'billing.lastname',
                'bregion' => 'billing.region',
                'bpostcode' => 'billing.postcode',
                'btelephone' => 'billing.telephone',
                'bcountry_id' => 'billing.country_id',
                'bcity' => 'billing.city',
                'bstreet' => 'billing.street',
                'refund' => 'o.total_refunded',
		'prefix' => 'shipping.prefix'
            ]);

            $orders = [];
            $data[] = $this->getHeaders();

            $ordersWithProfiles = [];
            $skuMaxPrice = [];
            // add array of orders with profile_id
            foreach ($collection as $item) {
              if ($item->getProfileId()) {
                $ordersWithProfiles[] = $item->getEntityId();

                //remove profile_id from not subscription item
                    $skuMaxPrice[$item->getEntityId()][$item->getPsnValue()] = $skuMaxPrice[$item->getEntityId()][$item->getPsnValue()] ?? [];
                    if (!in_array($item->getItemPrice(), $skuMaxPrice[$item->getEntityId()][$item->getPsnValue()])) {
                      $skuMaxPrice[$item->getEntityId()][$item->getPsnValue()][] = $item->getItemPrice();
                    }

                }
            }

            foreach ($collection as $item) {

              if (!in_array($item->getEntityId(), $ordersWithProfiles)) continue;

              $skipRow = '';
              $freight = '';
	      $discount = '';
	      $refund = '';
              if (!in_array($item->getEntityId(), $orders)) {
                $skipRow = 1;
		$discount = abs($this->_getOrderDiscount($item->getEntityId()));
		$refund = $item->getRefund();
                $freight = $item->getShippingAmount() + $item->getShippingTaxAmount();
                $orders[] = $item->getEntityId();
              }

              $profileId = $item->getProfileId();

              if (isset($skuMaxPrice[$item->getEntityId()][$item->getPsnValue()]) &&
                count($skuMaxPrice[$item->getEntityId()][$item->getPsnValue()]) > 1 &&
                max($skuMaxPrice[$item->getEntityId()][$item->getPsnValue()]) == $item->getItemPrice()
              ){
                $profileId = '';
              }

              $data[] = [
                  $item->getIncrementId(),
                  $this->convertDate($item->getCreatedAt()),
                  $this->convertDate($item->getShipCanceledDate()),
                  $freight,
                  $item->getSource(),
                  $this->getParenSku($item->getEntityId(), $item->getPsnValue()),
                  $item->getGrandTotal(),
                  $item->getPsnValue(),
                  intval($item->getQtyShipped()),
                  intval($item->getQtyShipped()),
                  $item->getItemPrice(),
                  intval($item->getQtyOrdered()) * floatval($item->getItemPrice()),
                  'Y',
                  $item->getMediaCode() ?? $item->getParentMediaCode(),
                  $item->getProjectCode() ?? $item->getParentProjectCode(),
                  0,
                  $item->getItemTax(),
                  $item->getSfirstname(),
                  $item->getSlastname(),
                  $item->getSstreet(),
                  '',
                  $item->getScity(),
                  $item->getSregion(),
                  $item->getSpostcode(),
                  $item->getStelephone(),
                  $item->getScountryId(),
                  $item->getBfirstname(),
                  $item->getBlastname(),
                  $item->getBstreet(),
                  '',
                  $item->getBcity(),
                  $item->getBregion(),
                  $item->getBpostcode(),
                // $item->getBtelephone(),
                // $item->getBcountryId(),
                // $item->getItemCreditmemoId(),
                  '',
                  $item->getOrderStatus(),
                  $item->getCustomerEmail(),
                  $profileId,
                  $skipRow,
                  $discount,
                  $refund
              ];
            }
            return $this->csv->generate('multifeed_order_subs', $data, $storeId);
    }
}
