<?php
namespace Hunters\SearchShopMap\Helper;

class HelpPatch
{
    /**
     * @var \Hunters\SearchShopMap\Service\SearchZip
     */
    public $searchZip;

    /**
     * @var \Magento\Company\Model\ResourceModel\Company\Collection
     */
    public $companyCollectionFactory;

    /**
     * HelpPatch constructor
     * @param \Hunters\SearchShopMap\Service\SearchZip $searchZip
     * @param \Magento\Company\Model\ResourceModel\Company\Collection $companyCollectionFactory
     */
    public function __construct(
        \Hunters\SearchShopMap\Service\SearchZip $searchZip,
        \Magento\Company\Model\ResourceModel\Company\Collection $companyCollectionFactory
    ) {
        $this->searchZip = $searchZip;
        $this->companyCollectionFactory = $companyCollectionFactory;
    }

    public function coordinate($company)
    {
        sleep(0.2);
        $address = $this->searchZip->total($company);
        if ($address != NULL){
            $result = json_decode($address, true);
            return $result;
        }
        else {
            return NULL;
        }
    }

    public function validData()
    {
        $company = $this->companyCollectionFactory->getData();
        $result = array();
        foreach ($company as $all){
            $result[] = [
                'company_name' => mb_convert_case($all['company_name'], 2),
                'company_email' => mb_strtolower($all['company_email']),
                'telephone' => $all['telephone'],
                'city' => mb_convert_case($all['city'], 2),
                'street' => mb_convert_case($all['street'], 2),
                'postcode' => $all['postcode']
            ];
        }
//        удалить ограничение!!!!!!
        $result = array_slice($result, 0, 9);


        $resultIncorrectZip = array_map(array($this, 'coordinate'), $result);
        $resultIncorrectArray = array_filter($resultIncorrectZip, function($element) {
            if ($element != NULL) {
                return $element;
            }
        });
        $result = array_values($resultIncorrectArray);
        return $result;
    }


    public function addDataTable($zipArr)
    {
        $arr  = array();
        $count = count($zipArr);

        for ($i = 0; $i < $count; $i++) {
            $arr[$i]['postcode'] = $zipArr[$i]['postcode'];
            $arr[$i]['state'] = $zipArr[$i]['state'];
            $arr[$i]['coordinate'] = $zipArr[$i]['coordinate'];
        }
        return $arr;
    }
}