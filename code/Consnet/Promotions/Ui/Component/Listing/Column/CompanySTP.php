<?php
// namespace Consnet\Promotions\Ui\Component\Listing\Column;

// use \Magento\Sales\Api\OrderRepositoryInterface;
// use \Magento\Framework\View\Element\UiComponent\ContextInterface;
// use \Magento\Framework\View\Element\UiComponentFactory;
// use \Magento\Ui\Component\Listing\Columns\Column;
// use \Magento\Framework\Api\SearchCriteriaBuilder;

// class CompanySTP extends Column
// {
//     protected $_orderRepository;
//     protected $_searchCriteria;

//     public function __construct(ContextInterface $context, UiComponentFactory $uiComponentFactory, OrderRepositoryInterface $orderRepository, SearchCriteriaBuilder $criteria, array $components = [], array $data = [])
//     {
//         $this->_orderRepository = $orderRepository;
//         $this->_searchCriteria  = $criteria;
//         parent::__construct($context, $uiComponentFactory, $components, $data);
//     }

//     public function prepareDataSource(array $dataSource)
//     {
//         if (isset($dataSource['data']['items'])) {

//         // $om = \Magento\Framework\App\ObjectManager::getInstance();
//         // $resource = $om->get('Magento\Framework\App\ResourceConnection');
//         // $connection = $resource->getConnection();
//         // $tableName = $resource->getTableName('sales_order');

//             foreach ($dataSource['data']['items'] as & $item) {

//                 $order  = $this->_orderRepository->get($item["entity_id"]);
//                 $orderNumber = $order->getData("increment_id"); 
 
//                 $item[$this->getData('name')] = $order->getData("ECC_ORDER");
//             }
//         }

//         return $dataSource;
//     }
// }




///////// ---  /////

<?php
 
namespace Consnet\Promotions\Ui\Component\Listing\Column;

use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Framework\View\Element\UiComponent\ContextInterface;

class CompanySTP extends \Magento\Ui\Component\Listing\Columns\Column
{
    
    protected $company_stp_id;

    /**
     * @param ContextInterface $context
     * @param UiComponentFactory $uiComponentFactory
     * @param \Magento\Directory\Model\Country $country
     * @param array $components
     * @param array $data
     */
    public function __construct(
        ContextInterface $context,
        UiComponentFactory $uiComponentFactory,
        \Magento\Directory\Model\Country $country,
        array $components = [],
        array $data = []
    ) {
        parent::__construct($context, $uiComponentFactory, $components, $data);
        $this->country = $country;
    }

    /**
     * Prepare Data Source
     *
     * @param array $dataSource
     * @return array
     */
    public function prepareDataSource(array $dataSource)
    {
        if (isset($dataSource['data']['items'])) {
            $fieldName = $this->getData('STP_ID');
            foreach ($dataSource['data']['items'] as & $item) {
                if (isset($item[$fieldName])) {
                    $country = $this->country->loadByCode($item[$fieldName]);
                    $item[$fieldName] = $country->getName();
                }
            }
        }

        return $dataSource;
    }
}




 