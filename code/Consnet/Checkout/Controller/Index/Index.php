<?php
namespace Consnet\Checkout\Controller\Index;

class Index extends \Magento\Framework\App\Action\Action
{
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
        $this->resultPageFactory = $resultPageFactory;
        $this->jsonHelper = $jsonHelper;
        parent::__construct($context);
    }

    /**
     * Execute view action
     */
    public function execute()
    {
        //$postdata = json_decode(file_get_contents("php://input"));
        //var_dump($_POST);     
        if(isset($_POST['quote_id'])){
            $om = \Magento\Framework\App\ObjectManager::getInstance();
            $companymanagement = $om->create('Magento\Checkout\Model\Session\SuccessValidator');

            $quote = $om->create('\Magento\Quote\Model\Quote')->load($_POST['quote_id']);

            $companymanagement = $om->create('Magento\Company\Model\CompanyManagement');
            
            $companydata = $companymanagement->getByCustomerId($quote->getCustomerId())->getData();
            //var_dump($companydata);die;
            $addressdata = [
                'shipping_address' =>[
                    'firstname'    => $companydata['company_name'], //address Details
                    'lastname'     => $companydata['legal_name'],
                            'street' => $companydata['legal_name'],
                            'city' => $companydata['legal_name'],
                    'country_id' => $companydata['country_id'],
                    'region' => $companydata['region'],
                    'postcode' => $companydata['postcode'],
                    'telephone' => $companydata['telephone'],
                    //'fax' => $companydata['fax'],
                    'save_in_address_book' => 0
                    ]
            ];

            //Set Address to quote
            $quote->getShippingAddress()->setShippingMethod('freeshipping_freeshipping');
            $quote->getShippingAddress()->setFreeShipping(true);
            $quote->getBillingAddress()->addData($addressdata['shipping_address']);
            $quote->getShippingAddress()->addData($addressdata['shipping_address']);

            $quote->getShippingAddress()->setShippingMethod('freeshipping_freeshipping');
            $quote->getShippingAddress()->setFreeShipping(true);

            //  Net
            $address = $quote->getShippingAddress(); 
            $addressValidation = $address->validate(); 
            /*if ($addressValidation !== true) { 
                Mage::throwException( Mage::helper('sales')->__('Please check shipping address information. %s', implode(' ', $addressValidation)) ); 
            } */
            $method= $address->getShippingMethod(); 
            $rate = $address->getShippingRateByCode($method);

            $quote->getShippingAddress()->setCollectShippingRates(true); 
            $quote->setTotalsCollectedFlag(false);
            $quote->collectTotals(); 
            $rate = $address->getShippingRateByCode($method);
            //  Net

            // Set Sales Order Payment
            $quote->getPayment()->importData(['method' => 'purchaseorder']);
            //$quote->setPaymentMethod('checkmo');
            
            if (isset($_POST['delivery_date'])) {
                $date = date_create($_POST['delivery_date']);
                //date_modify($date, '+1 day');
                $formatedDate = date_format($date, 'dmY');
            }
            else {
                $date = date_create("2017-11-21");
                //date_modify($date, '+1 day');
                $formatedDate = date_format($date, 'dmY');
            }            
            
            $quote->setData("DELIVERY_DATE",$formatedDate);
            
            $quote->getPayment()->setPoNumber($_POST['po_number']);
            $quote->setData("po_number",$_POST['po_number']);
            //$_SESSION["po_number"] = $_POST['po_number'];
            $quote->save();
            // Collect Totals & Save Quote
            $quote->collectTotals()->save();

            //$quote_payment_method = $quote->getPayment();
            //$quote_payment_method
            
            // Create Order From Quote
            $quoteManagement = $om->create('\Magento\Quote\Model\QuoteManagement');
             
            $neworder = $quoteManagement->submit($quote);
            //$neworder->setStatus(\Magento\Sales\Model\Order::STATE_NEW);
            $neworder->setData("DELIVERY_DATE",$formatedDate);
            $neworder->setPoNumber($_POST['po_number']);
            $neworder->setData("po_number",$_POST['po_number']);
            //$neworder->setStatus(\Magento\Sales\Model\Order::STATE_PENDING);
            //$neworder->setState(\Magento\Sales\Model\Order::STATE_PENDING);
            $neworder->save();

            /*$LOCATION  = $this->url->getUrl('sales/order/view/order_id/'.$edited_order->getId());
            unset($_SESSION['CURRENT_ORDER']);
            $this->responseFactory->create()->setRedirect($LOCATION)->sendResponse();    */ 
            
            //$session = $om->create('Magento\Checkout\Model\Session\SuccessValidator');
            
            //$session = $this->getOnepage()->getCheckout(); 
            /*if (!$this->_objectManager->get('Magento\Checkout\Model\Session\SuccessValidator')->isValid()) { 
                return $this->resultRedirectFactory->create()->setPath('checkout/cart'); 
            } */
            //$session->clearQuote(); 

            //@todo: Refactor it to match CQRS 
            
            return $this->resultRedirectFactory->create()->setPath("sales/order/view/order_id/".$neworder->getId()."/"); 
            
            /*$resultPage = $this->resultPageFactory->create(); 
            $this->_eventManager->dispatch( 
                'checkout_onepage_controller_success_action', 
                ['order_ids' => [$neworder->getId()]] ); 
            return $resultPage;*/
            /*$resultPage = $this->resultPageFactory->create();
            $resultPage->setPath("sales/order/view/order_id/".$neworder->getId()."/");
            return $resultPage;*/
        }else {
            return $this->resultRedirectFactory->create()->setPath("checkout/cart/"); 
        }
        //echo "here";
        /*$dispatchmanager = $om->create('\Magento\Quote\Model\QuoteManagement');
        
        $resultPage = $this->resultPageFactory->create();
        $resultPage->setPath("checkout/onepage/success");
        return $resultPage; */
    }
}
