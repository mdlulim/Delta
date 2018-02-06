<?php

namespace Consnet\Promotions\Model;

use Magento\Framework\Model\AbstractModel;

class Promotion extends AbstractModel {

    /**
     * Initialize resource model
     * @return void
     */
    protected function _construct() {
        $this->_init('Consnet\Promotions\Model\ResourceModel\Promotion');
    }

}
