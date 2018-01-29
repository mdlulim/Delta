<?php 
namespace Consnet\Erporder\Controller\Adminhtml\Order; 

class Index extends \Magento\Backend\App\Action 
{ 
    public function __construct( \Magento\Backend\App\Action\Context $context, 
    \Magento\Framework\View\Result\PageFactory $resultPageFactory ) { 
        parent::__construct($context); 
        $this->resultPageFactory = $resultPageFactory; 
    } 
    
    public function execute() { 
       die();
       // print_r('died');
       // die("hello Webkul Employee");

       
    } 
}
?>