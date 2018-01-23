<?php


namespace Consnet\Company\Plugin\Magento\Company\Model\ResourceModel\Company;

class Collection
{

    public function afterJoinAdvancedCustomerEntityTable($result) {
       return $result;
    }
}
