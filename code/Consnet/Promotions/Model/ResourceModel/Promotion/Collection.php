<?php

namespace Consnet\Promotions\Model\ResourceModel\Promotion;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

class Collection extends AbstractCollection {

    /**
     * @var string
     */
    
     protected $_idFieldName = 'promo_id'; 
    /**
     * Define resource model
     * @return void
     */
    protected function _construct() {
        $this->_init('Consnet\Promotions\Model\Promotion', 'Consnet\Promotions\Model\ResourceModel\Promotion');
    }

}
