<?php

namespace Consnet\Promotions\Model\ResourceModel\Demo;

use Magento\Framework\Model\ResourceModel\Db\Collection\
AbstractCollection;

class Collection extends AbstractCollection {

    /**
     * @var string
     */
    protected $_idFieldName = 'demo_id'; 
    //  protected $_idFieldName = 'promo_id'; 
    /**
     * Define resource model
     * @return void
     */
    protected function _construct() {
        $this->_init('Consnet\Promotions\Model\Demo', 'Consnet\Promotions\Model\ResourceModel\Demo');
    }

}
