<?php

namespace Consnet\Promotions\Block;

use Magento\Framework\View\Element\Template;
use Consnet\Promotions\Model\ResourceModel\Demo\Collection as DemoCollection;
use Magento\Store\Model\ScopeInterface;

class PromoList extends Template {

  
    protected $_demoCollection;

    
    protected $_demoColFactory;

    
    public function __construct(
    Template\Context $context, \Consnet\Promotions\Model\ResourceModel\Promotion\CollectionFactory $collectionFactory, array $data = []
    ) {
        $this->_demoColFactory = $collectionFactory;
        parent::__construct(
                $context, $data
        );
    }

    /**
     * Get Demo Items Collection
     * @return DemoCollection
     */
    public function getPromoItems() {
        if (null === $this->_demoCollection) {
          //   $this->_demoCollection = $this->_demoColFactory->create();
          //  ->addAttributeToFilter('kunnr',['eq'=>100000000]);
         
          $om = \Magento\Framework\App\ObjectManager::getInstance();
          $customerSession = $om->get('Magento\Customer\Model\Session');

       //    $customer_model = $om->create('Magento\Customer\Model\Customer'); 
       //    $customer = $customer_model->load($customerSession->getId());
       //    $customerData = $customer->getData();

         //   $companymanagement = $om->create('Magento\Company\Model\CompanyManagement');
        //    $stp_id = $companymanagement->getByCustomerId($customerSession->getId())->getData("STP_ID");
       //     $soldtoparty_number = $stp_id;//$customerData['stp_id'];
          
          $myJob =  new \Consnet\Promotions\Controller\Index\CronJobPromotion() ;
        $this->_demoCollection =  $this->_demoCollection =  $myJob->getDataFromErp($_SESSION['kunnr']) ;
        }
        return $this->_demoCollection;
    }
    
}
