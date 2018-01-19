<?php

namespace Consnet\Customer\Controller\Accounts\Index;

use Magento\Framework\App\Action\Context;

class Accounts extends \Magento\Framework\App\Action\Action {


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

  $_SESSION['modal_on'] = false;
  $resultPage = $this->_resultPageFactory->create();
  return $resultPage;
}



}
