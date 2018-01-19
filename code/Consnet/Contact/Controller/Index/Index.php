<?php
/**
 *
 * Copyright © 2013-2017 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Consnet\Contact\Controller\Index;

class Index extends \Magento\Contact\Controller\Index
{
    /**
     * Show Contact Us page
     *
     * @return void
     */
    public function execute()
    {
    	   $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
			$customerSession = $objectManager->get('Magento\Customer\Model\Session');
			if($customerSession->isLoggedIn()) {
  			
  		    
         
        $this->_view->loadLayout();
        $this->_view->renderLayout();
       
       
        
     }else{
     	$resultRedirect = $this->resultRedirectFactory->create();
                $resultRedirect->setPath('customer/account/login');
                return $resultRedirect;
    }
    }
}
