<?php

namespace Consnet\Promotions\Model;

use Magento\Framework\Model\AbstractModel;

class Demo extends AbstractModel {

    /**
     * Initialize resource model
     * @return void
     */
    protected function _construct() {
        $this->_init('Consnet\Promotions\Model\ResourceModel\Demo');
    }

}
