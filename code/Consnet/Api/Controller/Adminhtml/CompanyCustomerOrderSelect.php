<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Consnet\Api\Controller\Adminhtml;


use Magento\Framework\View\Result\PageFactory;

class CompanyCustomerOrderSelect  extends \Magento\Backend\App\Action
{

       /**
     * @var PageFactory
     */
    protected $resultPageFactory;


    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        PageFactory $resultPageFactory
    ) {
        $this->resultPageFactory = $resultPageFactory;
        parent::__construct($context);
       
    }
    /**
     * Index page
     *
     * @return void
     */
    public function execute()
    {
          $company_id =  $this->getRequest()->getParam('id');
          var_dump($company_id);
          die();
        

         //$resultRedirect = $this->resultRedirectFactory->create();
         //$resultRedirect->setPath('customer/account/');
        //return $resultPage;
    }
}
