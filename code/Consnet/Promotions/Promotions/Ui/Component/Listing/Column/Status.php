<?php
namespace Consnet\Promotions\Ui\Component\Listing\Column;

use \Magento\Sales\Api\OrderRepositoryInterface;
use \Magento\Framework\View\Element\UiComponent\ContextInterface;
use \Magento\Framework\View\Element\UiComponentFactory;
use \Magento\Ui\Component\Listing\Columns\Column;
use \Magento\Framework\Api\SearchCriteriaBuilder;

class Status extends Column
{
    protected $_orderRepository;
    protected $_searchCriteria;

    public function __construct(ContextInterface $context, UiComponentFactory $uiComponentFactory, OrderRepositoryInterface $orderRepository, SearchCriteriaBuilder $criteria, array $components = [], array $data = [])
    {
        $this->_orderRepository = $orderRepository;
        $this->_searchCriteria  = $criteria;
        parent::__construct($context, $uiComponentFactory, $components, $data);
    }

    public function prepareDataSource(array $dataSource)
    {
        if (isset($dataSource['data']['items'])) {

        // $om = \Magento\Framework\App\ObjectManager::getInstance();
        // $resource = $om->get('Magento\Framework\App\ResourceConnection');
        // $connection = $resource->getConnection();
        // $tableName = $resource->getTableName('sales_order');

            foreach ($dataSource['data']['items'] as & $item) {

                $order  = $this->_orderRepository->get($item["entity_id"]);
                $orderNumber = $order->getData("increment_id"); 

                
                // $sql = "Select ECC_ORDER FROM " . $tableName." WHERE  increment_id = '$orderNumber' ";
			    // $result = $connection->fetchAll($sql); 

                // if($result) {
                //      $export_status = $result[0][0] ;
                // }else{
                //     $export_status = "Not in ECC" ;
                // }
 
                $item[$this->getData('name')] = $order->getData("ECC_ORDER");
            }
        }

        return $dataSource;
    }
}



 