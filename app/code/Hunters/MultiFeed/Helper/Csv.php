<?php
namespace Hunters\MultiFeed\Helper;

class Csv extends \Magento\Framework\App\Helper\AbstractHelper
{
    protected $scopeConfig;
    protected $fileFactory;
    protected $filesystem;


    public function __construct(
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Framework\App\Response\Http\FileFactory $fileFactory,
        \Magento\Framework\Filesystem\Driver\File $filesystem
    )
    {
        $this->scopeConfig = $scopeConfig;
        $this->fileFactory = $fileFactory;
        $this->filesystem = $filesystem;
    }

    public function generate($fileName, $data, $storeId = null)
    {
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $csvHelper = $objectManager->get('Magento\Framework\File\Csv');

        $datetime = date('m-d-Y_hia');


        $objectManager =  \Magento\Framework\App\ObjectManager::getInstance();    
        $storeManager = $objectManager->create("\Magento\Store\Model\StoreManagerInterface");
        $stores = $storeManager->getStores(true, true);

        $storeName = 'store';
        foreach ($stores as $key => $value) {
            if ((int)$value->getId() === (int)$storeId) {
                $storeName = $value->getName();
                break;
            }
        }
	
	$storeName = str_replace(" ", "_", $storeName);
        $file = BP . '/var/mf/' . $storeName . '/' . $fileName . '_' . $datetime . '.csv';

        if (!is_dir(dirname($file))) {
            mkdir(dirname($file), 0755, true);
        }

        $delimeter = ',';


        $csvHelper->setDelimiter($delimeter);
        $csvHelper->saveData($file, $data);

        return $file;

    }

    public function getPackFile($filenames)
    {
      if ( empty($filenames) ) return;
      // get zip file
      $filepath = $this->_pack($filenames);

      if(!$filepath || !file_exists($filepath)) {
        throw new \Exception('Cannot create file for feeds');
      }

      return $this->fileFactory->create(
        pathinfo($filepath, PATHINFO_BASENAME),
        file_get_contents($filepath),
        \Magento\Framework\App\Filesystem\DirectoryList::TMP
      );
    }

    protected function _pack($filenames = array()) {
        $zip 		= new \ZipArchive;
        $filename   = sprintf('%s-%s.zip', 'MultiFeeds', date('mdY_His'));
        $filepath 	= BP . '/var/' . $filename;

        if($zip->open($filepath, \ZipArchive::CREATE) === true) {
            foreach($filenames as $_filename) {
                if(!file_exists($_filename)) {
                    throw new \Exception(sprintf('pack -> file %s does not exist', $_filename));
                }

                $zip->addFile($_filename, pathinfo($_filename, PATHINFO_BASENAME));
            }
        } else {
            throw new \Exception(sprintf('pack -> cannot create zip file %s', $filepath));
        }

        $zip->close();

        if(!$this->filesystem->isFile($filepath)) {
            throw new \Exception(sprintf('pack -> zip file %s have not been created', $filepath));
        }

        return $filepath;
    }
}
