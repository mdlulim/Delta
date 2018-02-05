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

    protected function _initSelect() {
        parent::_initSelect();
        $Collection = $this->getSelect();
        $om = \Magento\Framework\App\ObjectManager::getInstance();
        $authSession = $om->get('\Magento\Backend\Model\Auth\Session');
        $user = $authSession->getUser();
        $userId = $user->getUserId();
        $roleId = $authSession->getUser()->getRole()->getRoleId();
        $roleName = $user->getRole()->getRoleName();

        $resource = $om->get('Magento\Framework\App\ResourceConnection');
        $connection = $resource->getConnection();
        $tableName = $resource->getTableName('company'); 
        $tableName2 = $resource->getTableName('company_advanced_customer_entity'); 
        
        if($roleName == "sales_rep"){
           
            $sql = "Select entity_id FROM " . $tableName." WHERE  sales_representative_id =".$userId;
            $result = $connection->fetchAll($sql); 
        
            $arrayIds = [];
            $customerIds = array(0, 0);
            $id= 0;
            if($result != null){
                if(isset($_SESSION['company_id'])){
                    $arrayIds[0] =  $_SESSION['company_id'];
                }else{
                    foreach($result as $arr){
                        $arrayIds[$id] = $arr['entity_id'];
                        $id++;
                    }
                } 
            
            $sql2 = "Select customer_id FROM " . $tableName2.' WHERE  company_id IN (' . implode(',', $arrayIds) . ')';
            $result2 = $connection->fetchAll($sql2); 
            
            $ids=0;
            foreach($result2 as $arr){
              $customerIds[$ids]=$arr['customer_id'];
              $ids++;
            } 
            }
          
            //unset($_SESSION['company_id']);
            return $Collection->Where('entity_id IN (' . implode(',', $customerIds) . ')');
           
        }if ($roleName == "cic_agent"){
             if(isset($_SESSION['company_id'])){
                $arr[0] =  $_SESSION['company_id'];

                $sql2 = "Select customer_id FROM " . $tableName2.' WHERE  company_id IN (' . implode(',', $arr) . ')';
                $result2 = $connection->fetchAll($sql2); 
                $customerIds = [];
                $ids=0;
                foreach($result2 as $arr){
                    $customerIds[$ids]=$arr['customer_id'];
                    $ids++;
                }
                //var_dump($customerIds);
                //unset($_SESSION['company_id']);
                return $Collection->Where('entity_id IN (' . implode(',', $customerIds) . ')');
            }else{
                return $Collection;
            }
        }else{
             if(isset($_SESSION['company_id'])){
                $arr[0] =  $_SESSION['company_id'];

                $sql2 = "Select customer_id FROM " . $tableName2.' WHERE  company_id IN (' . implode(',', $arr) . ')';
                $result2 = $connection->fetchAll($sql2); 
                $customerIds = [];
                $ids=0;
                foreach($result2 as $arr){
                    $customerIds[$ids]=$arr['customer_id'];
                    $ids++;
                }
                //unset($_SESSION['company_id']);
                return $Collection->Where('entity_id IN (' . implode(',', $customerIds) . ')');
            }else{
                return $Collection;
            }
        }
    }

}
