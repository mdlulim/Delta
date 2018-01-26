<?php

namespace Consnet\ManageCustomer\Model;
 
use Magento\Framework\Model\AbstractModel;
 
class Customer extends AbstractModel
{
    /**
     * Define resource model
     */
    protected function _construct()
    {
        $this->_init('Consnet\ManageCustomer\Model\ResourceModel\Customer');
                 
    }

}
