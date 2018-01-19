<?php

namespace Consnet\Api\Controller\Account;

use Magento\Framework\App\Action\Context;

class SwitchAccount extends \Magento\Framework\App\Action\Action {


protected $_resultPageFactory;
  /**
   * @param \Magento\Company\Api\Data\CompanyInterfaceFactory $companyFactory
   */
  public function __construct(
  Context $context,
  \Magento\Framework\View\Result\PageFactory $resultPageFactory
  ){

   $this->_resultPageFactory = $resultPageFactory;
    parent::__construct($context);
}

public function execute() {

  $_SESSION['modal_on'] = true;
  $resultPage = $this->_resultPageFactory->create();
  $this->$resultPage->setRedirect('customer/account/index');
  return $resultPage;
}



}
