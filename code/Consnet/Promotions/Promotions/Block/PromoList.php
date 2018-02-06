<?php

namespace Consnet\Promotions\Block;

use Magento\Framework\View\Element\Template;
use Consnet\Promotions\Model\ResourceModel\Demo\Collection as DemoCollection;
use Magento\Store\Model\ScopeInterface;
use Magento\Catalog\Block\Product\ListProduct;

class PromoList extends Template {

    protected $_demoCollection;
    protected $_demoColFactory;
    protected $_productCollectionFactory;
    protected $listProductBlock;

      protected $_is_single_obj;
    
    public function __construct(
    \Magento\Backend\Block\Template\Context $context, 
    \Consnet\Promotions\Model\ResourceModel\Promotion\CollectionFactory $collectionFactory, 
    \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productCollectionFactory,
     \Magento\Catalog\Block\Product\ListProduct $listProductBlock,
    array $data = []
    ) {
        $this->_productCollectionFactory = $productCollectionFactory;    
        $this->_demoColFactory = $collectionFactory;
        $this->listProductBlock = $listProductBlock;
        parent::__construct(
                $context, $data
        );

      //  $this->$_is_single_obj = false ;
    }

    /**
     * Get Demo Items Collection
     * @return DemoCollection
     */
    public function getMode()
    {
        return $this->getChildBlock('toolbar')->getCurrentMode();
    } 

    public function getAdditionalHtml()
    {
        return $this->getChildHtml('additional');
    }

    public function getToolbarHtml()
    {
        return $this->getChildHtml('toolbar');
    }

    public function getPromoItems() {
        if (null === $this->_demoCollection) {
           
          $om = \Magento\Framework\App\ObjectManager::getInstance();
          $customerSession = $om->get('Magento\Customer\Model\Session');

        
        $myJob =  new \Consnet\Promotions\Controller\Index\CronJobPromotion() ;
        $this->_demoCollection =  $this->_demoCollection =  $myJob->getDataFromErp($_SESSION['kunnr']) ;
        }
        return $this->_demoCollection;
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

       if(is_object($this->_demoCollection )){

          
           if($this->_demoCollection->getTitle() == $promotionName){
              $skuList[$count] = $this->_demoCollection->getMatnr_product(); 
           }
 
           return $skuList; 
         }



        foreach ($this->_demoCollection as $item): 
           if($item->getTitle() == $promotionName){
              $skuList[$count] = $item->getMatnr_product();
              $count++;
           }

          endforeach; 
        return $skuList;  
    }
    public function getPromotionList()
    {          

       //  if (null === $this->_demoCollection) {
             $om = \Magento\Framework\App\ObjectManager::getInstance();
             $customerSession = $om->get('Magento\Customer\Model\Session');
             $myJob =  new \Consnet\Promotions\Controller\Index\CronJobPromotion() ;
             $this->_demoCollection =  $this->_demoCollection =  $myJob->getDataFromErp($_SESSION['kunnr']) ; //61900
      //  }
       //  echo  $this->_demoCollection->getTitle()  ;

         if(is_object($this->_demoCollection )){
           return $this->_demoCollection ;
         }

        $promoList = array();
        
        $count=0;
        foreach ($this->_demoCollection as $item):
           if($count == 0){
                    $promoList[$count] =  $item->getTitle();
                    $count++;
               $this->_is_single_obj = true ;
           }else{
               $this->_is_single_obj = false ;
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
    public function getIs_single_obj(){
          $list = $this->getPromotionList() ;
        //   var_dump ($this->_is_single_obj ) ;
         return $this->_is_single_obj  ;
    } 

    public function getPromotionLists()
    {   
         if (null === $this->_demoCollection) {
             $om = \Magento\Framework\App\ObjectManager::getInstance();
             $customerSession = $om->get('Magento\Customer\Model\Session');
             $myJob =  new \Consnet\Promotions\Controller\Index\CronJobPromotion() ;
             $this->_demoCollection =  $this->_demoCollection =  $myJob->getDataFromErp($_SESSION['kunnr']) ; //61900
        }
           
          
         //  var_dump (sizeof ($this->_demoCollection) ) ;
        return $this->_demoCollection  ;  
    }

    public function getAddToCartPostParams($product)
    {
        return $this->listProductBlock->getAddToCartPostParams($product);
    }
    
}
