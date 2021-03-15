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

//    public function coordinate($zip, $entityId = 0)
    public function coordinate($zip)
    {
//       сюда заходим один раз при распечатке zip

        #todo  Эбанат не потрыбно декодити а потым енкодити
        sleep(0.2);
//         return $this->searchZip->total($zip, $entityId);
         return $this->searchZip->total($zip);


//        if ($address != NULL){
//            $result = json_decode($address, true);
////            здесь должен быть такой ответ https://i.imgur.com/iuFAVmJ.png
//            return $result;
//        } else {
//            return NULL;
//        }
    }

    public function validData()
    {
        $allZipArray = $this->companyCollectionFactory->getColumnValues('postcode');
//        удалить ограничение
        $allZipArray = array_slice($allZipArray, 0, 7);
        $resultIncorrectZip = array_map(array($this, 'coordinate'), $allZipArray);
        $resultIncorrectArray = array_filter($resultIncorrectZip, function($element) {
            if (!empty($element)) {
                return $element;
            }
        });
        return array_values($resultIncorrectArray);
    }

//    public function addDataTable($zipArr)
//    {
////        $arr  = array();
////        $count = count($zipArr);
//
//        $company = $this->companyCollectionFactory->getData();
//        $result = array();
//        foreach ($company as $all){
//            $result[] = [
//                'company_name' => mb_convert_case($all['company_name'], 2),
//                'company_email' => mb_strtolower($all['company_email']),
//                'telephone' => $all['telephone'],
//                'city' => mb_convert_case($all['city'], 2),
//                'street' => mb_convert_case($all['street'], 2),
//                'postcode' => $all['postcode']
//            ];
//        }
//    }
}
