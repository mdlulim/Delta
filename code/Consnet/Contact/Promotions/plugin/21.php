<?php

namespace Consnet\Promotions\Controller\Index;

use Magento\Framework\App\Action\Action;

class Index extends Action {

    /**
     * @var \Magento\Framework\View\Result\PageFactory
     */
    protected $resultPageFactory;

    /**
     * @param \Magento\Framework\App\Action\Context $context
     * @param \Magento\Framework\View\Result\PageFactory
      resultPageFactory
     */
    public function __construct(
    \Magento\Framework\App\Action\Context $context, \Magento\Framework\View\Result\PageFactory
    $resultPageFactory
    ) {
        $this->resultPageFactory = $resultPageFactory;
        parent::__construct($context);
    }

    /**
     * Renders Promotions Index
     */
    public function execute() {
//        $model = $this->_objectManager->create('Consnet\Promotions\Model\Promotion');
//        //       $model->load(1);
//        $model->setTitle('Update London'); 
//        $model->setTitle('knuma_promo'); 
//        $model->save();
        
   //   $myJob =  new \Consnet\Promotions\Controller\Index\CronJobPromotion() ;
  //    $myJob->getDataFromErp() ;
       return $this->resultPageFactory->create();
    }

}
