<?php

namespace Consnet\Promotions\Model\ResourceModel;

use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

class Demo extends AbstractDb {

    /**
     * Initialize resource model
     * @return void
     */
    protected function _construct() {
        $this->_init('Consnet_demo', 'demo_id');
      //  $this->_init('delta_promotion', 'promo_id');
    }

}
