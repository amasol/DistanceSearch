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

    public function coordinate($coordinate)
    {
        sleep(0.2);
        $address = $this->searchZip->total($coordinate);
        return json_decode($address, true);
    }


    public function validData()
    {
        $company = $this->companyCollectionFactory->getData();

//        удалить ограничение
        $company = array_slice($company, 0, 7);
        $result = array();
        foreach ($company as $all){
            $result[] = [
                'company_id' =>$all['entity_id'] ,
                'company_name' => mb_convert_case($all['company_name'], 2),
                'company_email' => mb_strtolower($all['company_email']),
                'telephone' => $all['telephone'],
                'city' => mb_convert_case($all['city'], 2),
                'street' => mb_convert_case($all['street'], 2),
                'postcode' => $all['postcode']
            ];
        }

        $resultIncorrectZip = array_map(array($this, 'coordinate'), $result);

        $resultIncorrectArray = array_filter($resultIncorrectZip, function($element) {
            if ($element != NULL) {
                return $element;
            }
        });
        // возвращение отсортерованного и проиндексированного массива
        return array_values($resultIncorrectArray);
    }
}