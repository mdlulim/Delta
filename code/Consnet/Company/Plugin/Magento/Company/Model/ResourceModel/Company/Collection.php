<?php


namespace Consnet\Company\Plugin\Magento\Company\Model\ResourceModel\Company;

class Collection
{

    public function afterJoinAdvancedCustomerEntityTable($result) {
        $om = \Magento\Framework\App\ObjectManager::getInstance();
        $authSession = $om->get('\Magento\Backend\Model\Auth\Session');
        $userId=$authSession->getUser()->getUserId();
        $roleId= $authSession->getUser()->getRole()->getRoleId();
        $roleName= $authSession->getUser()->getRole()->getRoleName();
        if($roleName == 'sales_rep'){
            return $result->getSelect()->where('sales_representative_id ='".$userId');
        }else{
            return $result;
        }
        
    }
}
