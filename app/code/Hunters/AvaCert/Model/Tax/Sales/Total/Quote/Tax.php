<?php

namespace Hunters\AvaCert\Model\Tax\Sales\Total\Quote;

use Magento\Quote\Model\Quote;
use Magento\Quote\Model\Quote\Address;
use Magento\Quote\Api\Data\ShippingAssignmentInterface;


class Tax extends \ClassyLlama\AvaTax\Model\Tax\Sales\Total\Quote\Tax
{

    /**
     * Collect tax totals for quote address
     *
     * @param Quote $quote
     * @param ShippingAssignmentInterface $shippingAssignment
     * @param Address\Total $total
     * @return $this
     * @throws RemoteServiceUnavailableException
     */
    public function collect(
        Quote $quote,
        ShippingAssignmentInterface $shippingAssignment,
        Address\Total $total
    ) {
        $om = \Magento\Framework\App\ObjectManager::getInstance();
        $taxHelper = $om->get('Hunters\AvaCert\Helper\Tax');

        $customerId = $quote->getCustomerId();

        if ($customerId) {
          $isFirstOrder = $taxHelper->isFirstCompanyOrder($customerId);

          $hasCertificate = $taxHelper->hasCustomerCertificate($customerId);

          if ($isFirstOrder && $hasCertificate) {
              return $this;
          }
        }

        return parent::collect($quote, $shippingAssignment, $total);

    }

}
