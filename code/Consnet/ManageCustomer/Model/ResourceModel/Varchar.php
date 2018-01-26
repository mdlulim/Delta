<?php
namespace Consnet\ManageCustomer\Model\ResourceModel;

use \Magento\Framework\Model\ResourceModel\Db\AbstractDb;
 
class Varcher extends AbstractDb
{
    /**
     * Define main table
     */
    protected function _construct()
    {
        $this->_init('customer_entity_varchar', 'value_id');
    }
}