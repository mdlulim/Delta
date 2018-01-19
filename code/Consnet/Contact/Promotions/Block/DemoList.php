<?php

namespace Consnet\Promotions\Block;

use Magento\Framework\View\Element\Template;
use Consnet\Promotions\Model\ResourceModel\Demo\Collection as
DemoCollection;
use Magento\Store\Model\ScopeInterface;

class DemoList extends Template {

    /**
     * Demo collection
     *
     * @var DemoCollection
     */
    protected $_demoCollection;

    /**
     * Demo resource model
     *
     * @var \Consnet\Promotions\Model\ResourceModel\Demo\CollectionFactory
     */
    protected $_demoColFactory;
    protected $_CustomerSession;

    /**
     * @param Template\Context $context
     * @param \Consnet\Promotions\Model\ResourceModel\Demo\CollectionFactory $collectionFactory
     * @param array $data
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     */
    public function __construct(
    Template\Context $context, \Consnet\Promotions\Model\ResourceModel\Demo\CollectionFactory $collectionFactory, array $data = []
    // \Magento\Customer\Model\Session $CustomerSession
    ) {
        $this->_demoColFactory = $collectionFactory;
        //  $this->_CustomerSession = $CustomerSession;

        parent::__construct(
                $context, $data
        );
    }

    /**
     * Get Demo Items Collection
     * @return DemoCollection
     */
    public function getDemoItems() {
        if (null === $this->_demoCollection) {
            $this->_demoCollection = $this->_demoColFactory->create();
        }
        return $this->_demoCollection;
    }


    
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
          
       $custID = '' ;
       if(isset($_SESSION['kunnr'])) {
           $custID =    $_SESSION['kunnr'] ;
       }else {
            $custID =     $customerSession->getName() ;   
       }   
        var_dump($customerSession->getCreated_at()) ; //print $custID ;
        $myJob =  new \Consnet\Promotions\Controller\Index\CronJobPromotion() ;
        $this->_demoCollection =  $this->_demoCollection =  $myJob->getDataFromErp($custID) ;
        }
        return $this->_demoCollection;
    }
    
    public function getPromotionList()
    {   
         if (null === $this->_demoCollection) {
             $om = \Magento\Framework\App\ObjectManager::getInstance();
             $customerSession = $om->get('Magento\Customer\Model\Session');
             $myJob =  new \Consnet\Promotions\Controller\Index\CronJobPromotion() ;
             $this->_demoCollection =  $this->_demoCollection =  $myJob->getDataFromErp($_SESSION['kunnr']) ; //61900
        }
        
        $promoList = array();
        $count=0;
        foreach ($this->_demoCollection as $item): 
           if($count == 0){
               $promoList[$count] =  $item->getTitle();
               $count++;
           }else{
                $flag = true;
               for($i=0; $i < $count; $i++){
                    if($promoList[$i] == $item->getTitle()){
                        $flag = false;
                        break;
                    }
               }
                $promoList[$count] =  $item->getTitle();
                $count++;

           }

          endforeach; 
        return $promoList;  
    }
    public function getSku($promotionName)
    {   
         if (null === $this->_demoCollection) {
             $om = \Magento\Framework\App\ObjectManager::getInstance();
             $customerSession = $om->get('Magento\Customer\Model\Session');
             $myJob =  new \Consnet\Promotions\Controller\Index\CronJobPromotion() ;
             $this->_demoCollection =  $this->_demoCollection =  $myJob->getDataFromErp($_SESSION['kunnr']) ;
        }
        
        $skuList = array();
        $count=0;
        foreach ($this->_demoCollection as $item): 
           if($item->getTitle() == $promotionName){
              $skuList[$count] = $item->getMatnr_product();
              $count++;
           }

          endforeach; 
        return $skuList;  
    }


}
