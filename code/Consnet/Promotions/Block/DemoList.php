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

    // /**
    //  * Get Demo Items Collection
    //  * @return DemoCollection
    //  */
    // public function getDemoItems() {
    //     if (null === $this->_demoCollection) {
    //         $this->_demoCollection = $this->_demoColFactory->create();
    //     }
    //     return $this->_demoCollection;
    // }


    
    public function getPromoItems() {
        if (null === $this->_demoCollection) {
          //   $this->_demoCollection = $this->_demoColFactory->create();
          //  ->addAttributeToFilter('kunnr',['eq'=>100000000]);
         
          $om = \Magento\Framework\App\ObjectManager::getInstance();
          $customerSession = $om->get('Magento\Customer\Model\Session');
          $adminsessionquote = $om->get('Magento\Backend\Model\Session\Quote');

       //    $customer_model = $om->create('Magento\Customer\Model\Customer'); 
       //    $customer = $customer_model->load($customerSession->getId());
       //    $customerData = $customer->getData();

      //  var_dump($adminsessionquote->getCustomerId()) ;

        $custID = '' ;  
            $companymanagement = $om->create('Magento\Company\Model\CompanyManagement');
            if (($adminsessionquote->getQuote()->getCustomerId()) != NULL) {
                   $stp_id = $companymanagement->getByCustomerId($adminsessionquote->getCustomerId())->getData("STP_ID");
                   //echo 'Customer STP: '. $stp_id.". Quote ID: ".$adminsessionquote->getQuote()->getId() ; //print $custID ;
                   echo 'Customer STP: '.$companymanagement->getByCustomerId($adminsessionquote->getQuote()->getCustomerId())->getData("STP_ID");
                   //var_dump(get_class_methods($adminsessionquote));
                    $custID = $stp_id ;
            }
            else {
                //$stp_id = $companymanagement->getByCustomerId($adminsessionquote->getCustomerId())->getData("STP_ID");
                   //echo 'Customer STP: '. $stp_id.". Quote ID: ".$adminsessionquote->getQuote()->getId() ; //print $custID ;
                   //echo 'Thato STP: '.$companymanagement->getByCustomerId($adminsessionquote->getQuote()->getCustomerId())->getData("STP_ID");
                   //var_dump(get_class_methods($adminsessionquote));
                   // $custID = $stp_id ;
            }
            //echo "Madihlaba";
            $om = \Magento\Framework\App\ObjectManager::getInstance();
            $adminsession = $om->get(\Magento\Backend\Model\Session\Quote::class);
            $customer_id = $adminsession->getCustomerId();
            $customer_model = $om->create('Magento\Customer\Model\Customer');
            $customer_model->setWebsiteId(1);
            $customer = $customer_model->load($customer_id);
            $group_id = $customer->getData("group_id");
         //   echo "ID: ".$customer_id;
            $companymanagement_model = $om->create('Magento\Company\Model\CompanyManagement');
            /*if($customer_id == null){
                echo 'Thato STP: '.$companymanagement_model->getByCustomerId(226)->getData("STP_ID");
            }else{
                echo 'Thato STP: '.$companymanagement_model->getByCustomerId($customer_id)->getData("STP_ID");
            }*/
            
       
       //-     $soldtoparty_number = $stp_id;//$customerData['stp_id'];
          
    //  $custID = '61900' ; // '' ;
    //    if(isset($_SESSION['kunnr'])) {
    //        $custID =    $_SESSION['kunnr'] ;
    //    }else {
    //         $custID =     $customerSession->getid() ;   
    //    }   
       
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
