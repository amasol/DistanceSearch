<?php

namespace Hunters\SearchShopMap\Helper;

use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;

class Data extends AbstractHelper
{
    private $myCollectionFactory;

    public function __construct(
        Context $context,
        \Hunters\SearchShopMap\Model\ResourceModel\Collection\SaveZipCodeFactory $myCollectionFactory
    ) {
        $this->myCollectionFactory = $myCollectionFactory;
        parent::__construct($context);
    }

    public function getCompanyIdByCoordinate($lat, $lng)
    {
//        здесь нужно получить мои координаты по тем данным которые я отсылаю

        $model = $this->myCollectionFactory->create();
        $array_json = $model->getColumnValues("coordinate");
        $array = array_map('json_decode', $array_json);


//        $this->myCollectionFactory->create()->getSelect()->addFieldToFilter(/* lat == $lat and lng == $lng*/);

//         $blabla = [
//             "lat" => $lat,
//             "lng" => $lng,
//        ];


//        print_r(array_intersect($blabla, $array[0]));

//         if ($blabla) {
//
//             foreach ($array as $key) {
//                         echo '<pre>';
//                 print_r(array_intersect($blabla, $key));
//                         echo '<pre>';
//             }
//         }

        exit();

//        $arrayTwo = array_diff($array, $blabla);
//        echo '<pre>';
//        echo $arrayTwo;
//        print_r($blabla);
//        echo '</pre>';
//        exit();


//        $companyId = null;





//         you table with coordinate
//        $this->coordinate->create()->getSelect()->addFieldToFilter(/* lat == $lat and lng == $lng*/);


//        return $companyId;
    }





    public function getCompanyData($entityId)
    {

        // когда я говорю что да это мои координаты, то я по ним смотрю свой ентити и возвращаю значение
        #todo return data about company
        $model = $this->myCollectionFactory->create();
        $array = $model->getColumnValues("company_id");


        // Magento company data Magento_Company

//            $this->company->create()->getSelect()->addFieldToFilter(entity_id = company_id );
        return [];
    }
}