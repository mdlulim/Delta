<?php

namespace Consnet\Promotions\Model\ResourceModel;

use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

class Promotion extends AbstractDb {

    /**
     * Initialize resource model
     * @return void
     */
    protected function _construct() {
           $this->_init('delta_promotion', 'promo_id');
    }

}
