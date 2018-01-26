<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Consnet\CustomerFilter\Model\ResourceModel\Grid;

use Magento\Customer\Ui\Component\DataProvider\Document;
use Magento\Framework\Data\Collection\Db\FetchStrategyInterface as FetchStrategy;
use Magento\Framework\Data\Collection\EntityFactoryInterface as EntityFactory;
use Magento\Framework\Event\ManagerInterface as EventManager;
use Psr\Log\LoggerInterface as Logger;

class Collection extends \Magento\Framework\View\Element\UiComponent\DataProvider\SearchResult
{
    /**
     * @inheritdoc
     */
    protected $document = Document::class;

    /**
     * @inheritdoc
     */
    protected $_map = ['fields' => ['entity_id' => 'main_table.entity_id']];

    /**
     * Initialize dependencies.
     *
     * @param EntityFactory $entityFactory
     * @param Logger $logger
     * @param FetchStrategy $fetchStrategy
     * @param EventManager $eventManager
     * @param string $mainTable
     * @param string $resourceModel
     */
    public function __construct(
        EntityFactory $entityFactory,
        Logger $logger,
        FetchStrategy $fetchStrategy,
        EventManager $eventManager,
        $mainTable = 'customer_grid_flat',
        $resourceModel = \Magento\Customer\Model\ResourceModel\Customer::class
    ) {
        
        parent::__construct($entityFactory, $logger, $fetchStrategy, $eventManager, $mainTable, $resourceModel);

    }

    protected function _initSelect()
    {
       parent::_initSelect();
        $om = \Magento\Framework\App\ObjectManager::getInstance();
        $authSession = $om->get('\Magento\Backend\Model\Auth\Session');
        $user = $authSession->getUser();
        $userId = $user->getUserId();
        $roleId = $authSession->getUser()->getRole()->getRoleId();
       
        $resource = $om->get('Magento\Framework\App\ResourceConnection');
        $connection = $resource->getConnection();
        $tableName = $resource->getTableName('company'); 
        $tableName2 = $resource->getTableName('company_advanced_customer_entity'); 
        
        if(($user->getRole()->getRoleName() == 'sales_rep')) {
            $tableName = $resource->getTableName('company'); 
            $tableName2 = $resource->getTableName('company_advanced_customer_entity'); 
            $sql = "Select entity_id FROM " . $tableName." WHERE  sales_representative_id =".$userId;
            $result = $connection->fetchAll($sql); 
        
            $arrayIds = [];
            if($result != null){
                $id=0;  
                foreach($result as $arr){
                    $arrayIds[$id] = $arr['entity_id'];
                    $id++;
                }
                $sql2 = "Select customer_id FROM " . $tableName2.' WHERE  company_id IN (' . implode(',', $arrayIds) . ')';
                $result2 = $connection->fetchAll($sql2); 
                $customerIds = [];
                $ids=0;
                foreach($result2 as $arr){
                $customerIds[$ids]=$arr['customer_id'];
                $ids++;
                }
               $arrayIds = $customerIds; 
            }
            $cusId = $arrayIds;
            $cusIds = array();
            $i = 0;
            foreach($cusId as $d){
                $cusIds[$i]=intval($d);
                $i++;
            }
            return $this->addFieldToFilter('main_table.entity_id', array('in'=>$cusIds));
        }else{
         return $this;
        } 
       
       
    }

}
