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
        $model = $this->myCollectionFactory->create();

        $blabla = [
            "lat" => $lat,
            "lng" => $lng,
        ];

//        $dataArray  = $model->addFieldToFilter("coordinate", ['like' => json_encode($blabla)])->toArray();
        $collection  = $model->addFieldToFilter("coordinate", ['eq' => json_encode($blabla)]);

        foreach ($collection as $data) {
            file_put_contents(BP. '/var/log/data.log', print_r($data->getData(), true) . "\n", FILE_APPEND | LOCK_EX);
        }
        die();
//        file_put_contents(BP. '/var/log/data.log', print_r($dataArray, true)."\n", FILE_APPEND | LOCK_EX);

//        здесь нужно получить мои координаты по тем данным которые я отсылаю
//        $model = $this->myCollectionFactory->create();
////        $allMyTables = $model->getColumnValues("coordinate");
//        $allMyTables = $model->getData();
//        $newArrayLatLng = [
//             "lat" => $lat,
//             "lng" => $lng,
//        ];
//
//        if ($newArrayLatLng && $allMyTables) {
//            foreach ($allMyTables as $key) {
//            $arrayNoTrue = array_map('json_decode', $key);
//            $goodArray = json_decode(json_encode($arrayNoTrue['coordinate']), TRUE);
//                 echo '<pre>';
//                    print_r($goodArray);
//                 echo '</pre>';
//                 exit();
//            }
//        }
//        exit();

//        coordinate =  [
//            lat
//            lng
//            ];

//        $companyId = null;

//        $this->myCollectionFactory->create()->getSelect()->addFieldToFilter(/* lat == $lat and lng == $lng*/);
//        you table with coordinate
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
