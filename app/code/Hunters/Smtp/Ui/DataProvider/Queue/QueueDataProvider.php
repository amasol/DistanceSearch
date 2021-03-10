<?php
namespace Hunters\Smtp\Ui\DataProvider\Queue;

use Magento\Ui\DataProvider\AbstractDataProvider;
use Hunters\Smtp\Model\ResourceModel\Queue\Collection;

/**
 * Class ProductDataProvider
 */
class QueueDataProvider extends AbstractDataProvider
{
    protected $statuses = [
        '1' => 'Not Sent',
        '2' => 'Sent',
        '-1' => 'Failed'
    ];
    /**
     * foobar collection
     *
     * @var \[Namespace]\[Module]\Model\FooBar\Collection
     */
    protected $collection;

    /**
     * @var \Magento\Ui\DataProvider\AddFieldToCollectionInterface[]
     */
    protected $addFieldStrategies;

    /**
     * @var \Magento\Ui\DataProvider\AddFilterToCollectionInterface[]
     */
    protected $addFilterStrategies;

    /**
     * Construct
     *
     * @param string $name
     * @param string $primaryFieldName
     * @param string $requestFieldName
     * @param CollectionFactory $collectionFactory
     * @param array $meta
     * @param array $data
     */
    public function __construct(
        $name,
        $primaryFieldName,
        $requestFieldName,
        Collection $collection,
        array $meta = [],
        array $data = []
    ) {
        parent::__construct($name, $primaryFieldName, $requestFieldName, $meta, $data);
        $this->collection = $collection;
    }

    /**
     * Get collection
     *

    /**
     * Get data
     *
     * @return array
     */
    public function getData()
    {
        if (!$this->getCollection()->isLoaded()) {
            $this->getCollection()->getSelect()
            ->joinLeft(
                        ['queue_desc2' => 'mail_queue_desc'],
                        'main_table.mail_desc_id = queue_desc2.mail_desc_id'
                );
            $this->getCollection()->load();
        }

        $data = $this->getCollection()->toArray();

        if (isset($data['items']) && is_array($data['items'])) {
            foreach($data['items'] as &$item) {
                $item['status'] = $this->getStatusById(
                    $item['status']
                );
            }
        }

        return $data;
    }

    public function getStatusById($id){
        return $this->statuses[(string)$id] ?? '';
    }
}
