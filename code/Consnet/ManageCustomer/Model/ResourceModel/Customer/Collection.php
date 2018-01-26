<?php

namespace Consnet\ManageCustomer\Model\ResourceModel\Customer;
 
use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

class Collection extends AbstractCollection
{
    
    protected $_idFieldName = 'id';
    public function __construct(
        \Magento\Framework\Data\Collection\EntityFactoryInterface $entityFactory,
        \Psr\Log\LoggerInterface $logger,
        \Magento\Framework\Data\Collection\Db\FetchStrategyInterface $fetchStrategy,
        \Magento\Framework\Event\ManagerInterface $eventManager,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\DB\Adapter\AdapterInterface $connection = null,
        \Magento\Framework\Model\ResourceModel\Db\AbstractDb $resource = null
    ) {
        $this->_init(
            'Consnet\ManageCustomer\Model\Customer',
            'Consnet\ManageCustomer\Model\ResourceModel\Customer'
        );
        parent::__construct(
            $entityFactory, $logger, $fetchStrategy, $eventManager, $connection,
            $resource
        );
        $this->storeManager = $storeManager;
    }
    protected function _initSelect()
    {
        $user_id="123456789";
        parent::_initSelect();

        $this->getSelect()->joinLeft(
                ['secondTable' => $this->getTable('customer_entity_varchar')],
                'secondTable.value = "123456789"'
            );
    }

}
