<?php
namespace Consnet\Erporder\Controller\Index;

class Index extends \Magento\Framework\App\Action\Action
{
    protected $mageorder;
    protected $orderitems;

    protected $resultPageFactory;
    protected $jsonHelper;

    /**
     * Constructor
     *
     * @param \Magento\Framework\App\Action\Context  $context
     * @param \Magento\Framework\Json\Helper\Data $jsonHelper
     */
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        \Magento\Framework\Json\Helper\Data $jsonHelper
    ) {
        //$this->$mageorder = $mageorder;
        $this->resultPageFactory = $resultPageFactory;
        $this->jsonHelper = $jsonHelper;
        parent::__construct($context);
    }

    /**
     * Execute view action
     */
    public function execute()
    {
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $mageorder = $objectManager->create('\Consnet\Erporder\Helper\MageOrder');
        
        if (isset($_POST['orderid'])) {
            
            if(isset($_POST['sku1'])){
                $delivery_date = null;
                if(isset($_POST['changed_deliverydate'])){
                    $delivery_date = $_POST['changed_deliverydate'];
                    unset($_POST['changed_deliverydate']);
                }
                
                $orderitems = array(array('',''));
                $orderid = $_POST['orderid'];
                unset($_POST['orderid']);
                unset($orderitems[0]);
                
                $order_result = null;
    
                $list = array_keys($_POST);       
    
                $idx = 0;
                  foreach($list  as $key => $value ){
    
                    $id  = preg_replace('/\D/','',$value); 
                    if(!empty($value)){
                        if($idx == 0){
                            if(!empty($_POST[$value])){
                                $orderitems[$idx] = array($_POST[$value], $_POST['qty'.$id]);
                            }                
                        }       
                        if($key%2 == 0 ){//odd
                            if(!empty($_POST[$value])){
                                $orderitems[$idx] = array($_POST[$value], $_POST['qty'.$id]);
                            }
                        }
                    }               
                   $idx ++;                
                  }            
                if(count($orderitems) > 0){
                    $order = $objectManager->create('\Magento\Sales\Model\Order')->load($orderid);
                    if($order->getStatus() == 'pending'){
                        if($delivery_date == null){
                            $order_result = $mageorder->updateOrder($order, $orderitems, $order->getData('DELIVERY_DATE'), $order->getCustomerId(), 0);
                            $order_result = $mageorder->updateOrder($order, $orderitems, $order->getData('DELIVERY_DATE'), $order->getCustomerId(), 1);
                            echo $order_result;
                        }else {
                            $order_result = $mageorder->updateOrder($order, $orderitems, $delivery_date, $order->getCustomerId(), 0);
                            $order_result = $mageorder->updateOrder($order, $orderitems, $delivery_date, $order->getCustomerId(), 1);
                            echo $order_result;
                        }
                    }else {
                        echo 'edit_locked';
                    }                
                }else {
                    echo 'no_items_set';
                }
            }else {
                echo 'no_items_set';
            }
            
        }elseif(isset($_POST['cancel'])){
            $ordernumber = $_POST['ordernumber'];
            unset($_POST['ordernumber']);
            $result = $mageorder->cancel_order($ordernumber);
            echo $result;
        } 
        if(isset($_POST['delivery_date']) && isset($_POST['quote_number'])){
            $quote = $objectManager->create('\Magento\Quote\Model\QuoteFactory')->load($_POST['quote_number']);
            $quote->setData("DELIVERY_DATE", $_POST['delivery_date']);
            $quote->save();
            echo 1;
        }
        if(isset($_POST['order_status'])){
            $ordernumber = $_POST['mage_order_number'];
            $order = $objectManager->create('\Magento\Sales\Model\Order')->load($ordernumber);
            $realOrderId = $order->getRealOrderId();
            $ecc_status = $mageorder->get_ecc_order_status($realOrderId);
            print_r($ecc_status);
        }       
    }
}
