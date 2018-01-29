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
       $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
       $mageorder = $objectManager->create('\Consnet\Erporder\Helper\MageOrder');

       if(isset($_POST['order_status'])){
        $ordernumber = $_POST['mage_order_number'];
        $order = $objectManager->create('\Magento\Sales\Model\Order')->load($ordernumber);
        $realOrderId = $order->getRealOrderId();
        $ecc_status = $mageorder->get_ecc_order_status($realOrderId);

        if($order->getStatus() !== $ecc_status){
                switch ($ecc_status) {
                    case "pending":
                        $order->setStatus(\Magento\Sales\Model\Order::STATE_PROCESSING);
                        //pending
                        break;                        
                    case "processing":
                        $order->setStatus(\Magento\Sales\Model\Order::STATE_PROCESSING);
                        $ecc_status = 'updated';
                        //processing
                        break;                        
                    case "canceled":
                        $order->setStatus(\Magento\Sales\Model\Order::STATE_CANCELED);
                        $ecc_status = 'updated';
                        //canceled
                        break;
                    case "complete":
                        $order->setStatus(\Magento\Sales\Model\Order::STATE_COMPLETE);
                        $order->setStatus(\Magento\Sales\Model\Order::STATE_CANCELED);
                        $ecc_status = 'updated';
                        //complete
                        break;
                }$order->save();   
            }print_r($ecc_status);
        }       
    } 
}
?>