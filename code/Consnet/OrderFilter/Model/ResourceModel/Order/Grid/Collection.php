<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Consnet\OrderFilter\Model\ResourceModel\Order\Grid;

use Magento\Framework\Data\Collection\Db\FetchStrategyInterface as FetchStrategy;
use Magento\Framework\Data\Collection\EntityFactoryInterface as EntityFactory;
use Magento\Framework\Event\ManagerInterface as EventManager;
use Psr\Log\LoggerInterface as Logger;

/**
 * Order grid collection
 */
class Collection extends \Magento\Framework\View\Element\UiComponent\DataProvider\SearchResult
{
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
        $mainTable = 'sales_order_grid',
        $resourceModel = \Magento\Sales\Model\ResourceModel\Order::class
    ) {
        parent::__construct($entityFactory, $logger, $fetchStrategy, $eventManager, $mainTable, $resourceModel);
    }

    protected function _initSelect()
    {
        $om = \Magento\Framework\App\ObjectManager::getInstance();
        $authSession = $om->get('\Magento\Backend\Model\Auth\Session');
        //$userId = $authSession->getUser()->getUserId();
        $user = $authSession->getUser();
        $userId = $user->getUserId();
        //$roleId = $authSession->getUser()->getRole()->getRoleId();

        if(($user->getRole()->getRoleName() == 'sales_rep')) {
            //$arrCompanyIDs = [];
            $arrCustomerIDs = [];

            $resource = $om->get('Magento\Framework\App\ResourceConnection');
            $connection = $resource->getConnection();
            $companyTableName = $resource->getTableName('company');
            $companyCustomerTableName = $resource->getTableName('company_advanced_customer_entity');
            var_dump($_SESSION);
            if(array_key_exists('company_id', $_SESSION)) {
                //$arrCompanyIDs = array($_SESSION['company_id']);

                $sql = "SELECT customer_id FROM $companyCustomerTableName WHERE company_id = " . $_SESSION['company_id'];

                unset($_SESSION['company_id']);
            } else {
                $sql = "SELECT customer_id FROM $companyCustomerTableName WHERE company_id IN (SELECT entity_id FROM $companyTableName WHERE sales_representative_id = $userId)";
            }

            $result = $connection->fetchAll($sql);

            if($result != null && count($result) > 0) {
                foreach($result as $arrRecord){
                    $arrCustomerIDs[] = $arrRecord['customer_id'];
                }
            }

            parent::_initSelect();
            return $this->addFieldToFilter('main_table.customer_id', array('in'=>$arrCustomerIDs));
        } else {
            parent::_initSelect();
            return $this;
        }
    }
}
